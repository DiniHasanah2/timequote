<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Models\InternalSummary;
use App\Models\ECSConfiguration;
use App\Models\SecurityService;
use Barryvdh\DomPDF\Facade\Pdf;


class RateCardController extends Controller
{
    public function showRateCard($versionId)
    {
        $version = Version::with([
            'internal_summary',
            'region',
            'project.customer',
            'security_service',    
        ])->findOrFail($versionId);

        $internalSummary = $version->internal_summary;

        \Log::info('InternalSummary data:', (array) $internalSummary);

        if (is_null($internalSummary)) {
            \Log::warning("InternalSummary is null for version: $versionId");
            $internalSummary = new \App\Models\InternalSummary();
        }
        $pricing = config('pricing');

        // Calculate rate card items based on quantity × price_per_unit
        $rateCardItems = $this->calculateRateCardItems($internalSummary, $pricing);

        //add Managed Services rows
        /*$rateCardItems = array_merge(
            $rateCardItems,
            $this->calculateManagedItems($version->security_service, $pricing)
        );*/

        return view('projects.security_service.ratecard', compact(
            'version',
            'rateCardItems'
        ));
    }

    private function calculateRateCardItems($internalSummary, $pricing)
    {
        $items = [];


        // Professional Services
        $items = array_merge($items, $this->calculateProfessionalServices($internalSummary, $pricing));


         // Managed Services
        $items = array_merge($items, $this->calculateManagedItems($internalSummary, $pricing));



        // Network Services
        $items = array_merge($items, $this->calculateNetworkItems($internalSummary, $pricing));



        // Compute Services
        $items = array_merge($items, $this->calculateComputeItems($internalSummary, $pricing));

        // License Services
        $items = array_merge($items, $this->calculateLicenseItems($internalSummary, $pricing));

        
        // Storage Services
        $items = array_merge($items, $this->calculateStorageItems($internalSummary, $pricing));
        
        
        // Security Services
        $items = array_merge($items, $this->calculateSecurityItems($internalSummary, $pricing));

         
        // Backup Services
        $items = array_merge($items, $this->calculateBackupDrItems($internalSummary, $pricing));

        // Monitoring Services  
        $items = array_merge($items, $this->calculateMonitoringItems($internalSummary, $pricing));

        




        

        return $items;
    }

    private function calculateNetworkItems($summary, $pricing)
    {
        $items = [];
        
        // Bandwidth KL
        if ($summary->kl_bandwidth > 0) {
            $priceKey = $this->getBandwidthPriceKey($summary->kl_bandwidth);
            $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Bandwidth',
                'quantity' => $summary->kl_bandwidth,
                'unit' => 'Mbps',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_bandwidth * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // Bandwidth Cyber
        if ($summary->cyber_bandwidth > 0) {
            $priceKey = $this->getBandwidthPriceKey($summary->cyber_bandwidth);
            $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Bandwidth',
                'quantity' => $summary->cyber_bandwidth,
                'unit' => 'Mbps',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_bandwidth * $price,
                'region' => 'Cyberjaya'
            ];
        }

        // Bandwidth with Anti-DDoS KL
        if ($summary->kl_bandwidth_with_antiddos > 0) {
            $priceKey = $this->getBandwidthPriceKey($summary->kl_bandwidth_with_antiddos);
            $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Bandwidth with Anti-DDoS',
                'quantity' => $summary->kl_bandwidth_with_antiddos,
                'unit' => 'Mbps',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_bandwidth_with_antiddos * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // Bandwidth with Anti-DDoS Cyber
        if ($summary->cyber_bandwidth_with_antiddos > 0) {
            $priceKey = $this->getBandwidthPriceKey($summary->cyber_bandwidth_with_antiddos);
            $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Bandwidth with Anti-DDoS',
                'quantity' => $summary->cyber_bandwidth_with_antiddos,
                'unit' => 'Mbps',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_bandwidth_with_antiddos * $price,
                'region' => 'Cyberjaya'
            ];
        }



         // Elastic IP
        $elasticIPs = [
            'kl_included_elastic_ip' => ['KL Included Elastic IP (FOC)', 'CNET-EIP-SHR-FOC'],
            'cyber_included_elastic_ip' => ['Cyberjaya Included Elastic IP (FOC)', 'CNET-EIP-SHR-FOC'],
            'kl_elastic_ip' => ['KL Elastic IP', 'CNET-EIP-SHR-STD'],
            'cyber_elastic_ip' => ['Cyberjaya Elastic IP', 'CNET-EIP-SHR-STD'],
        ];

        foreach ($elasticIPs as $field => [$name, $priceKey]) {
            if ($summary->$field > 0) {
                $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
                $region = strpos($field, 'kl_') === 0 ? 'Kuala Lumpur' : 'Cyberjaya';
                $items[] = [
                    'name' => $name,
                    'quantity' => $summary->$field,
                    'unit' => 'Unit',
                    'price_per_unit' => $price,
                    'total_price' => $summary->$field * $price,
                    'region' => $region
                ];
            }
        }

        
       

        // Elastic Load Balancer
        $loadBalancers = [
            'kl_elastic_load_balancer' => ['KL Elastic Load Balancer', 'CNET-ELB-SHR-STD'],
            'cyber_elastic_load_balancer' => ['Cyberjaya Elastic Load Balancer', 'CNET-ELB-SHR-STD'],
        ];

        foreach ($loadBalancers as $field => [$name, $priceKey]) {
            if ($summary->$field > 0) {
                $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
                $region = strpos($field, 'kl_') === 0 ? 'Kuala Lumpur' : 'Cyberjaya';
                $items[] = [
                    'name' => $name,
                    'quantity' => $summary->$field,
                    'unit' => 'Unit',
                    'price_per_unit' => $price,
                    'total_price' => $summary->$field * $price,
                    'region' => $region
                ];
            }
        }

        // Direct Connect Virtual Gateway
        $directConnects = [
            'kl_direct_connect_virtual' => ['KL Direct Connect Virtual Gateway', 'CNET-DGW-SHR-EXT'],
            'cyber_direct_connect_virtual' => ['Cyberjaya Direct Connect Virtual Gateway', 'CNET-DGW-SHR-EXT'],
        ];

        foreach ($directConnects as $field => [$name, $priceKey]) {
            if ($summary->$field > 0) {
                $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
                $region = strpos($field, 'kl_') === 0 ? 'Kuala Lumpur' : 'Cyberjaya';
                $items[] = [
                    'name' => $name,
                    'quantity' => $summary->$field,
                    'unit' => 'Unit',
                    'price_per_unit' => $price,
                    'total_price' => $summary->$field * $price,
                    'region' => $region
                ];
            }
        }

        // L2BR Instance
        $l2brInstances = [
            'kl_l2br_instance' => ['KL L2BR Instance', 'CNET-L2BR-SHR-EXT'],
            'cyber_l2br_instance' => ['Cyberjaya L2BR Instance', 'CNET-L2BR-SHR-EXT'],
        ];

        foreach ($l2brInstances as $field => [$name, $priceKey]) {
            if ($summary->$field > 0) {
                $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
                $region = strpos($field, 'kl_') === 0 ? 'Kuala Lumpur' : 'Cyberjaya';
                $items[] = [
                    'name' => $name,
                    'quantity' => $summary->$field,
                    'unit' => 'Unit',
                    'price_per_unit' => $price,
                    'total_price' => $summary->$field * $price,
                    'region' => $region
                ];
            }
        }





        // vPLL KL
        if ($summary->kl_virtual_private_leased_line > 0) {
            $priceKey = $this->getVPLLPriceKey($summary->kl_virtual_private_leased_line);
            $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Virtual Private Leased Line (vPLL)',
                'quantity' => $summary->kl_virtual_private_leased_line,
                'unit' => 'Mbps',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_virtual_private_leased_line * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // vPLL Cyber
        if ($summary->cyber_virtual_private_leased_line > 0) {
            $priceKey = $this->getVPLLPriceKey($summary->cyber_virtual_private_leased_line);
            $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Virtual Private Leased Line (vPLL)',
                'quantity' => $summary->cyber_virtual_private_leased_line,
                'unit' => 'Mbps',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_virtual_private_leased_line * $price,
                'region' => 'Cyberjaya'
            ];
        }





        // vPLL L2BR KL
        if ($summary->kl_vpll_l2br > 0) {
            $price = $pricing['CNET-L2BR-SHR-INT']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL vPLL L2BR',
                'quantity' => $summary->kl_vpll_l2br,
                'unit' => 'Pair',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_vpll_l2br * $price,
                'region' => 'Kuala Lumpur'
            ];
        }






        // NAT Gateways
        $natGateways = [
            'kl_nat_gateway_small' => ['KL NAT Gateway (Small)', 'CNET-NAT-SHR-S'],
            'kl_nat_gateway_medium' => ['KL NAT Gateway (Medium)', 'CNET-NAT-SHR-M'],
            'kl_nat_gateway_large' => ['KL NAT Gateway (Large)', 'CNET-NAT-SHR-L'],
            'kl_nat_gateway_xlarge' => ['KL NAT Gateway (XLarge)', 'CNET-NAT-SHR-XL'],
            'cyber_nat_gateway_small' => ['Cyberjaya NAT Gateway (Small)', 'CNET-NAT-SHR-S'],
            'cyber_nat_gateway_medium' => ['Cyberjaya NAT Gateway (Medium)', 'CNET-NAT-SHR-M'],
            'cyber_nat_gateway_large' => ['Cyberjaya NAT Gateway (Large)', 'CNET-NAT-SHR-L'],
            'cyber_nat_gateway_xlarge' => ['Cyberjaya NAT Gateway (XLarge)', 'CNET-NAT-SHR-XL'],
        ];

        foreach ($natGateways as $field => [$name, $priceKey]) {
            if ($summary->$field > 0) {
                $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
                $region = strpos($field, 'kl_') === 0 ? 'Kuala Lumpur' : 'Cyberjaya';
                $items[] = [
                    'name' => $name,
                    'quantity' => $summary->$field,
                    'unit' => 'Unit',
                    'price_per_unit' => $price,
                    'total_price' => $summary->$field * $price,
                    'region' => $region
                ];
            }
        }


        // Global Server Load Balancer
        $gslbs = [
            'kl_gslb' => ['KL Global Server Load Balancer (GSLB)', 'CNET-GLB-SHR-DOMAIN'],
            'cyber_gslb' => ['Cyberjaya Global Server Load Balancer (GSLB)', 'CNET-GLB-SHR-DOMAIN'],
        ];

        foreach ($gslbs as $field => [$name, $priceKey]) {
            if ($summary->$field > 0) {
                $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
                $region = strpos($field, 'kl_') === 0 ? 'Kuala Lumpur' : 'Cyberjaya';
                $items[] = [
                    'name' => $name,
                    'quantity' => $summary->$field,
                    'unit' => 'Domain',
                    'price_per_unit' => $price,
                    'total_price' => $summary->$field * $price,
                    'region' => $region
                ];
            }
        }

        return $items;
    }

    private function calculateStorageItems($summary, $pricing)
    {
        $items = [];

        // EVS Storage - KL
        if ($summary->kl_evs > 0) {
            $price = $pricing['CSTG-EVS-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Elastic Volume Service (EVS)',
                'quantity' => $summary->kl_evs,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_evs * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // EVS Storage - Cyber
        if ($summary->cyber_evs > 0) {
            $price = $pricing['CSTG-EVS-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Elastic Volume Service (EVS)',
                'quantity' => $summary->cyber_evs,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_evs * $price,
                'region' => 'Cyberjaya'
            ];
        }

        // SFS Storage - KL
        if ($summary->kl_scalable_file_service > 0) {
            $price = $pricing['CSTG-SFS-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Scalable File Service (SFS)',
                'quantity' => $summary->kl_scalable_file_service,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_scalable_file_service * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // SFS Storage - Cyber
        if ($summary->cyber_scalable_file_service > 0) {
            $price = $pricing['CSTG-SFS-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Scalable File Service (SFS)',
                'quantity' => $summary->cyber_scalable_file_service,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_scalable_file_service * $price,
                'region' => 'Cyberjaya'
            ];
        }

        // OBS Storage - KL
        if ($summary->kl_object_storage_service > 0) {
            $price = $pricing['CSTG-OBS-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Object Storage Service (OBS)',
                'quantity' => $summary->kl_object_storage_service,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_object_storage_service * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // OBS Storage - Cyber
        if ($summary->cyber_object_storage_service > 0) {
            $price = $pricing['CSTG-OBS-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Object Storage Service (OBS)',
                'quantity' => $summary->cyber_object_storage_service,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_object_storage_service * $price,
                'region' => 'Cyberjaya'
            ];
        }

        // Snapshot Storage - KL
        if ($summary->kl_snapshot_storage > 0) {
            $price = $pricing['CSTG-BCK-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Snapshot Storage',
                'quantity' => $summary->kl_snapshot_storage,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_snapshot_storage * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // Snapshot Storage - Cyber
        if ($summary->cyber_snapshot_storage > 0) {
            $price = $pricing['CSTG-BCK-SHR-STD']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Snapshot Storage',
                'quantity' => $summary->cyber_snapshot_storage,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_snapshot_storage * $price,
                'region' => 'Cyberjaya'
            ];
        }

        // Image Storage - KL
        if ($summary->kl_image_storage > 0) {
            $price = $pricing['CSTG-OBS-SHR-IMG']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'KL Image Storage',
                'quantity' => $summary->kl_image_storage,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->kl_image_storage * $price,
                'region' => 'Kuala Lumpur'
            ];
        }

        // Image Storage - Cyber
        if ($summary->cyber_image_storage > 0) {
            $price = $pricing['CSTG-OBS-SHR-IMG']['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Cyberjaya Image Storage',
                'quantity' => $summary->cyber_image_storage,
                'unit' => 'GB',
                'price_per_unit' => $price,
                'total_price' => $summary->cyber_image_storage * $price,
                'region' => 'Cyberjaya'
            ];
        }

        return $items;
    }

    private function calculateSecurityItems($summary, $pricing)
    {
        $items = [];

        // Security Services
        $securityServices = [
            'kl_firewall_fortigate' => ['KL Cloud Firewall (Fortigate)', 'CSEC-VFW-DDT-FG'],
            'cyber_firewall_fortigate' => ['Cyberjaya Cloud Firewall (Fortigate)', 'CSEC-VFW-DDT-FG'],
            'kl_firewall_opnsense' => ['KL Cloud Firewall (OPNSense)', 'CSEC-VFW-DDT-OS'],
            'cyber_firewall_opnsense' => ['Cyberjaya Cloud Firewall (OPNSense)', 'CSEC-VFW-DDT-OS'],
            'kl_shared_waf' => ['KL Shared WAF', 'CSEC-WAF-SHR-HA'],
            'cyber_shared_waf' => ['Cyberjaya Shared WAF', 'CSEC-WAF-SHR-HA'],
            'kl_antivirus' => ['KL Anti-Virus', 'CSEC-EDR-NOD-STD'],
            'cyber_antivirus' => ['Cyberjaya Anti-Virus', 'CSEC-EDR-NOD-STD'],
            'kl_cloud_vulnerability' => ['KL Cloud Vulnerability Assessment', 'SECT-VAS-EIP-STD'],
            'cyber_cloud_vulnerability' => ['Cyberjaya Cloud Vulnerability Assessment', 'SECT-VAS-EIP-STD'],
        ];

        foreach ($securityServices as $field => [$name, $priceKey]) {
            if ($summary->$field > 0) {
                $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
                $unit = strpos($priceKey, 'WAF') !== false ? 'Mbps' : 'Unit';
                $region = strpos($field, 'kl_') === 0 ? 'Kuala Lumpur' : 'Cyberjaya';
                $items[] = [
                    'name' => $name,
                    'quantity' => $summary->$field,
                    'unit' => $unit,
                    'price_per_unit' => $price,
                    'total_price' => $summary->$field * $price,
                    'region' => $region
                ];
            }
        }

        
        return $items;
        
    }



    private function calculateManagedItems($securityService, $pricing)
{
    $items = [];
    if (!$securityService) {
        return $items;
    }

    // Map the display names to your pricing keys in config/pricing.php
    $map = [
        'Managed Operating System'   => 'CMNS-MOS-NOD-STD',
        'Managed Backup and Restore' => 'CMNS-MBR-NOD-STD',
        'Managed Patching'           => 'CMNS-MPT-NOD-STD',
        'Managed DR'                 => 'CMNS-MDR-NOD-STD',
    ];

    // Count selections for each region (you’re using 4 slots per region)
    foreach ($map as $label => $priceKey) {
        $price = $pricing[$priceKey]['price_per_unit'] ?? 0;

        $kl = 0; $cj = 0;
        for ($i = 1; $i <= 4; $i++) {
            $klField = "kl_managed_services_$i";
            $cjField = "cyber_managed_services_$i";

            if (($securityService->$klField ?? 'None') === $label) $kl++;
            if (($securityService->$cjField ?? 'None') === $label) $cj++;
        }

        if ($kl > 0) {
            $items[] = [
                'name' => $label,              // "Managed ..." → groups as "Managed Services" in your blade
                'unit' => 'VM',
                'region' => 'Kuala Lumpur',
                'quantity' => $kl,
                'price_per_unit' => $price,
                'total_price' => $kl * $price,
            ];
        }

        if ($cj > 0) {
            $items[] = [
                'name' => $label,
                'unit' => 'VM',
                'region' => 'Cyberjaya',
                'quantity' => $cj,
                'price_per_unit' => $price,
                'total_price' => $cj * $price,
            ];
        }
    }

    return $items;
}

private function calculateMonitoringItems($summary, $pricing)
{
    $items = [];
    if (!$summary) return $items;

    // Your config key for TCS inSight vMonitoring
    $price = $pricing['CMON-TIS-NOD-STD']['price_per_unit'] ?? 0;

    // KL
    if ((int)($summary->kl_insight_vmonitoring ?? 0) > 0) {
        $items[] = [
            // start name with "Monitoring" so your blade groups under "Monitoring Services"
            'name' => 'Monitoring TCS inSight vMonitoring',
            'unit' => 'Unit',
            'region' => 'Kuala Lumpur',
            'quantity' => (int)$summary->kl_insight_vmonitoring,
            'price_per_unit' => $price,
            'total_price' => (int)$summary->kl_insight_vmonitoring * $price,
        ];
    }

    // CJ
    if ((int)($summary->cyber_insight_vmonitoring ?? 0) > 0) {
        $items[] = [
            'name' => 'Monitoring TCS inSight vMonitoring',
            'unit' => 'Unit',
            'region' => 'Cyberjaya',
            'quantity' => (int)$summary->cyber_insight_vmonitoring,
            'price_per_unit' => $price,
            'total_price' => (int)$summary->cyber_insight_vmonitoring * $price,
        ];
    }

    return $items;
}

private function calculateBackupDrItems($summary, $pricing)
{
    $items = [];
    if (!$summary) return $items;

    // label => [KL field, CJ field, pricing key]
    $map = [
        'Backup Cloud Server Backup Service - Full Backup Capacity' => [
            'kl' => 'kl_full_backup_capacity',
            'cj' => 'cyber_full_backup_capacity',
            'priceKey' => 'CSBS-STRG-BCK-CSBSF',
        ],
        'Backup Cloud Server Backup Service - Incremental Backup Capacity' => [
            'kl' => 'kl_incremental_backup_capacity',
            'cj' => 'cyber_incremental_backup_capacity',
            'priceKey' => 'CSBS-STRG-BCK-CSBSI',
        ],
        'Backup Cloud Server Replication Service - Retention Capacity' => [
            'kl' => 'kl_replication_retention_capacity',
            'cj' => 'cyber_replication_retention_capacity',
            'priceKey' => 'CSBS-STRG-BCK-REPS',
        ],
    ];

    foreach ($map as $label => $def) {
        $price = $pricing[$def['priceKey']]['price_per_unit'] ?? 0;

        $klQty = (int)($summary->{$def['kl']} ?? 0);
        $cjQty = (int)($summary->{$def['cj']} ?? 0);

        if ($klQty > 0) {
            $items[] = [
                'name' => $label,          // starts with "Backup" → your blade groups under "Backup Services"
                'unit' => 'GB',
                'region' => 'Kuala Lumpur',
                'quantity' => $klQty,
                'price_per_unit' => $price,
                'total_price' => $klQty * $price,
            ];
        }

        if ($cjQty > 0) {
            $items[] = [
                'name' => $label,
                'unit' => 'GB',
                'region' => 'Cyberjaya',
                'quantity' => $cjQty,
                'price_per_unit' => $price,
                'total_price' => $cjQty * $price,
            ];
        }
    }

    return $items;
}


private function calculateLicenseItems($summary, $pricing)
{
    $items = [];
    if (!$summary) return $items;

    // Map summary fields → pricing keys (price_per_unit taken from pricing.php)
    $map = [
        // Microsoft
        'Microsoft Windows Server (Core Pack) - Standard'   => ['kl' => 'kl_windows_std',  'cj' => 'cyber_windows_std',  'key' => 'CLIC-WIN-COR-SRVSTD'],
        'Microsoft Windows Server (Core Pack) - Data Center'=> ['kl' => 'kl_windows_dc',   'cj' => 'cyber_windows_dc',   'key' => 'CLIC-WIN-COR-SRVDC'],
        'Microsoft Remote Desktop Services (SAL)'           => ['kl' => 'kl_rds',          'cj' => 'cyber_rds',          'key' => 'CLIC-WIN-USR-RDSSAL'],
        'Microsoft SQL (Web) (Core Pack)'                   => ['kl' => 'kl_sql_web',      'cj' => 'cyber_sql_web',      'key' => 'CLIC-WIN-COR-SQLWEB'],
        'Microsoft SQL (Standard) (Core Pack)'              => ['kl' => 'kl_sql_std',      'cj' => 'cyber_sql_std',      'key' => 'CLIC-WIN-COR-SQLSTD'],
        'Microsoft SQL (Enterprise) (Core Pack)'            => ['kl' => 'kl_sql_ent',      'cj' => 'cyber_sql_ent',      'key' => 'CLIC-WIN-COR-SQLENT'],

        // RHEL
        'RHEL (1-8vCPU)'                                    => ['kl' => 'kl_rhel_1_8',     'cj' => 'cyber_rhel_1_8',     'key' => 'CLIC-RHL-COR-8'],
        'RHEL (9-127vCPU)'                                  => ['kl' => 'kl_rhel_9_127',   'cj' => 'cyber_rhel_9_127',   'key' => 'CLIC-RHL-COR-127'],
    ];

    foreach ($map as $label => $def) {
        $price = $pricing[$def['key']]['price_per_unit'] ?? 0;

        $klQty = (int) ($summary->{$def['kl']} ?? 0);
        $cjQty = (int) ($summary->{$def['cj']} ?? 0);

        if ($klQty > 0) {
            $items[] = [
                'name' => $label,            // starts with "Microsoft" or "RHEL" → your blade groups nicely
                'unit' => 'Unit',
                'region' => 'Kuala Lumpur',
                'quantity' => $klQty,
                'price_per_unit' => $price,
                'total_price' => $klQty * $price,
            ];
        }
        if ($cjQty > 0) {
            $items[] = [
                'name' => $label,
                'unit' => 'Unit',
                'region' => 'Cyberjaya',
                'quantity' => $cjQty,
                'price_per_unit' => $price,
                'total_price' => $cjQty * $price,
            ];
        }
    }

    return $items;
}

//method calculator for ECS
    private function calculateComputeItems($summary, $pricing)
{
    $items = [];

    // Divide flavour mapping
    $flavours = explode(',', $summary->ecs_flavour_mapping);

    foreach ($flavours as $flavour) {
        $flavour = trim($flavour);

        if (!$flavour) continue;

        // find price based on naming
        $matched = collect($pricing)->first(function ($value) use ($flavour) {
            return strtolower($value['name'] ?? '') === strtolower($flavour);
        });

        if ($matched) {
            // Calculate total unit for KL and CJ
            $klQty = $this->getFlavourCountFromECS('Kuala Lumpur', $flavour, $summary->version_id);
            $cyberQty = $this->getFlavourCountFromECS('Cyberjaya', $flavour, $summary->version_id);

            $price = $matched['price_per_unit'] ?? 0;

            if ($klQty > 0) {
                $items[] = [
                    'name' => $matched['name'],
                    'quantity' => $klQty,
                    'unit' => 'Unit',
                    'price_per_unit' => $price,
                    'total_price' => $klQty * $price,
                    'region' => 'Kuala Lumpur'
                ];
            }

            if ($cyberQty > 0) {
                $items[] = [
                    'name' => $matched['name'],
                    'quantity' => $cyberQty,
                    'unit' => 'Unit',
                    'price_per_unit' => $price,
                    'total_price' => $cyberQty * $price,
                    'region' => 'Cyberjaya'
                ];
            }
        }
    }

    return $items;
}







    private function calculateProfessionalServices($summary, $pricing)
    {
        $items = [];

        // Professional Services
        if ($summary->mandays > 0) {
            $priceKey = $this->getMandayPriceKey($summary->mandays);
            $price = $pricing[$priceKey]['price_per_unit'] ?? 0;
            $items[] = [
                'name' => 'Professional Services',
                'quantity' => $summary->mandays,
                'unit' => 'Day',
                'price_per_unit' => $price,
                'total_price' => $summary->mandays * $price,
                'region' => 'Both'
            ];
        }

        return $items;
    }

    private function getBandwidthPriceKey($bandwidth)
    {
        if ($bandwidth <= 10) return 'CNET-BWS-CIA-10';
        if ($bandwidth <= 30) return 'CNET-BWS-CIA-30';
        if ($bandwidth <= 50) return 'CNET-BWS-CIA-50';
        if ($bandwidth <= 80) return 'CNET-BWS-CIA-80';
        return 'CNET-BWS-CIA-100';
    }

    private function getVPLLPriceKey($bandwidth)
    {
        if ($bandwidth <= 10) return 'CNET-PLL-SHR-10';
        if ($bandwidth <= 30) return 'CNET-PLL-SHR-30';
        if ($bandwidth <= 50) return 'CNET-PLL-SHR-50';
        if ($bandwidth <= 80) return 'CNET-PLL-SHR-80';
        if ($bandwidth <= 100) return 'CNET-PLL-SHR-100';
        return 'CNET-PLL-SHR-100';
    }

    private function getMandayPriceKey($mandays)
    {
        if ($mandays <= 0.5) return 'CPFS-PFS-MDY-0.5OTC';
        if ($mandays <= 1) return 'CPFS-PFS-MDY-1OTC';
        if ($mandays <= 3) return 'CPFS-PFS-MDY-3OTC';
        return 'CPFS-PFS-MDY-5OTC';
    }



private function getFlavourCountFromECS($regionName, $flavour, $versionId)
{
    return \App\Models\ECSConfiguration::where('version_id', $versionId)
        ->where('region', $regionName)
        ->where('ecs_flavour_mapping', $flavour)
        ->count();
}













 public function downloadRateCardPdf($versionId)
    {
        $version  = Version::with(['project.customer'])->findOrFail($versionId);
        $pricing  = config('pricing'); // seluruh pricing, termasuk rate_card_price_per_unit

        // Render PDF guna design sedia ada
        $pdf = Pdf::loadView('projects.ratecard_pdf', [
            'version' => $version,
            'pricing' => $pricing,
        ]);

        return $pdf->download('ratecard.pdf');
    }

}
