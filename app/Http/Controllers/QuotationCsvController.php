<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Version;
use App\Models\Quotation;
use App\Models\InternalSummary;
use Illuminate\Support\Str;
use League\Csv\Writer;
use SplTempFileObject;

class QuotationCsvController extends Controller
{
    //public function generateCsv($versionId)

    public function generateCsv(Request $request, $versionId)
    {
        $version = Version::with(['project.customer', 'solution_type'])->findOrFail($versionId);

        // Create a real quotation row if not exists
        $quotation = Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            ]
        );

        $mode = $request->query('mode', 'monthly'); // 'monthly' | 'annual'
$contractDuration = $mode === 'annual'
    ? 12
    : (int) (session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12));

        // Get all the data needed for the quotation
        $internal = InternalSummary::where('version_id', $versionId)->first();
        if (!$internal) {
            $internal = new InternalSummary();
        }

        $pricing = config('pricing');

        // Calculate all the summary data
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

        // Calculate totals
        /*$monthlyTotal = 
            ($totalManagedCharges ?? 0) +
            ($klTotal ?? 0) + ($cjTotal ?? 0) +
            ($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0) +
            (collect($licenseRateCard)->sum('kl_price') + collect($licenseRateCard)->sum('cj_price')) +
            ($totalStorageCharges ?? 0) +
            ($totalBackupCharges ?? 0) +
            ($totalcloudSecurityCharges ?? 0) +
            ($totalMonitoringCharges ?? 0) +
            ($totalSecurityCharges ?? 0);*/

        // === samakan formula monthlyTotal dengan PDF ===
$licenseKL = collect($licenseRateCard)->sum('kl_price');
$licenseCJ = collect($licenseRateCard)->sum('cj_price');
$licenseTotal = $totalLicenseCharges ?? ($licenseKL + $licenseCJ);

$monthlyTotal =
    ($totalManagedCharges ?? 0) +                 // Managed (total)
    (($klTotal ?? 0) + ($cjTotal ?? 0)) +         // Network (KL + CJ)
    (($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) +   // ECS (KL + CJ)
    $licenseTotal +                               // Licenses
    ($totalStorageCharges ?? 0) +                 // Storage (total)
    ($totalBackupCharges ?? 0) +                  // Backup (total)
    ($totalcloudSecurityCharges ?? 0) +           // Cloud Security (total)
    ($totalMonitoringCharges ?? 0) +              // Monitoring (total)
    ($totalSecurityCharges ?? 0);                 // Security Services (total)





            



        $contractTotal = ($monthlyTotal * $contractDuration) + ($totalProfessionalCharges ?? 0);
        $serviceTax = $contractTotal * 0.08;
        $finalTotal = $contractTotal + $serviceTax;

        // Create CSV
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        // Set headers
        $csv->insertOne(['Quotation Report']);
        $csv->insertOne(['Generated Date', now()->format('d/m/Y')]);
        $csv->insertOne(['Quotation ID', $quotation->id ?? 'N/A']);
        $csv->insertOne(['Customer', $version->project->customer->name ?? 'N/A']);
        $csv->insertOne(['Project', $version->project->name ?? 'N/A']);
      

        $csv->insertOne(['Contract Duration', $contractDuration . ' Months']);
        $csv->insertOne([]);

        // Summary headers
        $csv->insertOne(['Category', 'One Time Charges', 'KL Monthly', 'CJ Monthly', 'Total Monthly']);
        
        // Add data rows
        $csv->insertOne(['Professional Services', 'RM' . number_format($totalProfessionalCharges, 2), '', '', '']);
        
        // Managed Services
        $klManaged = collect($managedSummary)->sum('kl_price');
        $cjManaged = collect($managedSummary)->sum('cj_price');
        $csv->insertOne(['Managed Services', '', 
            $klManaged > 0 ? 'RM' . number_format($klManaged, 2) : 'RM -',
            $cjManaged > 0 ? 'RM' . number_format($cjManaged, 2) : 'RM -',
            $totalManagedCharges > 0 ? 'RM' . number_format($totalManagedCharges, 2) : 'RM -'
        ]);

        // Network
        $csv->insertOne(['Network', '', 
            $klTotal > 0 ? 'RM' . number_format($klTotal, 2) : 'RM -',
            $cjTotal > 0 ? 'RM' . number_format($cjTotal, 2) : 'RM -',
            ($klTotal + $cjTotal) > 0 ? 'RM' . number_format($klTotal + $cjTotal, 2) : 'RM -'
        ]);

        // ECS
        $csv->insertOne(['Compute - ECS', '', 
            $klEcsTotal > 0 ? 'RM' . number_format($klEcsTotal, 2) : 'RM -',
            $cjEcsTotal > 0 ? 'RM' . number_format($cjEcsTotal, 2) : 'RM -',
            ($klEcsTotal + $cjEcsTotal) > 0 ? 'RM' . number_format($klEcsTotal + $cjEcsTotal, 2) : 'RM -'
        ]);

        // Licenses
        /*$licenseKL = collect($licenseRateCard)->sum('kl_price');
        $licenseCJ = collect($licenseRateCard)->sum('cj_price');
        $csv->insertOne(['Licenses', '', 
            $licenseKL > 0 ? 'RM' . number_format($licenseKL, 2) : 'RM -',
            $licenseCJ > 0 ? 'RM' . number_format($licenseCJ, 2) : 'RM -',
            ($licenseKL + $licenseCJ) > 0 ? 'RM' . number_format($licenseKL + $licenseCJ, 2) : 'RM -'
        ]);*/

        $csv->insertOne(['Licenses', '', 
    $licenseKL > 0 ? 'RM' . number_format($licenseKL, 2) : 'RM -',
    $licenseCJ > 0 ? 'RM' . number_format($licenseCJ, 2) : 'RM -',
    ($licenseKL + $licenseCJ) > 0 ? 'RM' . number_format($licenseKL + $licenseCJ, 2) : 'RM -'
]);


        // Storage
        $klStorage = collect($storageSummary)->sum('kl_price');
        $cjStorage = collect($storageSummary)->sum('cj_price');
        $csv->insertOne(['Storage', '', 
            $klStorage > 0 ? 'RM' . number_format($klStorage, 2) : 'RM -',
            $cjStorage > 0 ? 'RM' . number_format($cjStorage, 2) : 'RM -',
            $totalStorageCharges > 0 ? 'RM' . number_format($totalStorageCharges, 2) : 'RM -'
        ]);

        // Backup
        $klBackup = collect($backupSummary)->sum('kl_price');
        $cjBackup = collect($backupSummary)->sum('cj_price');
        $csv->insertOne(['Backup', '', 
            $klBackup > 0 ? 'RM' . number_format($klBackup, 2) : 'RM -',
            $cjBackup > 0 ? 'RM' . number_format($cjBackup, 2) : 'RM -',
            $totalBackupCharges > 0 ? 'RM' . number_format($totalBackupCharges, 2) : 'RM -'
        ]);

        // Cloud Security
        $klCloud = collect($cloudSecuritySummary)->sum('kl_price');
        $cjCloud = collect($cloudSecuritySummary)->sum('cj_price');
        $csv->insertOne(['Cloud Security', '', 
            $klCloud > 0 ? 'RM' . number_format($klCloud, 2) : 'RM -',
            $cjCloud > 0 ? 'RM' . number_format($cjCloud, 2) : 'RM -',
            $totalcloudSecurityCharges > 0 ? 'RM' . number_format($totalcloudSecurityCharges, 2) : 'RM -'
        ]);

        // Monitoring
        $klMonitor = collect($monitoringSummary)->sum('kl_price');
        $cjMonitor = collect($monitoringSummary)->sum('cj_price');
        $csv->insertOne(['Monitoring', '', 
            $klMonitor > 0 ? 'RM' . number_format($klMonitor, 2) : 'RM -',
            $cjMonitor > 0 ? 'RM' . number_format($cjMonitor, 2) : 'RM -',
            $totalMonitoringCharges > 0 ? 'RM' . number_format($totalMonitoringCharges, 2) : 'RM -'
        ]);

        // Security Services
        $klSecurity = collect($securitySummary)->sum('kl_price');
        $cjSecurity = collect($securitySummary)->sum('cj_price');
        $csv->insertOne(['Security Services', '', 
            $klSecurity > 0 ? 'RM' . number_format($klSecurity, 2) : 'RM -',
            $cjSecurity > 0 ? 'RM' . number_format($cjSecurity, 2) : 'RM -',
            $totalSecurityCharges > 0 ? 'RM' . number_format($totalSecurityCharges, 2) : 'RM -'
        ]);

        // Totals
        $csv->insertOne([]);
        $csv->insertOne(['', 'ONE TIME CHARGES TOTAL', 'RM' . number_format($totalProfessionalCharges, 2)]);
        $csv->insertOne(['', 'MONTHLY TOTAL', 'RM' . number_format($monthlyTotal, 2)]);
        $csv->insertOne(['', 'CONTRACT TOTAL', 'RM' . number_format($contractTotal, 2)]);
        $csv->insertOne(['', 'SERVICE TAX (8%)', 'RM' . number_format($serviceTax, 2)]);
        $csv->insertOne(['', 'FINAL TOTAL (Include Tax)', 'RM' . number_format($finalTotal, 2)]);

        // Set headers for download
        $csv->output('quotation_' . $version->id . '_' . now()->format('Ymd_His') . '.csv');
        die;
    }

    // Copy the same helper methods from QuotationController
    private function computeManagedSummary(\App\Models\Version $version): array
    {
        $svc = $version->security_service;
        if (!$svc) {
            return [[], 0.0];
        }

        $services = [
            'Managed Operating System',
            'Managed Backup and Restore',
            'Managed Patching',
            'Managed DR',
        ];

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

        foreach ($pricing as $key => $item) {
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
            foreach ($pricing as $key => $item) {
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

        $price = (float)($pricing['CMON-TIS-NOD-STD']['price_per_unit'] ?? 0);
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

        $price = (float)($pricing['SECT-VAS-EIP-STD']['price_per_unit'] ?? 0);
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
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);
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
        $rows = $version->ecs_configuration ?? collect();
        if (!($rows instanceof \Illuminate\Support\Collection)) {
            $rows = collect($rows);
        }

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
                    'flavour'   => $flavour,
                    'unit'      => $unit,
                    'unit_price'=> $price,
                    'kl_qty'    => 0,
                    'cj_qty'    => 0,
                    'kl_price'  => 0.0,
                    'cj_price'  => 0.0,
                ];
            }

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

    private function computeNetworkTotals($s, array $pricing): array
    {
        $kl = 0.0; $cj = 0.0;

        $getPrice = function($key) use ($pricing) {
            return (float) data_get($pricing, "$key.price_per_unit", 0);
        };

        if (!empty($s->kl_bandwidth)) {
            $kl += $s->kl_bandwidth * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth));
        }
        if (!empty($s->cyber_bandwidth)) {
            $cj += $s->cyber_bandwidth * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth));
        }
        if (!empty($s->kl_bandwidth_with_antiddos)) {
            $kl += $s->kl_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth_with_antiddos));
        }
        if (!empty($s->cyber_bandwidth_with_antiddos)) {
            $cj += $s->cyber_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth_with_antiddos));
        }

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

    private function computeProfessionalTotals($s)
    {
        $unit = (float) data_get(config('pricing'), 'CPFS-PFS-MDY-5OTC.price_per_unit', 1200);
        $days = (int) ($s->mandays ?? 0);
        $total = $days * $unit;
        return [$days, $unit, $total];
    }
}
