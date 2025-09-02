<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Version;
use App\Models\Quotation;
use App\Models\InternalSummary;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;


class QuotationController extends Controller
{
    public function preview($versionId)
    {
        $version = Version::with(['project.customer', 'solution_type'])->findOrFail($versionId);

        // Create a real quotation row if not exists (so Solutions list won’t be empty)
        $quotation = Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                // If Quotation model has booted() that fills id & quote_code, you can omit the next 2 lines.
                // 'id'        => (string) Str::ulid(), // or Str::uuid()
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            ]
        );

        // No more fallback — we now have a real Quotation row
        $quotationId = $quotation->id;

        // (Optional) If you are not storing contract_duration in DB yet, read from session for display
        $quotation->contract_duration = session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12);

        // ---- Internal summary & totals (your existing logic) ----
        /*$internal = InternalSummary::where('version_id', $versionId)->first();
        
        // ---- Managed Services (KL/CJ) ----
[$managedSummary, $totalManagedCharges] = $this->computeManagedSummary($version);


        // ECS (per-flavour, per-region) -> untuk quotation totals
[$ecsSummary, $klEcsTotal, $cjEcsTotal] = $this->computeEcsSummary($version);




        // ---- Network totals (KL & CJ) ----
$pricing = config('pricing');




[$licenseRateCard, $totalLicenseCharges] = $this->computeLicenseRateCard($internal, $pricing);

if (!$internal) {
    $internal = new \App\Models\InternalSummary(); // fallback kosong
}

[$klTotal, $cjTotal] = $this->computeNetworkTotals($internal, $pricing);


      [$psDays, $psUnit, $totalProfessionalCharges] = $this->computeProfessionalTotals($internal);

       









$licenseSummary  = [];
$securitySummary = $monitoringSummary = $cloudSecuritySummary = $storageSummary = $backupSummary = [];
$totalManagedCharges = $totalManagedCharges; // keep computed
$totalSecurityCharges = $totalMonitoringCharges = $totalcloudSecurityCharges = 0;
$totalStorageCharges  = $totalBackupCharges = 0;*/







// ---- Internal summary ----
$internal = InternalSummary::where('version_id', $versionId)->first();
if (!$internal) {
    $internal = new InternalSummary(); // fallback kosong
}

// ---- Pricing ----
$pricing = config('pricing');

// ---- Managed ----
[$managedSummary, $totalManagedCharges] = $this->computeManagedSummary($version);

// ---- Licenses (guna $internal & $pricing yang dah ready) ----
[$licenseRateCard, $totalLicenseCharges] = $this->computeLicenseRateCard($internal, $pricing);

// ---- ECS ----
[$ecsSummary, $klEcsTotal, $cjEcsTotal] = $this->computeEcsSummary($version);

// ---- Network ----
[$klTotal, $cjTotal] = $this->computeNetworkTotals($internal, $pricing);

// ---- Professional ----
[$psDays, $psUnit, $totalProfessionalCharges] = $this->computeProfessionalTotals($internal);

[$storageSummary, $totalStorageCharges] = $this->computeStorageSummary($internal, $pricing);
[$cloudSecuritySummary, $totalcloudSecurityCharges] = $this->computeCloudSecuritySummary($internal, $pricing);
[$monitoringSummary, $totalMonitoringCharges] = $this->computeMonitoringSummary($internal, $pricing);
[$securitySummary, $totalSecurityCharges] = $this->computeSecuritySummary($internal, $pricing);
[$backupSummary, $totalBackupCharges] = $this->computeBackupSummary($internal, $pricing);

$licenseSummary = [];

// === Kira & SIMPAN Final Total ke DB (quotations.total_amount) ===
$licenseKL = collect($licenseRateCard)->sum('kl_price');
$licenseCJ = collect($licenseRateCard)->sum('cj_price');

$klEcsTotal = collect($ecsSummary)->sum('kl_price');
$cjEcsTotal = collect($ecsSummary)->sum('cj_price');

$monthlyTotal =
    ($totalManagedCharges ?? 0) +
    (($klTotal ?? 0) + ($cjTotal ?? 0)) +
    (($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) +
    ($totalLicenseCharges ?? ($licenseKL + $licenseCJ)) +
    ($totalStorageCharges ?? 0) +
    ($totalBackupCharges ?? 0) +
    ($totalcloudSecurityCharges ?? 0) +
    ($totalMonitoringCharges ?? 0) +
    ($totalSecurityCharges ?? 0);

// Duration ikut dropdown (simbolik sama macam Blade)
$duration = (int) session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12);

$contractTotal = ($monthlyTotal * $duration) + ($totalProfessionalCharges ?? 0);
$serviceTax    = $contractTotal * 0.08;
$finalTotal    = $contractTotal + $serviceTax;

// SIMPAN dalam DB
$quotation->total_amount = round($finalTotal, 2);
// kalau kau ADA kolum contract_duration dalam table quotations, boleh juga simpan:
// $quotation->contract_duration = $duration;
$quotation->save();




        return view('projects.security_service.quotation', compact(
            'version','quotation','quotationId','totalProfessionalCharges',
            'klTotal','cjTotal','managedSummary','totalManagedCharges',
            'ecsSummary','licenseRateCard','licenseSummary',
            
            'securitySummary','totalSecurityCharges',
            'monitoringSummary','totalMonitoringCharges',
            'cloudSecuritySummary','totalcloudSecurityCharges',
            'storageSummary','totalStorageCharges',
            'backupSummary','totalBackupCharges',
            'psDays','psUnit', 'totalLicenseCharges',
        ) + ['project'=>$version->project, 'mode'=>'monthly', 'viewOnly'=>0]);
    }



private function computeProfessionalTotals($s)

{
    
    $unit = (float) data_get(config('pricing'), 'CPFS-PFS-MDY-5OTC.price_per_unit', 1200);
    // Mandays ambil dari internal_summaries.mandays
    $days = (int) ($s->mandays ?? 0);
    $total = $days * $unit;
    return [$days, $unit, $total];
}






    private function computeNetworkTotals($s, $pricing)
{
    $kl = 0.0; $cj = 0.0;

    $getPrice = function($key) use ($pricing) {
        return (float) data_get($pricing, "$key.price_per_unit", 0);
    };

    // Bandwidth (tiered)
    if (!empty($s->kl_bandwidth)) {
        $kl += $s->kl_bandwidth * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth));
    }
    if (!empty($s->cyber_bandwidth)) {
        $cj += $s->cyber_bandwidth * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth));
    }
    // Bandwidth + Anti-DDoS (tiered)
    if (!empty($s->kl_bandwidth_with_antiddos)) {
        $kl += $s->kl_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth_with_antiddos));
    }
    if (!empty($s->cyber_bandwidth_with_antiddos)) {
        $cj += $s->cyber_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth_with_antiddos));
    }

    // Item “flat” ikut key pricing
    $map = [
        // Elastic IP
        'kl_included_elastic_ip'    => ['CNET-EIP-SHR-FOC','KL'],
        'cyber_included_elastic_ip' => ['CNET-EIP-SHR-FOC','CJ'],
        'kl_elastic_ip'             => ['CNET-EIP-SHR-STD','KL'],
        'cyber_elastic_ip'          => ['CNET-EIP-SHR-STD','CJ'],

        // Elastic Load Balancer
        'kl_elastic_load_balancer'    => ['CNET-ELB-SHR-STD','KL'],
        'cyber_elastic_load_balancer' => ['CNET-ELB-SHR-STD','CJ'],

        // Direct Connect VGW
        'kl_direct_connect_virtual'    => ['CNET-DGW-SHR-EXT','KL'],
        'cyber_direct_connect_virtual' => ['CNET-DGW-SHR-EXT','CJ'],

        // L2BR instance & vPLL L2BR
        'kl_l2br_instance'    => ['CNET-L2BR-SHR-EXT','KL'],
        'cyber_l2br_instance' => ['CNET-L2BR-SHR-EXT','CJ'],
        'kl_vpll_l2br'        => ['CNET-L2BR-SHR-INT','KL'],
        'cyber_vpll_l2br'     => ['CNET-L2BR-SHR-INT','CJ'],

        // NAT Gateway
        'kl_nat_gateway_small'     => ['CNET-NAT-SHR-S','KL'],
        'kl_nat_gateway_medium'    => ['CNET-NAT-SHR-M','KL'],
        'kl_nat_gateway_large'     => ['CNET-NAT-SHR-L','KL'],
        'kl_nat_gateway_xlarge'    => ['CNET-NAT-SHR-XL','KL'],
        'cyber_nat_gateway_small'  => ['CNET-NAT-SHR-S','CJ'],
        'cyber_nat_gateway_medium' => ['CNET-NAT-SHR-M','CJ'],
        'cyber_nat_gateway_large'  => ['CNET-NAT-SHR-L','CJ'],
        'cyber_nat_gateway_xlarge' => ['CNET-NAT-SHR-XL','CJ'],

        // GSLB
        'kl_gslb'    => ['CNET-GLB-SHR-DOMAIN','KL'],
        'cyber_gslb' => ['CNET-GLB-SHR-DOMAIN','CJ'],
    ];

    foreach ($map as $field => [$priceKey, $region]) {
        $qty = (float) ($s->$field ?? 0);
        if ($qty > 0) {
            $line = $qty * $getPrice($priceKey);
            if ($region === 'KL') $kl += $line; else $cj += $line;
        }
    }

    // vPLL (tiered)
    if (!empty($s->kl_virtual_private_leased_line)) {
        $kl += $s->kl_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->kl_virtual_private_leased_line));
    }
    if (!empty($s->cyber_virtual_private_leased_line)) {
        $cj += $s->cyber_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->cyber_virtual_private_leased_line));
    }

    return [round($kl, 2), round($cj, 2)];
}

private function getBandwidthPriceKey($mbps)
{
    if ($mbps <= 10) return 'CNET-BWS-CIA-10';
    if ($mbps <= 30) return 'CNET-BWS-CIA-30';
    if ($mbps <= 50) return 'CNET-BWS-CIA-50';
    if ($mbps <= 80) return 'CNET-BWS-CIA-80';
    return 'CNET-BWS-CIA-100';
}

private function getVPLLPriceKey($mbps)
{
    if ($mbps <= 10) return 'CNET-PLL-SHR-10';
    if ($mbps <= 30) return 'CNET-PLL-SHR-30';
    if ($mbps <= 50) return 'CNET-PLL-SHR-50';
    if ($mbps <= 80) return 'CNET-PLL-SHR-80';
    return 'CNET-PLL-SHR-100';
}


    

    public function annual($versionId)
{
    $view = $this->preview($versionId);
    $data = $view->getData();
    $data['mode'] = 'annual';
    return view('projects.security_service.annual_quotation', $data);
}






 /**
 * Managed Services summary & total untuk quotation.
 * Ambil kira 4 slot: kl_managed_services_1..4 / cyber_managed_services_1..4
 * dan harga ikut config('pricing') (category_name = 'Managed Services').
 */
private function computeManagedSummary(\App\Models\Version $version): array
{
    $svc = $version->security_service;
    if (!$svc) {
        return [[], 0.0];
    }

    // Senarai servis yang kita support
    $services = [
        'Managed Operating System',
        'Managed Backup and Restore',
        'Managed Patching',
        'Managed DR',
    ];

    // Kira qty per region
    $counts = [];
    foreach ($services as $s) {
        $counts[$s] = ['kl_qty' => 0, 'cj_qty' => 0];
    }

    foreach (range(1, 4) as $i) {
        $v = $svc->{'kl_managed_services_' . $i} ?? null;
        if ($v && in_array($v, $services, true)) $counts[$v]['kl_qty']++;
    }
    foreach (range(1, 4) as $i) {
        $v = $svc->{'cyber_managed_services_' . $i} ?? null;
        if ($v && in_array($v, $services, true)) $counts[$v]['cj_qty']++;
    }

    // Lookup harga ikut nama dari config('pricing')
    [$priceByName, $unitByName] = $this->lookupManagedServicePrices();

    $summary = [];
    $grand = 0.0;

    foreach ($services as $name) {
        $unit  = $unitByName[strtolower($name)] ?? 'VM';
        $price = (float) ($priceByName[strtolower($name)] ?? 0);

        $klPrice = $counts[$name]['kl_qty'] * $price;
        $cjPrice = $counts[$name]['cj_qty'] * $price;

        if (($counts[$name]['kl_qty'] + $counts[$name]['cj_qty']) > 0) {
            $summary[] = [
                'name'            => $name,
                'unit'            => $unit,
                'price_per_unit'  => $price,
                'kl_qty'          => $counts[$name]['kl_qty'],
                'cj_qty'          => $counts[$name]['cj_qty'],
                'kl_price'        => $klPrice,
                'cj_price'        => $cjPrice,
            ];
        }
        $grand += $klPrice + $cjPrice;
    }

    return [$summary, round($grand, 2)];
}

private function lookupManagedServicePrices(): array
{
    $pricing = config('pricing');
    $priceByName = [];
    $unitByName  = [];

    foreach ($pricing as $code => $item) {
        if (!is_array($item)) continue;
        $cat  = strtolower((string)($item['category_name'] ?? ''));
        $name = strtolower(trim((string)($item['name'] ?? '')));
        if ($name === '') continue;

        // Utama: ikut kategori "Managed Services"
        if ($cat === 'managed services') {
            $priceByName[$name] = (float)($item['price_per_unit'] ?? 0);
            $unitByName[$name]  = (string)($item['measurement_unit'] ?? 'VM');
        }
    }

    // Fallback: kalau kategori dalam config tak sama, tetap match ikut nama
    if (empty($priceByName)) {
        foreach ($pricing as $code => $item) {
            if (!is_array($item)) continue;
            $name = strtolower(trim((string)($item['name'] ?? '')));
            if (in_array($name, [
                'managed operating system',
                'managed backup and restore',
                'managed patching',
                'managed dr',
            ], true)) {
                $priceByName[$name] = (float)($item['price_per_unit'] ?? 0);
                $unitByName[$name]  = (string)($item['measurement_unit'] ?? 'VM');
            }
        }
    }

    return [$priceByName, $unitByName];
}



private function computeLicenseRateCard($summary, array $pricing): array
{
    $rows = [];
    $grand = 0.0;

    $map = [
        'Microsoft Windows Server (Core Pack) - Standard'    => ['kl'=>'kl_windows_std',  'cj'=>'cyber_windows_std',  'key'=>'CLIC-WIN-COR-SRVSTD'],
        'Microsoft Windows Server (Core Pack) - Data Center' => ['kl'=>'kl_windows_dc',   'cj'=>'cyber_windows_dc',   'key'=>'CLIC-WIN-COR-SRVDC'],
        'Microsoft Remote Desktop Services (SAL)'            => ['kl'=>'kl_rds',          'cj'=>'cyber_rds',          'key'=>'CLIC-WIN-USR-RDSSAL'],
        'Microsoft SQL (Web) (Core Pack)'                    => ['kl'=>'kl_sql_web',      'cj'=>'cyber_sql_web',      'key'=>'CLIC-WIN-COR-SQLWEB'],
        'Microsoft SQL (Standard) (Core Pack)'               => ['kl'=>'kl_sql_std',      'cj'=>'cyber_sql_std',      'key'=>'CLIC-WIN-COR-SQLSTD'],
        'Microsoft SQL (Enterprise) (Core Pack)'             => ['kl'=>'kl_sql_ent',      'cj'=>'cyber_sql_ent',      'key'=>'CLIC-WIN-COR-SQLENT'],
        'RHEL (1-8vCPU)'                                     => ['kl'=>'kl_rhel_1_8',     'cj'=>'cyber_rhel_1_8',     'key'=>'CLIC-RHL-COR-8'],
        'RHEL (9-127vCPU)'                                   => ['kl'=>'kl_rhel_9_127',   'cj'=>'cyber_rhel_9_127',   'key'=>'CLIC-RHL-COR-127'],
    ];

    foreach ($map as $label => $def) {
        $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);
        $klQty = (int)($summary->{$def['kl']} ?? 0);
        $cjQty = (int)($summary->{$def['cj']} ?? 0);

        if ($klQty > 0 || $cjQty > 0) {
            $row = [
                'name'           => $label,
                'unit'           => 'Unit',
                'price_per_unit' => $price,
                'kl_qty'         => $klQty,
                'cj_qty'         => $cjQty,
                'kl_price'       => $klQty * $price,
                'cj_price'       => $cjQty * $price,
            ];
            $rows[] = $row;
            $grand += $row['kl_price'] + $row['cj_price'];
        }
    }

    return [$rows, round($grand, 2)];
}


private function computeStorageSummary($s, array $pricing): array
{
    if (!$s) return [[], 0.0];

    $rows = [];
    $grand = 0.0;

    // label => [KL field, CJ field, pricing key, unit]
    $map = [
        'Elastic Volume Service (EVS)' => [
            'kl'=>'kl_evs', 'cj'=>'cyber_evs', 'key'=>'CSTG-EVS-SHR-STD', 'unit'=>'GB',
        ],
        'Scalable File Service (SFS)' => [
            'kl'=>'kl_scalable_file_service', 'cj'=>'cyber_scalable_file_service', 'key'=>'CSTG-SFS-SHR-STD', 'unit'=>'GB',
        ],
        'Object Storage Service (OBS)' => [
            'kl'=>'kl_object_storage_service', 'cj'=>'cyber_object_storage_service', 'key'=>'CSTG-OBS-SHR-STD', 'unit'=>'GB',
        ],
        'Snapshot Storage' => [
            'kl'=>'kl_snapshot_storage', 'cj'=>'cyber_snapshot_storage', 'key'=>'CSTG-BCK-SHR-STD', 'unit'=>'GB',
        ],
        'Image Storage' => [
            'kl'=>'kl_image_storage', 'cj'=>'cyber_image_storage', 'key'=>'CSTG-OBS-SHR-IMG', 'unit'=>'GB',
        ],
    ];

    foreach ($map as $label => $def) {
        $unit  = $def['unit'];
        $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);

        $klQty = (float)($s->{$def['kl']} ?? 0);
        $cjQty = (float)($s->{$def['cj']} ?? 0);

        if ($klQty > 0 || $cjQty > 0) {
            $row = [
                'name'           => $label,
                'unit'           => $unit,
                'price_per_unit' => $price,
                'kl_qty'         => $klQty,
                'cj_qty'         => $cjQty,
                'kl_price'       => $klQty * $price,
                'cj_price'       => $cjQty * $price,
            ];
            $rows[] = $row;
            $grand += $row['kl_price'] + $row['cj_price'];
        }
    }

    return [$rows, round($grand, 2)];
}


private function computeCloudSecuritySummary($s, array $pricing): array
{
    if (!$s) return [[], 0.0];

    $rows = [];
    $grand = 0.0;

    // label => [KL field, CJ field, pricing key, unit]
    $map = [
        'Cloud Firewall (Fortigate)' => [
            'kl' => 'kl_firewall_fortigate', 'cj' => 'cyber_firewall_fortigate',
            'key' => 'CSEC-VFW-DDT-FG', 'unit' => 'Unit',
        ],
        'Cloud Firewall (OPNSense)' => [
            'kl' => 'kl_firewall_opnsense', 'cj' => 'cyber_firewall_opnsense',
            'key' => 'CSEC-VFW-DDT-OS', 'unit' => 'Unit',
        ],
        'Cloud Shared WAF (Mbps)' => [
            'kl' => 'kl_shared_waf', 'cj' => 'cyber_shared_waf',
            'key' => 'CSEC-WAF-SHR-HA', 'unit' => 'Mbps',
        ],
        'Anti-Virus (Panda)' => [
            'kl' => 'kl_antivirus', 'cj' => 'cyber_antivirus',
            'key' => 'CSEC-EDR-NOD-STD', 'unit' => 'Unit',
        ],
    ];

    foreach ($map as $label => $def) {
        $unit  = $def['unit'];
        $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);

        $klQty = (float)($s->{$def['kl']} ?? 0);
        $cjQty = (float)($s->{$def['cj']} ?? 0);

        if ($klQty > 0 || $cjQty > 0) {
            $row = [
                'name'           => $label,
                'unit'           => $unit,
                'price_per_unit' => $price,
                'kl_qty'         => $klQty,
                'cj_qty'         => $cjQty,
                'kl_price'       => $klQty * $price,
                'cj_price'       => $cjQty * $price,
            ];
            $rows[] = $row;
            $grand += $row['kl_price'] + $row['cj_price'];
        }
    }

    return [$rows, round($grand, 2)];
}


private function computeMonitoringSummary($s, array $pricing): array
{
    if (!$s) return [[], 0.0];

    $price = (float)($pricing['CMON-TIS-NOD-STD']['price_per_unit'] ?? 0); // RM 40.00
    $klQty = (int)($s->kl_insight_vmonitoring ?? 0);
    $cjQty = (int)($s->cyber_insight_vmonitoring ?? 0);

    $rows = [];
    if ($klQty > 0 || $cjQty > 0) {
        $rows[] = [
            'name'           => 'TCS inSight vMonitoring',
            'unit'           => 'Unit',
            'price_per_unit' => $price,
            'kl_qty'         => $klQty,
            'cj_qty'         => $cjQty,
            'kl_price'       => $klQty * $price,
            'cj_price'       => $cjQty * $price,
        ];
    }

    $grand = collect($rows)->sum(fn($r) => $r['kl_price'] + $r['cj_price']);
    return [$rows, round($grand, 2)];
}


private function computeSecuritySummary($s, array $pricing): array
{
    if (!$s) return [[], 0.0];

    // Harga ikut pricing.php
    $price = (float)($pricing['SECT-VAS-EIP-STD']['price_per_unit'] ?? 0); // RM 80.00

    // Qty dari internal_summaries
    $klQty = (int)($s->kl_cloud_vulnerability ?? 0);
    $cjQty = (int)($s->cyber_cloud_vulnerability ?? 0);

    $rows = [];
    if ($klQty > 0 || $cjQty > 0) {
        $rows[] = [
            'name'           => 'Cloud Vulnerability Assessment (Per IP)',
            'unit'           => 'Unit',
            'price_per_unit' => $price,
            'kl_qty'         => $klQty,
            'cj_qty'         => $cjQty,
            'kl_price'       => $klQty * $price,
            'cj_price'       => $cjQty * $price,
        ];
    }

    $grand = 0.0;
    foreach ($rows as $r) { $grand += ($r['kl_price'] + $r['cj_price']); }

    return [$rows, round($grand, 2)];
}


private function computeBackupSummary($s, array $pricing): array
{
    if (!$s) return [[], 0.0];

    $rows = [];
    $grand = 0.0;

    // label => [KL field, CJ field, pricing key, unit]
    $map = [
        'Cloud Server Backup Service - Full Backup Capacity' => [
            'kl' => 'kl_full_backup_capacity',
            'cj' => 'cyber_full_backup_capacity',
            'key' => 'CSBS-STRG-BCK-CSBSF',
            'unit' => 'GB',
        ],
        'Cloud Server Backup Service - Incremental Backup Capacity' => [
            'kl' => 'kl_incremental_backup_capacity',
            'cj' => 'cyber_incremental_backup_capacity',
            'key' => 'CSBS-STRG-BCK-CSBSI',
            'unit' => 'GB',
        ],
        'Cloud Server Replication Service - Retention Capacity' => [
            'kl' => 'kl_replication_retention_capacity',
            'cj' => 'cyber_replication_retention_capacity',
            'key' => 'CSBS-STRG-BCK-REPS',
            'unit' => 'GB',
        ],
    ];

    foreach ($map as $label => $def) {
        $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0); // RM 0.20
        $unit  = $def['unit'];

        $klQty = (float)($s->{$def['kl']} ?? 0);
        $cjQty = (float)($s->{$def['cj']} ?? 0);

        if ($klQty > 0 || $cjQty > 0) {
            $row = [
                'name'           => $label,
                'unit'           => $unit,
                'price_per_unit' => $price,
                'kl_qty'         => $klQty,
                'cj_qty'         => $cjQty,
                'kl_price'       => round($klQty * $price, 2),
                'cj_price'       => round($cjQty * $price, 2),
            ];
            $rows[] = $row;
            $grand += $row['kl_price'] + $row['cj_price'];
        }
    }

    return [$rows, round($grand, 2)];
}

private function computeEcsSummary(\App\Models\Version $version): array
{
    // Dapatkan semua row ECS untuk version ni
    $rows = $version->ecs_configuration ?? collect();
    if (!($rows instanceof \Illuminate\Support\Collection)) {
        $rows = collect($rows);
    }

    // Build lookup: flavour name -> pricing info (price_per_unit, unit)
    [$nameToPrice, $nameToUnit] = $this->buildEcsPricingLookups();

    // summary keyed by flavour
    $summary = [];

    foreach ($rows as $r) {
        $flavour = trim((string) $r->ecs_flavour_mapping);
        if ($flavour === '') continue;

        $region  = trim((string) $r->region); // 'Kuala Lumpur' atau 'Cyberjaya'
        $price   = (float) ($nameToPrice[strtolower($flavour)] ?? 0);
        $unit    = (string) ($nameToUnit[strtolower($flavour)] ?? 'Unit');

        if (!isset($summary[$flavour])) {
            $summary[$flavour] = [
                'flavour'   => $flavour,
                'unit'      => $unit,
                'unit_price'=> $price,
                'kl_qty'    => 0,
                'cj_qty'    => 0,
                'kl_price'  => 0.0,
                'cj_price'  => 0.0,
            ];
        }

        // Qty setiap baris dikira 1 unit per VM
        if (strcasecmp($region, 'Kuala Lumpur') === 0) {
            $summary[$flavour]['kl_qty']   += 1;
            $summary[$flavour]['kl_price']  = $summary[$flavour]['kl_qty'] * $price;
        } elseif (strcasecmp($region, 'Cyberjaya') === 0) {
            $summary[$flavour]['cj_qty']   += 1;
            $summary[$flavour]['cj_price']  = $summary[$flavour]['cj_qty'] * $price;
        }
    }

    $ecsSummary = array_values($summary);

    $klTotal = collect($ecsSummary)->sum('kl_price');
    $cjTotal = collect($ecsSummary)->sum('cj_price');

    return [$ecsSummary, $klTotal, $cjTotal];
}



private function buildEcsPricingLookups(): array
{
    $pricing = config('pricing');
    $nameToPrice = [];
    $nameToUnit  = [];

    foreach ($pricing as $key => $item) {
        // Ambil hanya item compute ECS (key biasanya bermula 'CMPT-ECS-')
        if (is_array($item) && is_string($key) && str_starts_with($key, 'CMPT-ECS-')) {
            $name = strtolower(trim((string) ($item['name'] ?? '')));
            if ($name !== '') {
                $nameToPrice[$name] = (float) ($item['price_per_unit'] ?? 0);
                $nameToUnit[$name]  = (string) ($item['measurement_unit'] ?? 'Unit');
            }
        }
    }
    return [$nameToPrice, $nameToUnit];
}


    public function generateQuotation($versionId)
    {
        return $this->preview($versionId);
    }

    public function autoSave(Request $request, $versionId)
    {
        // If you have NOT added contract_duration column yet, keep using session:
        if ($request->field === 'contract_duration') {
            session()->put("quotation.$versionId.contract_duration", (int) $request->value);
        }

       

        return response()->json(['success' => true]);
    }






public function internalSummaryPdf($versionId)
{
    $version = Version::with([
        'project.customer',
        'project.presale',
        'solution_type',
        'ecs_configuration',
        'security_service',
        'non_standard_items',
    ])->findOrFail($versionId);

    // Snapshot (kalau belum commit, akan kosong -> view handle default 0)
    $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();

    // Logo (ikut pattern generateQuotation/downloadZip kau)
    $logoPath   = public_path('assets/time_logo.png');
    $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

    $data = [
        'version'   => $version,
        'project'   => $version->project,
        'internal'  => $internal,   // dalam blade aku pakai $internal / $summary
        'logoBase64'=> $logoBase64,
        'logoPath'  => $logoPath,
    ];

    $fileName = sprintf(
        'internal_summary_%s_v%s.pdf',
        $version->project->project_code ?? Str::slug($version->project->name ?? 'project'),
        $version->version_number ?? '1'
    );

    $pdf = Pdf::loadView('pdf.internal-summary', $data)
        ->setPaper('a4', 'landscape') // tukar ke 'portrait' kalau suka
        ->setOptions(['defaultFont' => 'sans-serif']);

    return $pdf->download($fileName);
}


public function downloadZip(Request $request, $versionId)
{
    $tmpDir = null;

    try {
        // 1) LOAD DATA ASAS
        $version = Version::with([
            'project.customer', 'solution_type', 'ecs_configuration', 'security_service'
        ])->findOrFail($versionId);

        $quotation = Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
            ]
        );

        $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();
        $pricing  = config('pricing');

        // 2) KIRA SEMUA SUMMARY
        [$managedSummary, $totalManagedCharges]             = $this->computeManagedSummary($version);
        [$licenseRateCard, $totalLicenseCharges]            = $this->computeLicenseRateCard($internal, $pricing);
        [$ecsSummary, $klEcsTotal, $cjEcsTotal]             = $this->computeEcsSummary($version);
        [$klTotal, $cjTotal]                                = $this->computeNetworkTotals($internal, $pricing);
        [$psDays, $psUnit, $totalProfessionalCharges]       = $this->computeProfessionalTotals($internal);
        [$storageSummary, $totalStorageCharges]             = $this->computeStorageSummary($internal, $pricing);
        [$cloudSecuritySummary, $totalcloudSecurityCharges] = $this->computeCloudSecuritySummary($internal, $pricing);
        [$monitoringSummary, $totalMonitoringCharges]       = $this->computeMonitoringSummary($internal, $pricing);
        [$securitySummary, $totalSecurityCharges]           = $this->computeSecuritySummary($internal, $pricing);
        [$backupSummary, $totalBackupCharges]               = $this->computeBackupSummary($internal, $pricing);

        // 3) DURATION
        $durationMonthly = (int) (session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12));
        $durationAnnual  = 12;

        // 4) LOGO
        $logoPath   = public_path('assets/time_logo.png');
        $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

        // 5) DATA COMMON UNTUK BLADE
        $common = [
            'version'                   => $version,
            'quotation'                 => $quotation,
            'internal'                  => $internal,
            'project'                   => $version->project,

            'viewOnly'                  => 1,
            'isPdf'                     => 1,

            'managedSummary'            => $managedSummary,
            'totalManagedCharges'       => $totalManagedCharges,
            'licenseRateCard'           => $licenseRateCard,
            'totalLicenseCharges'       => $totalLicenseCharges,
            'ecsSummary'                => $ecsSummary,
            'klEcsTotal'                => $klEcsTotal,
            'cjEcsTotal'                => $cjEcsTotal,
            'klTotal'                   => $klTotal,
            'cjTotal'                   => $cjTotal,
            'psDays'                    => $psDays,
            'psUnit'                    => $psUnit,
            'totalProfessionalCharges'  => $totalProfessionalCharges,
            'storageSummary'            => $storageSummary,
            'totalStorageCharges'       => $totalStorageCharges,
            'cloudSecuritySummary'      => $cloudSecuritySummary,
            'totalcloudSecurityCharges' => $totalcloudSecurityCharges,
            'monitoringSummary'         => $monitoringSummary,
            'totalMonitoringCharges'    => $totalMonitoringCharges,
            'securitySummary'           => $securitySummary,
            'totalSecurityCharges'      => $totalSecurityCharges,
            'backupSummary'             => $backupSummary,
            'totalBackupCharges'        => $totalBackupCharges,
            
            'logoBase64'                => $logoBase64,
            'logoPath'                  => $logoPath,
        ];

        // 6) RENDER 3 PDF
        $monthlyData = $common + ['mode' => 'monthly', 'contractDuration' => $durationMonthly];
        $annualData  = $common + ['mode' => 'annual',  'contractDuration' => $durationAnnual];

        $pdfMonthly = Pdf::loadView('pdf.quotation-table', $monthlyData)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->output();

        $pdfAnnual = Pdf::loadView('pdf.quotation-table', $annualData)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->output();

        $internalData = [
            'version'   => $version,
            'project'   => $version->project,
            'internal'  => $internal,
            'logoBase64'=> $logoBase64,
            'logoPath'  => $logoPath,
        ];
        $pdfInternal = Pdf::loadView('pdf.internal-summary', $internalData)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->output();

        // 7) SIMPAN FAIL SEMENTARA
        $tmpDir = storage_path('app/tmp/'.Str::uuid());
        File::ensureDirectoryExists($tmpDir);

        $projectCode = $version->project->project_code ?? Str::slug($version->project->name ?? 'project');
        $verNo       = $version->version_number ?? '1';
        $base        = "quotation_{$projectCode}_v{$verNo}";

        $monthlyName  = "{$base}_monthly_{$durationMonthly}m.pdf";
        $annualName   = "{$base}_annual_12m.pdf";
        $internalName = "{$base}_internal_summary.pdf";

        file_put_contents("$tmpDir/$monthlyName",  $pdfMonthly);
        file_put_contents("$tmpDir/$annualName",   $pdfAnnual);
        file_put_contents("$tmpDir/$internalName", $pdfInternal);

        // 8) ZIP
        $zipPath = "$tmpDir/{$base}_bundle.zip";
        $zip = new \ZipArchive();
        $openResult = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            throw new \RuntimeException("Cannot create zip file. Code: {$openResult}");
        }
        $zip->addFile("$tmpDir/$monthlyName",  $monthlyName);
        $zip->addFile("$tmpDir/$annualName",   $annualName);
        $zip->addFile("$tmpDir/$internalName", $internalName);
        $zip->close();

        // 9) HANTAR ZIP & PADAM ZIP LEPAS HANTAR (folder cleanup selepas response)
        $response = response()
            ->download($zipPath, basename($zipPath))
            ->deleteFileAfterSend(true);

        // Cleanup folder sementara selepas response dihantar
        app()->terminating(function () use ($tmpDir) {
            try {
                \Illuminate\Support\Facades\File::deleteDirectory($tmpDir);
            } catch (\Throwable $e) {
                \Log::warning('TMP cleanup failed: '.$e->getMessage());
            }
        });

        return $response;

    } catch (\Throwable $e) {
        // Cleanup segera jika gagal sebelum hantar response
        if ($tmpDir && is_dir($tmpDir)) {
            try { File::deleteDirectory($tmpDir); } catch (\Throwable $ex) {}
        }

        return response()->json([
            'error'   => 'Failed to generate ZIP',
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ], 500);
    }
}


/*public function downloadZip(Request $request, $versionId)
{

    $tmpDir = null;

    try {
        // 1) LOAD DATA ASAS
        $version = Version::with([
            'project.customer', 'solution_type', 'ecs_configuration', 'security_service'
        ])->findOrFail($versionId);

        $quotation = Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
            ]
        );

        $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();
        $pricing  = config('pricing');

        // 2) KIRA SEMUA SUMMARY (guna helper helper sama mcm PDF)
        [$managedSummary, $totalManagedCharges]             = $this->computeManagedSummary($version);
        [$licenseRateCard, $totalLicenseCharges]            = $this->computeLicenseRateCard($internal, $pricing);
        [$ecsSummary, $klEcsTotal, $cjEcsTotal]             = $this->computeEcsSummary($version);
        [$klTotal, $cjTotal]                                = $this->computeNetworkTotals($internal, $pricing);
        [$psDays, $psUnit, $totalProfessionalCharges]       = $this->computeProfessionalTotals($internal);
        [$storageSummary, $totalStorageCharges]             = $this->computeStorageSummary($internal, $pricing);
        [$cloudSecuritySummary, $totalcloudSecurityCharges] = $this->computeCloudSecuritySummary($internal, $pricing);
        [$monitoringSummary, $totalMonitoringCharges]       = $this->computeMonitoringSummary($internal, $pricing);
        [$securitySummary, $totalSecurityCharges]           = $this->computeSecuritySummary($internal, $pricing);
        [$backupSummary, $totalBackupCharges]               = $this->computeBackupSummary($internal, $pricing);

        // 3) DURATION
        $durationMonthly = (int) (session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12));
        $durationAnnual  = 12;

        // 4) LOGO
        $logoPath   = public_path('assets/time_logo.png');
        $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

        // 5) DATA COMMON UNTUK BLADE
        $common = [
            'version'                   => $version,
            'quotation'                 => $quotation,
            'internal'                  => $internal,
            'project'                   => $version->project,

            'viewOnly'                  => 1,
            'isPdf'                     => 1,

            'managedSummary'            => $managedSummary,
            'totalManagedCharges'       => $totalManagedCharges,
            'licenseRateCard'           => $licenseRateCard,
            'totalLicenseCharges'       => $totalLicenseCharges,
            'ecsSummary'                => $ecsSummary,
            'klEcsTotal'                => $klEcsTotal,
            'cjEcsTotal'                => $cjEcsTotal,
            'klTotal'                   => $klTotal,
            'cjTotal'                   => $cjTotal,
            'psDays'                    => $psDays,
            'psUnit'                    => $psUnit,
            'totalProfessionalCharges'  => $totalProfessionalCharges,
            'storageSummary'            => $storageSummary,
            'totalStorageCharges'       => $totalStorageCharges,
            'cloudSecuritySummary'      => $cloudSecuritySummary,
            'totalcloudSecurityCharges' => $totalcloudSecurityCharges,
            'monitoringSummary'         => $monitoringSummary,
            'totalMonitoringCharges'    => $totalMonitoringCharges,
            'securitySummary'           => $securitySummary,
            'totalSecurityCharges'      => $totalSecurityCharges,
            'backupSummary'             => $backupSummary,
            'totalBackupCharges'        => $totalBackupCharges,

            'logoBase64'                => $logoBase64,
            'logoPath'                  => $logoPath,
        ];

        // 6) RENDER 3 PDF: MONTHLY, ANNUAL, INTERNAL SUMMARY
        $monthlyData = $common + ['mode' => 'monthly', 'contractDuration' => $durationMonthly];
        $annualData  = $common + ['mode' => 'annual',  'contractDuration' => $durationAnnual];

        $pdfMonthly = Pdf::loadView('pdf.quotation-table', $monthlyData)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->output();

        $pdfAnnual = Pdf::loadView('pdf.quotation-table', $annualData)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->output();

        // Internal Summary PDF (view: resources/views/pdf/internal-summary.blade.php)
        $internalData = [
            'version'   => $version,
            'project'   => $version->project,
            'internal'  => $internal,
            'logoBase64'=> $logoBase64,
            'logoPath'  => $logoPath,
        ];
        $pdfInternal = Pdf::loadView('pdf.internal-summary', $internalData)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->output();

        // 7) SIMPAN FAIL SEMENTARA
        $tmpDir = storage_path('app/tmp/'.Str::uuid());
        File::ensureDirectoryExists($tmpDir);

        $projectCode = $version->project->project_code ?? Str::slug($version->project->name ?? 'project');
        $verNo       = $version->version_number ?? '1';
        $base        = "quotation_{$projectCode}_v{$verNo}";

        $monthlyName  = "{$base}_monthly_{$durationMonthly}m.pdf";
        $annualName   = "{$base}_annual_12m.pdf";
        $internalName = "{$base}_internal_summary.pdf";

        file_put_contents("$tmpDir/$monthlyName",  $pdfMonthly);
        file_put_contents("$tmpDir/$annualName",   $pdfAnnual);
        file_put_contents("$tmpDir/$internalName", $pdfInternal);

        // 8) ZIP
        $zipPath = "$tmpDir/{$base}_bundle.zip";
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot create zip file.');
        }
        $zip->addFile("$tmpDir/$monthlyName",  $monthlyName);
        $zip->addFile("$tmpDir/$annualName",   $annualName);
        $zip->addFile("$tmpDir/$internalName", $internalName);
        $zip->close();

        // 9) HANTAR & AUTO-DELETE ZIP (folder akan dibersihkan dalam finally)
        return response()->download($zipPath)->deleteFileAfterSend(true);

    } catch (\Throwable $e) {
        return response()->json([
            'error'   => 'Failed to generate ZIP',
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ], 500);
    } finally {
        // Cleanup folder sementara (jika sempat dibuat)
        if ($tmpDir && is_dir($tmpDir)) {
            try { File::deleteDirectory($tmpDir); } catch (\Throwable $ex) {}

        }
    }
}*/


}
