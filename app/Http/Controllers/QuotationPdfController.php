<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Version;
use App\Models\Quotation;
use App\Models\InternalSummary;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationPdfController extends Controller
{
    public function downloadTablePdf(Request $request, $versionId)
    {
        try {
            $mode = $request->query('mode', 'monthly'); // 'monthly' | 'annual'

            // --------- Load core models ----------
            $version = Version::with(['project.customer', 'solution_type', 'ecs_configuration', 'security_service'])->findOrFail($versionId);

            // ensure quotation row exists (sama macam preview)
            $quotation = Quotation::firstOrCreate(
                ['version_id' => $versionId],
                [
                    'project_id' => $version->project_id,
                    'presale_id' => auth()->id(),
                    'status'     => 'pending',
                    'quote_code' => 'Q-' . now()->format('Ymd') . '-' . strtoupper(str()->random(5)),
                ]
            );

            // internal summary
            $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();

            // pricing
            $pricing = config('pricing');

            // --------- Kiraan sama macam QuotationController ---------
            [$managedSummary, $totalManagedCharges] = $this->computeManagedSummary($version);
            [$licenseRateCard, $totalLicenseCharges] = $this->computeLicenseRateCard($internal, $pricing);
            [$ecsSummary, $klEcsTotal, $cjEcsTotal] = $this->computeEcsSummary($version);
            [$klTotal, $cjTotal] = $this->computeNetworkTotals($internal, $pricing);
            [$psDays, $psUnit, $totalProfessionalCharges] = $this->computeProfessionalTotals($internal);
            [$storageSummary, $totalStorageCharges] = $this->computeStorageSummary($internal, $pricing);
            [$cloudSecuritySummary, $totalcloudSecurityCharges] = $this->computeCloudSecuritySummary($internal, $pricing);
            [$monitoringSummary, $totalMonitoringCharges] = $this->computeMonitoringSummary($internal, $pricing);
            [$securitySummary, $totalSecurityCharges] = $this->computeSecuritySummary($internal, $pricing);
            [$backupSummary, $totalBackupCharges] = $this->computeBackupSummary($internal, $pricing);

            // contract duration (ikut screen behavior)
            $contractDuration = $mode === 'annual'
                ? 12
                : (int) (session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12));


            // --- Logo (base64) ---
$logoPath = public_path('assets/time_logo.png'); // tukar kalau path lain
$logoBase64 = null;
if (is_file($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}


           
            $data = [
                'version'                   => $version,
                'quotation'                 => $quotation,
                'internal'                  => $internal,
                'project'                   => $version->project,
                'mode'                      => $mode,
                'viewOnly'                  => 1,     // hide links/btn
                'isPdf'                     => 1,     // if you want conditional css
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
                'contractDuration'          => $contractDuration,
                'backupSummary'             => $backupSummary,
                'totalBackupCharges'        => $totalBackupCharges,
                //'logoPath'                  => public_path('assets/time_logo.png'),
                'logoBase64' => $logoBase64,
                'logoPath'   => $logoPath,
            ];

            // --------- Render PDF dari blade khas ---------
            $pdf = Pdf::loadView('pdf.quotation-table', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions(['defaultFont' => 'sans-serif']);

            //$filename = 'quotation_' . ($version->project->project_code ?? 'project') . '_v' . ($version->version_number ?? '1') . '.pdf';

            $filename = sprintf(
    'quotation_%s_v%s_%s_%sm.pdf',
    $version->project->project_code ?? 'project',
    $version->version_number ?? '1',
    $mode,                                 // 'monthly' | 'annual'
    $contractDuration                      // 12 bila annual, x bila monthly ikut session
);


            return $pdf->download($filename);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    // ================= Helper kiraan (copy dari QuotationController) =================

    /*private function computeProfessionalTotals($s)
    {
        $unit = (float) data_get(config('pricing'), 'CPFS-PFS-MDY-5OTC.price_per_unit', 1200);
        $days = (int) ($s->mandays ?? 0);
        return [$days, $unit, $days * $unit];
    }*/
    private function computeProfessionalTotals($s)
{
    $days = (int) ($s->mandays ?? 0);

    $pricing = config('pricing');

    $sku1 = 'CPFS-PFS-MDY-1OTC'; // <=4 days
    $sku5 = 'CPFS-PFS-MDY-5OTC'; // >=5 days (discounted)

    $price1 = (float) data_get($pricing, "$sku1.price_per_unit", 0);
    $price5 = (float) data_get($pricing, "$sku5.price_per_unit", $price1);

    // Pilih harga ikut hari
    $unitPrice = $days >= 5 ? $price5 : $price1;

    $total = round($days * $unitPrice, 2);

    // Return ikut signature sedia ada: [$psDays, $psUnit, $totalProfessionalCharges]
    return [$days, 'Day', $total];
}


    private function computeNetworkTotals($s, $pricing)
    {
        $kl = 0.0; $cj = 0.0;
        $getPrice = fn($key) => (float) data_get($pricing, "$key.price_per_unit", 0);

        if (!empty($s->kl_bandwidth)) $kl += $s->kl_bandwidth * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth));
        if (!empty($s->cyber_bandwidth)) $cj += $s->cyber_bandwidth * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth));
        if (!empty($s->kl_bandwidth_with_antiddos)) $kl += $s->kl_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth_with_antiddos));
        if (!empty($s->cyber_bandwidth_with_antiddos)) $cj += $s->cyber_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth_with_antiddos));

        $map = [
            'kl_included_elastic_ip'    => ['CNET-EIP-SHR-FOC','KL'],
            'cyber_included_elastic_ip' => ['CNET-EIP-SHR-FOC','CJ'],
            'kl_elastic_ip'             => ['CNET-EIP-SHR-STD','KL'],
            'cyber_elastic_ip'          => ['CNET-EIP-SHR-STD','CJ'],
            'kl_elastic_load_balancer'    => ['CNET-ELB-SHR-STD','KL'],
            'cyber_elastic_load_balancer' => ['CNET-ELB-SHR-STD','CJ'],
            'kl_direct_connect_virtual'    => ['CNET-DGW-SHR-EXT','KL'],
            'cyber_direct_connect_virtual' => ['CNET-DGW-SHR-EXT','CJ'],
            'kl_l2br_instance'    => ['CNET-L2BR-SHR-EXT','KL'],
            'cyber_l2br_instance' => ['CNET-L2BR-SHR-EXT','CJ'],
            'kl_vpll_l2br'        => ['CNET-L2BR-SHR-INT','KL'],
            'cyber_vpll_l2br'     => ['CNET-L2BR-SHR-INT','CJ'],
            'kl_nat_gateway_small'     => ['CNET-NAT-SHR-S','KL'],
            'kl_nat_gateway_medium'    => ['CNET-NAT-SHR-M','KL'],
            'kl_nat_gateway_large'     => ['CNET-NAT-SHR-L','KL'],
            'kl_nat_gateway_xlarge'    => ['CNET-NAT-SHR-XL','KL'],
            'cyber_nat_gateway_small'  => ['CNET-NAT-SHR-S','CJ'],
            'cyber_nat_gateway_medium' => ['CNET-NAT-SHR-M','CJ'],
            'cyber_nat_gateway_large'  => ['CNET-NAT-SHR-L','CJ'],
            'cyber_nat_gateway_xlarge' => ['CNET-NAT-SHR-XL','CJ'],
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

        if (!empty($s->kl_virtual_private_leased_line)) $kl += $s->kl_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->kl_virtual_private_leased_line));
        if (!empty($s->cyber_virtual_private_leased_line)) $cj += $s->cyber_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->cyber_virtual_private_leased_line));

        return [round($kl,2), round($cj,2)];
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

    private function computeManagedSummary(\App\Models\Version $version): array
    {
        $svc = $version->security_service;
        if (!$svc) return [[], 0.0];

        $services = [
            'Managed Operating System',
            'Managed Backup and Restore',
            'Managed Patching',
            'Managed DR',
        ];
        $counts = [];
        foreach ($services as $s) $counts[$s] = ['kl_qty'=>0,'cj_qty'=>0];

        foreach (range(1,4) as $i) {
            $v = $svc->{'kl_managed_services_' . $i} ?? null;
            if ($v && in_array($v, $services, true)) $counts[$v]['kl_qty']++;
        }
        foreach (range(1,4) as $i) {
            $v = $svc->{'cyber_managed_services_' . $i} ?? null;
            if ($v && in_array($v, $services, true)) $counts[$v]['cj_qty']++;
        }

        [$priceByName, $unitByName] = $this->lookupManagedServicePrices();
        $summary = []; $grand = 0.0;
        foreach ($services as $name) {
            $unit  = $unitByName[strtolower($name)] ?? 'VM';
            $price = (float) ($priceByName[strtolower($name)] ?? 0);
            $klPrice = $counts[$name]['kl_qty'] * $price;
            $cjPrice = $counts[$name]['cj_qty'] * $price;

            if (($counts[$name]['kl_qty'] + $counts[$name]['cj_qty']) > 0) {
                $summary[] = [
                    'name'=>$name,'unit'=>$unit,'price_per_unit'=>$price,
                    'kl_qty'=>$counts[$name]['kl_qty'],'cj_qty'=>$counts[$name]['cj_qty'],
                    'kl_price'=>$klPrice,'cj_price'=>$cjPrice,
                ];
            }
            $grand += $klPrice + $cjPrice;
        }
        return [$summary, round($grand,2)];
    }
    private function lookupManagedServicePrices(): array
    {
        $pricing = config('pricing');
        $priceByName = []; $unitByName = [];
        foreach ($pricing as $code => $item) {
            if (!is_array($item)) continue;
            $cat  = strtolower((string)($item['category_name'] ?? ''));
            $name = strtolower(trim((string)($item['name'] ?? '')));
            if ($name === '') continue;
            if ($cat === 'managed services') {
                $priceByName[$name] = (float)($item['price_per_unit'] ?? 0);
                $unitByName[$name]  = (string)($item['measurement_unit'] ?? 'VM');
            }
        }
        if (empty($priceByName)) {
            foreach ($pricing as $code => $item) {
                if (!is_array($item)) continue;
                $name = strtolower(trim((string)($item['name'] ?? '')));
                if (in_array($name, [
                    'managed operating system','managed backup and restore',
                    'managed patching','managed dr',
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
        $rows = []; $grand = 0.0;
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
                    'name'=>$label,'unit'=>'Unit','price_per_unit'=>$price,
                    'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                    'kl_price'=>$klQty*$price,'cj_price'=>$cjQty*$price,
                ];
                $rows[] = $row; $grand += $row['kl_price'] + $row['cj_price'];
            }
        }
        return [$rows, round($grand,2)];
    }

    private function computeStorageSummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];
        $rows = []; $grand = 0.0;
        $map = [
            'Elastic Volume Service (EVS)' => ['kl'=>'kl_evs','cj'=>'cyber_evs','key'=>'CSTG-EVS-SHR-STD','unit'=>'GB'],
            'Scalable File Service (SFS)'  => ['kl'=>'kl_scalable_file_service','cj'=>'cyber_scalable_file_service','key'=>'CSTG-SFS-SHR-STD','unit'=>'GB'],
            'Object Storage Service (OBS)' => ['kl'=>'kl_object_storage_service','cj'=>'cyber_object_storage_service','key'=>'CSTG-OBS-SHR-STD','unit'=>'GB'],
            'Snapshot Storage'             => ['kl'=>'kl_snapshot_storage','cj'=>'cyber_snapshot_storage','key'=>'CSTG-BCK-SHR-STD','unit'=>'GB'],
            'Image Storage'                => ['kl'=>'kl_image_storage','cj'=>'cyber_image_storage','key'=>'CSTG-OBS-SHR-IMG','unit'=>'GB'],
        ];
        foreach ($map as $label => $def) {
            $unit = $def['unit'];
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);
            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);
            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'=>$label,'unit'=>$unit,'price_per_unit'=>$price,
                    'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                    'kl_price'=>$klQty*$price,'cj_price'=>$cjQty*$price,
                ];
                $rows[] = $row; $grand += $row['kl_price'] + $row['cj_price'];
            }
        }
        return [$rows, round($grand,2)];
    }

    private function computeCloudSecuritySummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];
        $rows = []; $grand = 0.0;
        $map = [
            'Cloud Firewall (Fortigate)' => ['kl'=>'kl_firewall_fortigate','cj'=>'cyber_firewall_fortigate','key'=>'CSEC-VFW-DDT-FG','unit'=>'Unit'],
            'Cloud Firewall (OPNSense)'  => ['kl'=>'kl_firewall_opnsense','cj'=>'cyber_firewall_opnsense','key'=>'CSEC-VFW-DDT-OS','unit'=>'Unit'],
            'Cloud Shared WAF (Mbps)'    => ['kl'=>'kl_shared_waf','cj'=>'cyber_shared_waf','key'=>'CSEC-WAF-SHR-HA','unit'=>'Mbps'],
            'Anti-Virus (Panda)'         => ['kl'=>'kl_antivirus','cj'=>'cyber_antivirus','key'=>'CSEC-EDR-NOD-STD','unit'=>'Unit'],
        ];
        foreach ($map as $label => $def) {
            $unit = $def['unit'];
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);
            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);
            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'=>$label,'unit'=>$unit,'price_per_unit'=>$price,
                    'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                    'kl_price'=>$klQty*$price,'cj_price'=>$cjQty*$price,
                ];
                $rows[] = $row; $grand += $row['kl_price'] + $row['cj_price'];
            }
        }
        return [$rows, round($grand,2)];
    }

    private function computeMonitoringSummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];
        $price = (float)($pricing['CMON-TIS-NOD-STD']['price_per_unit'] ?? 0);
        $klQty = (int)($s->kl_insight_vmonitoring ?? 0);
        $cjQty = (int)($s->cyber_insight_vmonitoring ?? 0);
        $rows = [];
        if ($klQty > 0 || $cjQty > 0) {
            $rows[] = [
                'name'=>'TCS inSight vMonitoring','unit'=>'Unit','price_per_unit'=>$price,
                'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                'kl_price'=>$klQty*$price,'cj_price'=>$cjQty*$price,
            ];
        }
        $grand = collect($rows)->sum(fn($r)=>$r['kl_price'] + $r['cj_price']);
        return [$rows, round($grand,2)];
    }

    private function computeSecuritySummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];
        $price = (float)($pricing['SECT-VAS-EIP-STD']['price_per_unit'] ?? 0);
        $klQty = (int)($s->kl_cloud_vulnerability ?? 0);
        $cjQty = (int)($s->cyber_cloud_vulnerability ?? 0);
        $rows = [];
        if ($klQty > 0 || $cjQty > 0) {
            $rows[] = [
                'name'=>'Cloud Vulnerability Assessment (Per IP)','unit'=>'Unit','price_per_unit'=>$price,
                'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                'kl_price'=>$klQty*$price,'cj_price'=>$cjQty*$price,
            ];
        }
        $grand = 0.0; foreach ($rows as $r) $grand += $r['kl_price'] + $r['cj_price'];
        return [$rows, round($grand,2)];
    }

    private function computeBackupSummary($s, array $pricing): array
{
    if (!$s) return [[], 0.0];

    $rows = [];
    $grand = 0.0;

    // Mapping item → {field KL, field CJ, pricing key, unit}
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
        'Backup Storage – Snapshot' => [
            'kl' => 'kl_snapshot_storage',
            'cj' => 'cyber_snapshot_storage',
            'key' => 'CSBS-STRG-SNP-SCNT',
            'unit' => 'GB',
        ],
        'Backup Storage – Standalone Image "S3 usage CBSB or None"' => [
            'kl' => 'kl_image_storage',
            'cj' => 'cyber_image_storage',
            'key' => 'OBJS-STRG-OBJ-STD',
            'unit' => 'GB',
        ],
        'Cloud Server Backup Service - Full Backups Retention Capacity' => [
            'kl' => 'kl_full_backup_total_retention_storage',
            'cj' => 'cyber_full_backup_total_retention_storage',
            'key' => 'CSBS-STRG-BCK-CFREP',
            'unit' => 'GB',
        ],
        'Cloud Server Backup Service - Incremental Backups Retention Capacity' => [
            'kl' => 'kl_incremental_backup_total_retention_storage',
            'cj' => 'cyber_incremental_backup_total_retention_storage',
            'key' => 'CSBS-STRG-BCK-CIREP',
            'unit' => 'GB',
        ],
        'Cloud Server Replication Service - Retention Capacity' => [
            'kl' => 'kl_replication_retention_capacity',
            'cj' => 'cyber_replication_retention_capacity',
            'key' => 'CSBS-STRG-BCK-REPS',
            'unit' => 'GB',
        ],
    ];

    // Item-item kapasiti/retention biasa (GB)
    foreach ($map as $label => $def) {
        $price = (float) data_get($pricing, $def['key'].'.price_per_unit', 0);
        $unit  = $def['unit'];

        $klQty = (float) ($s->{$def['kl']} ?? 0);
        $cjQty = (float) ($s->{$def['cj']} ?? 0);

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

    // Replication Service - Bandwidth (TB) — pilih tier ikut jumlah TB
    $pickTier = function (float $tb) {
        if ($tb <= 0) return null;
        if ($tb <= 5)   return ['label' => 'Cloud Server Replication Service - Bandwidth 5TB',   'key' => 'CDRS-SVC-REP-5TBD'];
        if ($tb <= 10)  return ['label' => 'Cloud Server Replication Service - Bandwidth 10TB',  'key' => 'CDRS-SVC-REP-10TBD'];
        if ($tb <= 20)  return ['label' => 'Cloud Server Replication Service - Bandwidth 20TB',  'key' => 'CDRS-SVC-REP-20TBD'];
        if ($tb <= 50)  return ['label' => 'Cloud Server Replication Service - Bandwidth 50TB',  'key' => 'CDRS-SVC-REP-50TBD'];
        return ['label' => 'Cloud Server Replication Service - Bandwidth 100TB', 'key' => 'CDRS-SVC-REP-100TB'];
    };

    $klTB = (float) ($s->kl_replication_service_data_changes_TB ?? 0);
    $cjTB = (float) ($s->cyber_replication_service_data_changes_TB ?? 0);

    $klTier = $pickTier($klTB);
    $cjTier = $pickTier($cjTB);

    // KL row
    if ($klTier) {
        $price = (float) data_get($pricing, $klTier['key'].'.price_per_unit', 0);
        $row = [
            'name'           => $klTier['label'],
            'unit'           => 'TB',
            'price_per_unit' => $price,
            'kl_qty'         => $klTB,
            'cj_qty'         => 0,
            'kl_price'       => round($klTB * $price, 2),
            'cj_price'       => 0,
        ];
        $rows[] = $row;
        $grand += $row['kl_price'];
    }

    // CJ row
    if ($cjTier) {
        $price = (float) data_get($pricing, $cjTier['key'].'.price_per_unit', 0);
        $row = [
            'name'           => $cjTier['label'],
            'unit'           => 'TB',
            'price_per_unit' => $price,
            'kl_qty'         => 0,
            'cj_qty'         => $cjTB,
            'kl_price'       => 0,
            'cj_price'       => round($cjTB * $price, 2),
        ];
        $rows[] = $row;
        $grand += $row['cj_price'];
    }

    return [$rows, round($grand, 2)];
}


    private function computeEcsSummary(\App\Models\Version $version): array
    {
        $rows = $version->ecs_configuration ?? collect();
        if (!($rows instanceof \Illuminate\Support\Collection)) $rows = collect($rows);
        [$nameToPrice, $nameToUnit] = $this->buildEcsPricingLookups();
        $summary = [];
        foreach ($rows as $r) {
            $flavour = trim((string) $r->ecs_flavour_mapping);
            if ($flavour === '') continue;
            $region  = trim((string) $r->region);
            $price   = (float) ($nameToPrice[strtolower($flavour)] ?? 0);
            $unit    = (string) ($nameToUnit[strtolower($flavour)] ?? 'Unit');

            if (!isset($summary[$flavour])) {
                $summary[$flavour] = [
                    'flavour'=>$flavour,'unit'=>$unit,'unit_price'=>$price,'price_per_unit' => $price,
                    'kl_qty'=>0,'cj_qty'=>0,'kl_price'=>0.0,'cj_price'=>0.0,
                ];
            }
            if (strcasecmp($region, 'Kuala Lumpur') === 0) {
                $summary[$flavour]['kl_qty'] += 1;
                $summary[$flavour]['kl_price'] = $summary[$flavour]['kl_qty'] * $price;
            } elseif (strcasecmp($region, 'Cyberjaya') === 0) {
                $summary[$flavour]['cj_qty'] += 1;
                $summary[$flavour]['cj_price'] = $summary[$flavour]['cj_qty'] * $price;
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
        $nameToPrice = []; $nameToUnit = [];
        foreach ($pricing as $key => $item) {
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
}





