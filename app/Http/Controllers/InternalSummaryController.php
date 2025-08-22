<?php

namespace App\Http\Controllers;

use App\Models\InternalSummary;
use App\Models\Version;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InternalSummaryController extends Controller
{
    public function index($versionId)
    {
        $version = Version::with([
            'project.customer',
            'project.presale',
            'region'
        ])->findOrFail($versionId);

        /*$ecsData = $version->ecs_configuration;
        $ecsData = collect($ecsData);

    

        $project = $version->project;
        $region = $version->region;
        $solution_type = $version->solution_type ?? null;
        $securityService = $version->security_service;*/


        // --- PRECHECK ---
$project         = $version->project ?? null;
$region          = $version->region ?? null;
$solution_type   = $version->solution_type ?? null;
$securityService = $version->security_service ?? null;
$ecsData         = collect($version->ecs_configuration ?? []);

$missing = [];
if (!$solution_type)   $missing[] = 'Solution Type';
if (!$project)         $missing[] = 'Project';
if (!$region)          $missing[] = 'Professional Services (Region)';
if (!$securityService) $missing[] = 'Security Services';
if ($ecsData->isEmpty()) $missing[] = 'ECS & Backup (at least 1 VM/row)';
/* Tambah apa2 lain yang kau nak enforce di sini */

// Kalau ada yang missing → terus render view dengan alert,
// dan SKIP semua kiraan di bawah.
if (!empty($missing)) {
    return view('projects.security_service.internal_summary', [
        'version'              => $version,
        'project'              => $project,
        'solution_type'        => $solution_type,
        'summary'              => null,               // penting: biar null supaya blade tak akses property
        'klManagedServices'    => [],
        'cyberManagedServices' => [],
        'nonStandardItems'     => $version->non_standard_items ?? collect(),
        'ecsSummary'           => [],
        'licenseSummary'       => [],
        'klEvs'                => 0,
        'cyberEvs'             => 0,
        'klEvsDR'              => 0,
        'cyberEvsDR'           => 0,
        'usedFlavours'         => collect(),
        'flavourDetails'       => collect(),
        'drCountsKL'           => collect(),
        'drCountsCJ'           => collect(),
        'missing'              => $missing,          // <-- hantar ke blade
    ]);
}


        







        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        $nonStandardItems = $version->non_standard_items;

        $drLoc = trim((string)($region->dr_location ?? ''));
$isKL  = strcasecmp($drLoc, 'Kuala Lumpur') === 0;
$isCJ  = strcasecmp($drLoc, 'Cyberjaya') === 0;


        


    /*$flavourMap = \App\Models\ECSFlavour::all()
    ->keyBy(fn($item) => strtolower($item->flavour_name))



    
    ->map(function ($item) {
        return [
            'vcpu' => $item->vcpu,
            'windows_license_count' => $item->windows_license_count,
            'mssql' => $item->microsoft_sql_license_count,
            'rhel' => $item->rhel,
        ];
    });*/

    $flavourMap = \App\Models\ECSFlavour::all()
    // KEY MUST be trimmed + lowercased, or lookups will miss
    ->keyBy(function ($item) {
        return strtolower(trim((string) $item->flavour_name));
    })
    // store only the numbers we need, as ints
    ->map(function ($item) {
        return [
            'vcpu'                  => (int) $item->vcpu,
            'windows_license_count' => (int) $item->windows_license_count,
            // support either column name: microsoft_sql_license_count OR mssql
            'mssql'                 => (int) (
                $item->microsoft_sql_license_count
                ?? $item->mssql
                ?? 0
            ),
            'rhel'                  => (int) ($item->rhel ?? 0),
        ];
    });





/*$ecsData = $ecsData->map(function ($row) {
    $row->ecs_flavour_mapping = strtolower(trim($row->ecs_flavour_mapping ?? ''));
    $row->license_operating_system = trim($row->license_operating_system ?? '');
    $row->license_microsoft_sql = trim($row->license_microsoft_sql ?? '');
    $row->license_rds_license = trim($row->license_rds_license ?? '');
    return $row;
});*/







$ecsData = collect($version->ecs_configuration)->map(function ($row) {
    // normalise strings
    $row->region          = trim((string)($row->region ?? ''));
    $row->dr_activation   = strtoupper(trim((string)($row->dr_activation ?? 'No'))) === 'YES' ? 'Yes' : 'No';
    $row->csdr_needed     = strtoupper(trim((string)($row->csdr_needed ?? 'No'))) === 'YES' ? 'Yes' : 'No';
    $row->seed_vm_required= strtoupper(trim((string)($row->seed_vm_required ?? 'No'))) === 'YES' ? 'Yes' : 'No';


      $row->required           = strtoupper(trim((string)($row->required ?? 'No'))) === 'YES' ? 'Yes' : 'No';
    $row->csbs_standard_policy = preg_replace('/\s+/', ' ', trim((string)($row->csbs_standard_policy ?? '')));

    // keep your existing normalisations
    $row->ecs_flavour_mapping   = strtolower(trim((string)($row->ecs_flavour_mapping ?? '')));
    $row->license_operating_system = trim((string)($row->license_operating_system ?? ''));
    $row->license_microsoft_sql   = trim((string)($row->license_microsoft_sql ?? ''));
    $row->license_rds_license     = trim((string)($row->license_rds_license ?? ''));
    return $row;
});


// Pengiraan Full Backup Capacity (GB)
$klFullBackupCapacity = $ecsData->where('region', 'Kuala Lumpur')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->sum(function ($item) {
        return ($item->csbs_initial_data_size ?? 0) * (($item->full_backup_total_retention_full_copies ?? 0) + 1);
    });

$cyberFullBackupCapacity = $ecsData->where('region', 'Cyberjaya')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->sum(function ($item) {
        return ($item->csbs_initial_data_size ?? 0) * (($item->full_backup_total_retention_full_copies ?? 0) + 1);
    });

// Pengiraan Incremental Backup Capacity (GB)
$klIncrementalBackupCapacity = $ecsData->where('region', 'Kuala Lumpur')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->sum(function ($item) {
        return ceil(($item->csbs_estimated_incremental_data_change ?? $item->csbs_incremental_change ?? 0) * ($item->incremental_backup_total_retention_incremental_copies ?? 0));
    });

$cyberIncrementalBackupCapacity = $ecsData->where('region', 'Cyberjaya')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->sum(function ($item) {
        return ceil(($item->csbs_estimated_incremental_data_change ?? $item->csbs_incremental_change ?? 0) * ($item->incremental_backup_total_retention_incremental_copies ?? 0));
    });

// Pengiraan Replication Retention Capacity (GB)
$klReplicationRetentionCapacity = $ecsData->where('region', 'Cyberjaya')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->where('required', 'Yes')
    ->sum(function ($item) {
        return ($item->csbs_total_storage ?? 0) + ($item->additional_storage ?? 0);
    });

$klReplicationRetentionCapacity += $ecsData->where('region', 'Cyberjaya')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->where('required', 'No')
    ->where('dr_activation', 'Yes')
    ->sum(function ($item) {
        return ($item->csbs_total_storage ?? 0) + ($item->additional_storage ?? 0);
    });

$cyberReplicationRetentionCapacity = $ecsData->where('region', 'Kuala Lumpur')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->where('required', 'Yes')
    ->sum(function ($item) {
        return ($item->csbs_total_storage ?? 0) + ($item->additional_storage ?? 0);
    });

$cyberReplicationRetentionCapacity += $ecsData->where('region', 'Kuala Lumpur')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->where('required', 'No')
    ->where('dr_activation', 'Yes')
    ->sum(function ($item) {
        return ($item->csbs_total_storage ?? 0) + ($item->additional_storage ?? 0);
    });



// 4. Cold DR Days (Days)
$coldDrDaysKL = 0;
$coldDrDaysCJ = 0;

if ($ecsData->where('dr_activation', 'Yes')->count() > 0) {
    $coldDrDaysKL = $region->dr_location === 'Kuala Lumpur' ? $region->kl_dr_activation_days : 0;
    $coldDrDaysCJ = $region->dr_location === 'Cyberjaya' ? $region->cyber_dr_activation_days : 0;
}




// 5. Cold DR – Seeding VM (Unit)
$coldDrSeedingVMKL = 0;
$coldDrSeedingVMCJ = 0;

if ($region->dr_location === 'Kuala Lumpur') {
    $coldDrSeedingVMKL = $ecsData->where('csbs_standard_policy', '!=', 'No Backup')
        ->where('dr_activation', 'Yes')
        ->where('region', 'Cyberjaya')
        ->where('seed_vm_required', 'Yes')
        ->count();
} else if ($region->dr_location === 'Cyberjaya') {
    $coldDrSeedingVMCJ = $ecsData->where('csbs_standard_policy', '!=', 'No Backup')
        ->where('dr_activation', 'Yes')
        ->where('region', 'Kuala Lumpur')
        ->where('seed_vm_required', 'Yes')
        ->count();
}

// 6. Cloud Server Disaster Recovery Storage (GB)
$drStorageKL = 0;
$drStorageCJ = 0;

if ($region->dr_location === 'Kuala Lumpur') {
    $drStorageKL = $ecsData->where('region', 'Cyberjaya')
        ->where('csdr_needed', 'Yes')
        ->sum('csbs_total_storage');
} else if ($region->dr_location === 'Cyberjaya') {
    $drStorageCJ = $ecsData->where('region', 'Kuala Lumpur')
        ->where('csdr_needed', 'Yes')
        ->sum('csbs_total_storage');
}


// Cloud Server Disaster Recovery Replication (Unit)
/*
$drReplicationKL = 0;
$drReplicationCJ = 0;

// KL: Kira bilangan VM yang `CSDR Needed? = Yes` di Cyberjaya
if ($region->dr_location === 'Kuala Lumpur') {
    $drReplicationKL = $ecsData->where('region', 'Cyberjaya')
        ->where('csdr_needed', 'Yes')
        ->count();
}

// CJ: Kira bilangan VM yang `CSDR Needed? = Yes` di Kuala Lumpur
else if ($region->dr_location === 'Cyberjaya') {
    $drReplicationCJ = $ecsData->where('region', 'Kuala Lumpur')
        ->where('csdr_needed', 'Yes')
        ->count();
}









// 8. Cloud Server Disaster Recovery Days (DR Declaration)*/





// ===== DR rows to match Excel =====
// (7) Replication: IF(D7="Kuala Lumpur", COUNTIF(CSDR Needed?,"Yes"), 0)
//     -> Count ALL "Yes" (no region filter), put into chosen destination column only.
$yesCountAll = $ecsData->where('csdr_needed', 'Yes')->count();

$drReplicationKL = $isKL ? $yesCountAll : 0;
$drReplicationCJ = $isCJ ? $yesCountAll : 0;

// (8) DR Declaration Days: IF([replication]=0, 0, ColdDRDays)
$drDeclarationKL = $drReplicationKL > 0 ? (int)($region->kl_dr_activation_days ?? 0)    : 0;
$drDeclarationCJ = $drReplicationCJ > 0 ? (int)($region->cyber_dr_activation_days ?? 0) : 0;

// (9) Managed Service – Per Day: same as (7)
$drManagedServiceKL = $drReplicationKL;
$drManagedServiceCJ = $drReplicationCJ;

/*$drLicense = $this->getDRLicenseSummary(
    $ecsData, $flavourMap, $region->dr_location, $drDeclarationKL, $drDeclarationCJ
);*/


// ===DR License summary==
$drLic = $this->getDrLicenseSummary(
    $ecsData,
    $flavourMap,
    $isKL,
    $isCJ,
    (int)$coldDrDaysKL,
    (int)$coldDrDaysCJ
);


// ===== DR Elastic Volume Service (EVS) — During DR Activation =====
// Destinasi = site lawan + gandaan 2 (168 vs 84)
$klEvsDR = $ecsData
    ->where('region', 'Cyberjaya')      // asal CJ → DR kat KL
    ->where('dr_activation', 'Yes')
    ->sum(fn ($i) => ($i->storage_system_disk ?? 0) + ($i->storage_data_disk ?? 0)) * 2;

$cyberEvsDR = $ecsData
    ->where('region', 'Kuala Lumpur')   // asal KL → DR kat CJ
    ->where('dr_activation', 'Yes')
    ->sum(fn ($i) => ($i->storage_system_disk ?? 0) + ($i->storage_data_disk ?? 0)) * 2;



// ========== DR FLAVOUR COUNTS — FLIP DESTINATION ==========
// KL column = kira VM asal di CJ
$drCountsKL = $ecsData
    ->where('region', 'Cyberjaya')
    ->where('dr_activation', 'Yes')
    ->groupBy('ecs_flavour_mapping')        // base flavour (tanpa .dr)
    ->map->count();                          // contoh: ['m3.micro' => 1, 'c3.4xlarge' => 2]

// CJ column = kira VM asal di KL
$drCountsCJ = $ecsData
    ->where('region', 'Kuala Lumpur')
    ->where('dr_activation', 'Yes')
    ->groupBy('ecs_flavour_mapping')
    ->map->count();

// Senarai flavour (base) yang terlibat di mana-mana destinasi
$usedFlavours = $drCountsKL->keys()->merge($drCountsCJ->keys())->unique()->sort();

// Ambil details untuk VARIAN .dr (bukan base)
$drNames = $usedFlavours->map(fn($f) => $f . '.dr');

$flavourDetails = \App\Models\ECSFlavour::whereIn('flavour_name', $drNames->toArray())
    ->get()
    ->keyBy('flavour_name')
    ->map(function ($flavour) {
        return [
            'vcpu' => $flavour->vcpu,
            'vram' => $flavour->vram,
            'type' => $flavour->type,
            'generation' => $flavour->generation,
            'memory_label' => $flavour->memory_label,
            'windows_license_count' => $flavour->windows_license_count,
            'rhel' => $flavour->rhel,
            'dr' => $flavour->dr,
            'pin' => $flavour->pin,
            'gpu' => $flavour->gpu,
            'ddh' => $flavour->ddh,
            'mssql' => $flavour->mssql,
        ];
    });

// PASS ke view:
// compact('klEvs','cyberEvs','usedFlavours','flavourDetails','drCountsKL','drCountsCJ')





$licenseSummary = $this->getLicenseSummary($ecsData, $flavourMap);


    $ecsSummary = $ecsData->groupBy(['region', 'ecs_flavour_mapping'])
        ->map(function ($regionGroup) {
            return $regionGroup->map->count();
        });

    // Format: ['Kuala Lumpur' => ['m3.large' => 2], 'Cyberjaya' => ['m3.large' => 1, 'r3.large' => 1]]
    $ecsSummary = $ecsSummary->toArray();



        $klEvs = $ecsData
    ->where('region', 'Kuala Lumpur')
    ->sum(function ($item) {
        return ($item->storage_system_disk ?? 0) + ($item->storage_data_disk ?? 0);
    });

$cyberEvs = $ecsData
    ->where('region', 'Cyberjaya')
    ->sum(function ($item) {
        return ($item->storage_system_disk ?? 0) + ($item->storage_data_disk ?? 0);
    });



    $klSnapshot = $ecsData->where('region', 'Kuala Lumpur')->sum(function ($item) {
    $base = ($item->snapshot_copies ?? 0) == 0 ? 0 : 
        ($item->snapshot_copies * (($item->storage_system_disk ?? 0) + ($item->storage_data_disk ?? 0)));
    return $base + ($item->additional_capacity ?? 0);
});

$cyberSnapshot = $ecsData->where('region', 'Cyberjaya')->sum(function ($item) {
    $base = ($item->snapshot_copies ?? 0) == 0 ? 0 : 
        ($item->snapshot_copies * (($item->storage_system_disk ?? 0) + ($item->storage_data_disk ?? 0)));
    return $base + ($item->additional_capacity ?? 0);
});


$klImage = $ecsData->where('region', 'Kuala Lumpur')->sum(function ($item) {
    return ($item->image_copies ?? 0) == 0 ? 0 :
        ($item->image_copies * (($item->storage_system_disk ?? 0) + ($item->storage_data_disk ?? 0)));
});

$cyberImage = $ecsData->where('region', 'Cyberjaya')->sum(function ($item) {
    return ($item->image_copies ?? 0) == 0 ? 0 :
        ($item->image_copies * (($item->storage_system_disk ?? 0) + ($item->storage_data_disk ?? 0)));
});


// ================= DR Network & Security =================

// vPLL
$klDrVpll     = $isKL ? (int) ceil(($region->kl_db_bandwidth ?? 0) / 10) : 0;
$cyberDrVpll  = $isCJ ? (int) ceil(($region->cyber_db_bandwidth ?? 0) / 10) : 0;

// DR Elastic IP (Unit Per Day) – ikut destinasi DR
$klDrEip      = $isKL ? (int) ($region->kl_elastic_ip_dr ?? 0) : 0;
$cyberDrEip   = $isCJ ? (int) ($region->cyber_elastic_ip_dr ?? 0) : 0;

// DR Bandwidth vs Anti-DDoS (hanya salah satu ikut dr_bandwidth_type)
$klDrBw = $cyberDrBw = $klDrBwAnti = $cyberDrBwAnti = 0;

if ($isKL) {
    if (($region->dr_bandwidth_type ?? 'bandwidth') === 'bandwidth') {
        $klDrBw = (int) ($region->kl_db_bandwidth ?? 0);
    } else { // anti-ddos
        $klDrBwAnti = (int) ($region->kl_db_bandwidth ?? 0);
    }
}
if ($isCJ) {
    if (($region->dr_bandwidth_type ?? 'bandwidth') === 'bandwidth') {
        $cyberDrBw = (int) ($region->cyber_db_bandwidth ?? 0);
    } else { // anti-ddos
        $cyberDrBwAnti = (int) ($region->cyber_db_bandwidth ?? 0);
    }
}

// DR Cloud Firewall – kira berapa tier pilih setiap jenis; letak pada destinasi DR sahaja
$fortiCount = (int) (($region->tier1_dr_security === 'fortigate') + ($region->tier2_dr_security === 'fortigate'));
$opnCount   = (int) (($region->tier1_dr_security === 'opn_sense') + ($region->tier2_dr_security === 'opn_sense'));

$klDrForti    = $isKL ? $fortiCount : 0;
$cyberDrForti = $isCJ ? $fortiCount : 0;
$klDrOpn      = $isKL ? $opnCount   : 0;
$cyberDrOpn   = $isCJ ? $opnCount   : 0;













        

$summary = InternalSummary::updateOrCreate(
    ['version_id' => $version->id],
    [
                'id' => Str::uuid(),
                'version_id' => $version->id,
                'project_id' => $project->id ?? null,
                'customer_id' => $project->customer_id ?? null,
                'presale_id' => $project->presale_id ?? null,

                // KL fields
                'kl_bandwidth' => $region->kl_bandwidth,
                'kl_bandwidth_with_antiddos' => $region->kl_bandwidth_with_antiddos,
                'kl_included_elastic_ip' => $region->kl_included_elastic_ip,
                'kl_elastic_ip' => $region->kl_elastic_ip,
                'kl_elastic_load_balancer' => $region->kl_elastic_load_balancer,
                'kl_direct_connect_virtual' => $region->kl_direct_connect_virtual,
                'kl_l2br_instance' => $region->kl_l2br_instance,
                'kl_virtual_private_leased_line' => $region->kl_virtual_private_leased_line,
                'kl_vpll_l2br' => $region->kl_vpll_l2br,
                'kl_nat_gateway_small' => $region->kl_nat_gateway_small,
                'kl_nat_gateway_medium' => $region->kl_nat_gateway_medium,
                'kl_nat_gateway_large' => $region->kl_nat_gateway_large,
                'kl_nat_gateway_xlarge' => $region->kl_nat_gateway_xlarge,

                // Cyber fields
                'cyber_bandwidth' => $region->cyber_bandwidth,
                'cyber_bandwidth_with_antiddos' => $region->cyber_bandwidth_with_antiddos,
                'cyber_included_elastic_ip' => $region->cyber_included_elastic_ip,
                'cyber_elastic_ip' => $region->cyber_elastic_ip,
                'cyber_elastic_load_balancer' => $region->cyber_elastic_load_balancer,
                'cyber_direct_connect_virtual' => $region->cyber_direct_connect_virtual,
                'cyber_l2br_instance' => $region->cyber_l2br_instance,
                'cyber_nat_gateway_small' => $region->cyber_nat_gateway_small,
                'cyber_nat_gateway_medium' => $region->cyber_nat_gateway_medium,
                'cyber_nat_gateway_large' => $region->cyber_nat_gateway_large,
                'cyber_nat_gateway_xlarge' => $region->cyber_nat_gateway_xlarge,


             
        'kl_scalable_file_service' => $region->kl_scalable_file_service,
        'cyber_scalable_file_service' => $region->cyber_scalable_file_service,
        'kl_object_storage_service' => $region->kl_object_storage_service,
        'cyber_object_storage_service' => $region->cyber_object_storage_service,

        // EVS & Snapshot 
       'kl_evs' => $klEvs,
'cyber_evs' => $cyberEvs,
        'kl_snapshot_storage' => null,
        'cyber_snapshot_storage' => null,

        'mandays' => $region->mandays,
        'kl_license_count' => $region->kl_license_count,
        'cyber_license_count' => $region->cyber_license_count,
        'kl_duration' => $region->kl_duration,
        'cyber_duration' => $region->cyber_duration,
          // Monitoring
        'kl_security_advanced'     => $securityService->kl_security_advanced,
        'cyber_security_advanced'  => $securityService->cyber_security_advanced,
      


        'kl_insight_vmonitoring'   => $securityService->kl_insight_vmonitoring === 'Yes' ? 1 : 0,
'cyber_insight_vmonitoring'=> $securityService->cyber_insight_vmonitoring === 'Yes' ? 1 : 0,


        // Security Service
        'kl_cloud_vulnerability'   => $securityService->kl_cloud_vulnerability,
        'cyber_cloud_vulnerability'=> $securityService->cyber_cloud_vulnerability,

        // Cloud Security
        'kl_firewall_fortigate'    => $securityService->kl_firewall_fortigate,
        'cyber_firewall_fortigate' => $securityService->cyber_firewall_fortigate,
        'kl_firewall_opnsense'     => $securityService->kl_firewall_opnsense,
        'cyber_firewall_opnsense'  => $securityService->cyber_firewall_opnsense,
        'kl_shared_waf'            => $securityService->kl_shared_waf,
        'cyber_shared_waf'         => $securityService->cyber_shared_waf,
        'kl_antivirus'             => $securityService->kl_antivirus,
        'cyber_antivirus'          => $securityService->cyber_antivirus,

        // Other Services
        'kl_gslb'                  => $securityService->kl_gslb,
        'cyber_gslb'               => $securityService->cyber_gslb,

        // Managed Services
        'kl_managed_services_1'    => $securityService->kl_managed_services_1,
        'kl_managed_services_2'    => $securityService->kl_managed_services_2,
        'kl_managed_services_3'    => $securityService->kl_managed_services_3,
        'kl_managed_services_4'    => $securityService->kl_managed_services_4,
        'cyber_managed_services_1' => $securityService->cyber_managed_services_1,
        'cyber_managed_services_2' => $securityService->cyber_managed_services_2,
        'cyber_managed_services_3' => $securityService->cyber_managed_services_3,
        'cyber_managed_services_4' => $securityService->cyber_managed_services_4,


        'kl_snapshot_storage' => $klSnapshot,
'cyber_snapshot_storage' => $cyberSnapshot,
'kl_image_storage' => $klImage,
'cyber_image_storage' => $cyberImage,
 'ecs_flavour_summary' => $ecsSummary,


        
        //'ecs_flavour_mapping' => $ecsData->pluck('ecs_flavour_mapping')->first(),
        'ecs_flavour_mapping' => $ecsData->pluck('ecs_flavour_mapping')->implode(','),
'ecs_vcpu' => $ecsData->sum('ecs_vcpu'),
'ecs_vram' => $ecsData->sum('ecs_vram'),

 'kl_full_backup_capacity' => $klFullBackupCapacity,
        'cyber_full_backup_capacity' => $cyberFullBackupCapacity,
        'kl_incremental_backup_capacity' => $klIncrementalBackupCapacity,
        'cyber_incremental_backup_capacity' => $cyberIncrementalBackupCapacity,
        'kl_replication_retention_capacity' => $klReplicationRetentionCapacity,
        'cyber_replication_retention_capacity' => $cyberReplicationRetentionCapacity,


        // Cold DR Days
        'kl_cold_dr_days' => $coldDrDaysKL,
        'cyber_cold_dr_days' => $coldDrDaysCJ,

        // Cold DR – Seeding VM
        'kl_cold_dr_seeding_vm' => $coldDrSeedingVMKL,
        'cyber_cold_dr_seeding_vm' => $coldDrSeedingVMCJ,

        // Cloud Server Disaster Recovery Storage
        'kl_dr_storage' => $drStorageKL,
        'cyber_dr_storage' => $drStorageCJ,

        // Cloud Server Disaster Recovery Replication
        'kl_dr_replication' => $drReplicationKL,
        'cyber_dr_replication' => $drReplicationCJ,

        // Cloud Server Disaster Recovery Days (DR Declaration)
        'kl_dr_declaration' => $drDeclarationKL,
        'cyber_dr_declaration' => $drDeclarationCJ,

        // Cloud Server Disaster Recovery Managed Service – Per Day
        'kl_dr_managed_service' => $drManagedServiceKL,
        'cyber_dr_managed_service' => $drManagedServiceCJ,






        'kl_dr_vpll' => $klDrVpll,
        'cyber_dr_vpll' => $cyberDrVpll,
        'kl_dr_elastic_ip' => $klDrEip,
        'cyber_dr_elastic_ip' => $cyberDrEip,
        'kl_dr_bandwidth' => $klDrBw,
        'cyber_dr_bandwidth' => $cyberDrBw,
        'kl_dr_bandwidth_antiddos' => $klDrBwAnti,
        'cyber_dr_bandwidth_antiddos' => $cyberDrBwAnti,
        'kl_dr_firewall_fortigate' => $klDrForti,
        'cyber_dr_firewall_fortigate' => $cyberDrForti,
        'kl_dr_firewall_opnsense' => $klDrOpn,
        'cyber_dr_firewall_opnsense' => $cyberDrOpn,


 // License Summary
'kl_windows_std' => $licenseSummary['windows_std']['Kuala Lumpur'],
'cyber_windows_std' => $licenseSummary['windows_std']['Cyberjaya'],
'kl_windows_dc' => $licenseSummary['windows_dc']['Kuala Lumpur'],
'cyber_windows_dc' => $licenseSummary['windows_dc']['Cyberjaya'],
'kl_rds' => $licenseSummary['rds']['Kuala Lumpur'],
'cyber_rds' => $licenseSummary['rds']['Cyberjaya'],
'kl_sql_web' => $licenseSummary['sql_web']['Kuala Lumpur'],
'cyber_sql_web' => $licenseSummary['sql_web']['Cyberjaya'],
'kl_sql_std' => $licenseSummary['sql_std']['Kuala Lumpur'],
'cyber_sql_std' => $licenseSummary['sql_std']['Cyberjaya'],
'kl_sql_ent' => $licenseSummary['sql_ent']['Kuala Lumpur'],
'cyber_sql_ent' => $licenseSummary['sql_ent']['Cyberjaya'],
'kl_rhel_1_8' => $licenseSummary['rhel_1_8']['Kuala Lumpur'],
'cyber_rhel_1_8' => $licenseSummary['rhel_1_8']['Cyberjaya'],
'kl_rhel_9_127' => $licenseSummary['rhel_9_127']['Kuala Lumpur'],
'cyber_rhel_9_127' => $licenseSummary['rhel_9_127']['Cyberjaya'],


// --- DR Licenses (auto) ---
'kl_dr_license_months' => $drLic['kl']['license_months'],
'cyber_dr_license_months' => $drLic['cj']['license_months'],

'kl_dr_windows_std' => $drLic['kl']['windows_std'],
'cyber_dr_windows_std' => $drLic['cj']['windows_std'],

'kl_dr_windows_dc' => $drLic['kl']['windows_dc'],
'cyber_dr_windows_dc' => $drLic['cj']['windows_dc'],

'kl_dr_rds' => $drLic['kl']['rds'],
'cyber_dr_rds' => $drLic['cj']['rds'],

'kl_dr_sql_web' => $drLic['kl']['sql_web'],
'cyber_dr_sql_web' => $drLic['cj']['sql_web'],

'kl_dr_sql_std' => $drLic['kl']['sql_std'],
'cyber_dr_sql_std' => $drLic['cj']['sql_std'],

'kl_dr_sql_ent' => $drLic['kl']['sql_ent'],
'cyber_dr_sql_ent' => $drLic['cj']['sql_ent'],

'kl_dr_rhel_1_8' => $drLic['kl']['rhel_1_8'],
'cyber_dr_rhel_1_8' => $drLic['cj']['rhel_1_8'],

'kl_dr_rhel_9_127' => $drLic['kl']['rhel_9_127'],
'cyber_dr_rhel_9_127' => $drLic['cj']['rhel_9_127'],




            ]
        );


        $managedTypes = [
    'Managed Operating System',
    'Managed Backup and Restore',
    'Managed Patching',
    'Managed DR',
];

// Count KL
$klManagedServices = [
    'Managed Operating System' => 0,
    'Managed Backup and Restore' => 0,
    'Managed Patching' => 0,
    'Managed DR' => 0,
];

foreach (range(1, 4) as $i) {
    $service = $securityService->{'kl_managed_services_' . $i};
    if (in_array($service, $managedTypes)) {
        $klManagedServices[$service]++;
    }
}

// Count Cyber
$cyberManagedServices = [
    'Managed Operating System' => 0,
    'Managed Backup and Restore' => 0,
    'Managed Patching' => 0,
    'Managed DR' => 0,
];

foreach (range(1, 4) as $i) {
    $service = $securityService->{'cyber_managed_services_' . $i};
    if (in_array($service, $managedTypes)) {
        $cyberManagedServices[$service]++;
    }
}




        return view('projects.security_service.internal_summary', compact(
            'version',
            'project',
            'solution_type',
            'summary',
             'klManagedServices',
    'cyberManagedServices',
    'nonStandardItems',
      'ecsSummary',
       'licenseSummary',
       'klEvs',
    'cyberEvs',
     'klEvsDR','cyberEvsDR',  

 'usedFlavours','flavourDetails','drCountsKL','drCountsCJ'
  
     
        ));
    }





private function getLicenseSummary($ecsData, $flavourMap)
{

   

    
    $regions = ['Kuala Lumpur', 'Cyberjaya'];

    $summary = [];

    //dd($ecsData->pluck('ecs_flavour_mapping', 'id'));


    foreach ($regions as $region) {
      
            $summary['windows_std'][$region] = $ecsData->where('region', $region)
    ->where('license_operating_system', 'Microsoft Windows Std')
    ->sum(fn($row) => isset($flavourMap[$row->ecs_flavour_mapping]) 
        ? $flavourMap[$row->ecs_flavour_mapping]['windows_license_count'] 
        : 0);

$summary['windows_dc'][$region] = $ecsData->where('region', $region)
    ->where('license_operating_system', 'Microsoft Windows DC')
    ->sum(fn($row) => isset($flavourMap[$row->ecs_flavour_mapping]) 
        ? $flavourMap[$row->ecs_flavour_mapping]['windows_license_count'] 
        : 0);

$summary['rds'][$region] = $ecsData->where('region', $region)
    ->filter(fn($row) => str_starts_with($row->license_rds_license ?? '', 'Microsoft'))
    ->count();

$summary['sql_web'][$region] = $ecsData->where('region', $region)
    ->where('license_operating_system', '!=', 'Linux')
    ->where('license_microsoft_sql', 'Web')
    ->sum(fn($row) => isset($flavourMap[$row->ecs_flavour_mapping]) 
        ? $flavourMap[$row->ecs_flavour_mapping]['mssql'] 
        : 0);

$summary['sql_std'][$region] = $ecsData->where('region', $region)
    ->where('license_microsoft_sql', 'Standard')
    ->sum(fn($row) => isset($flavourMap[$row->ecs_flavour_mapping]) 
        ? $flavourMap[$row->ecs_flavour_mapping]['mssql'] 
        : 0);

$summary['sql_ent'][$region] = $ecsData->where('region', $region)
    ->where('license_operating_system', '!=', 'Linux')
    ->where('license_microsoft_sql', 'Enterprise')
    ->sum(fn($row) => isset($flavourMap[$row->ecs_flavour_mapping]) 
        ? $flavourMap[$row->ecs_flavour_mapping]['mssql'] 
        : 0);

$summary['rhel_1_8'][$region] = $ecsData->where('region', $region)
    ->where('license_operating_system', 'Red Hat Enterprise Linux')
    ->sum(function ($row) use ($flavourMap) {
        $vcpu = isset($flavourMap[$row->ecs_flavour_mapping]) 
            ? $flavourMap[$row->ecs_flavour_mapping]['vcpu'] 
            : 0;
        return ($vcpu > 0 && $vcpu < 9) ? $vcpu : 0;
    });

$summary['rhel_9_127'][$region] = $ecsData->where('region', $region)
    ->where('license_operating_system', 'Red Hat Enterprise Linux')
    ->sum(function ($row) use ($flavourMap) {
        $vcpu = isset($flavourMap[$row->ecs_flavour_mapping]) 
            ? $flavourMap[$row->ecs_flavour_mapping]['vcpu'] 
            : 0;
        return $vcpu >= 9 ? $vcpu : 0;
    });



    }

    return $summary;
}

// ==== DRLicense summary ===
private function getDrLicenseSummary(
    \Illuminate\Support\Collection $ecsData,
    \Illuminate\Support\Collection|array $flavourMap,
    bool $isKL,
    bool $isCJ,
    int $coldDrDaysKL,
    int $coldDrDaysCJ
): array {
    // Destination (where DR will run) and Source (original site)
    $destSite   = $isKL ? 'Kuala Lumpur' : ($isCJ ? 'Cyberjaya' : null);
    $sourceSite = $isKL ? 'Cyberjaya'    : ($isCJ ? 'Kuala Lumpur' : null);

    // If DR location not chosen → zeros
    if (!$destSite || !$sourceSite) {
        $zero = array_fill_keys([
            'license_months','windows_std','windows_dc','rds',
            'sql_web','sql_std','sql_ent','rhel_1_8','rhel_9_127'
        ], 0);
        return ['kl' => $zero, 'cj' => $zero];
    }

    // Helpers
    $mapArr = is_array($flavourMap) ? $flavourMap : $flavourMap->toArray();
    $getInMap = function ($flv, $key) use ($mapArr) {
        $base = strtolower(trim((string)$flv));
        $dr   = $base . '.dr';
        if (isset($mapArr[$base]) && array_key_exists($key, $mapArr[$base])) return (int)$mapArr[$base][$key];
        if (isset($mapArr[$dr])   && array_key_exists($key, $mapArr[$dr]))   return (int)$mapArr[$dr][$key];
        return 0;
    };
    $getVcpu   = fn($flv) => $getInMap($flv, 'vcpu');
    $isWinStd  = fn($r) => strcasecmp($r->license_operating_system, 'Microsoft Windows Std') === 0;
    $isWinDC   = fn($r) => strcasecmp($r->license_operating_system, 'Microsoft Windows DC')  === 0;
    $isWinOS   = fn($r) => stripos((string)$r->license_operating_system, 'windows') !== false;
    $isNotLinux= fn($r) => strcasecmp($r->license_operating_system, 'Linux') !== 0;
    $isRhel    = fn($r) => strcasecmp($r->license_operating_system, 'Red Hat Enterprise Linux') === 0;

    // Pick ONLY VMs from the source site that have DR Activation = Yes
    $vm = $ecsData->filter(function ($r) use ($sourceSite) {
        return strcasecmp((string)$r->region, $sourceSite) === 0
            && (string)$r->dr_activation === 'Yes';
    });

    // License Month (goes to DESTINATION column)
    $licenseMonths = $isKL
        ? ($coldDrDaysKL > 0 ? (int)ceil($coldDrDaysKL / 30) : 0)
        : ($coldDrDaysCJ > 0 ? (int)ceil($coldDrDaysCJ / 30) : 0);

    // Counts (belong to the SOURCE column)
    $winStd = $vm->filter($isWinStd)
                 ->sum(fn($r) => $getInMap($r->ecs_flavour_mapping, 'windows_license_count'));

    $winDc  = $vm->filter($isWinDC)
                 ->sum(fn($r) => $getInMap($r->ecs_flavour_mapping, 'windows_license_count'));

    // RDS: count Windows VMs that selected an RDS SAL (string flag starts with "Microsoft")
    $rds    = $vm->filter($isWinOS)
                 ->filter(fn($r) => str_starts_with((string)($r->license_rds_license ?? ''), 'Microsoft'))
                 ->count();

    $sqlWeb = $vm->filter(fn($r) => $isNotLinux($r) && strcasecmp($r->license_microsoft_sql, 'Web') === 0)
                 ->sum(fn($r) => $getInMap($r->ecs_flavour_mapping, 'mssql'));

    $sqlStd = $vm->filter(fn($r) => $isNotLinux($r) && strcasecmp($r->license_microsoft_sql, 'Standard') === 0)
                 ->sum(fn($r) => $getInMap($r->ecs_flavour_mapping, 'mssql'));

    $sqlEnt = $vm->filter(fn($r) => $isNotLinux($r) && strcasecmp($r->license_microsoft_sql, 'Enterprise') === 0)
                 ->sum(fn($r) => $getInMap($r->ecs_flavour_mapping, 'mssql'));

    $rhel_1_8 = $vm->filter($isRhel)->sum(function ($r) use ($getVcpu) {
        $v = $getVcpu($r->ecs_flavour_mapping);
        return ($v > 0 && $v < 9) ? $v : 0;
    });

    $rhel_9_127 = $vm->filter($isRhel)->sum(function ($r) use ($getVcpu) {
        $v = $getVcpu($r->ecs_flavour_mapping);
        return ($v >= 9) ? $v : 0;
    });

    // Prepare result buckets
    $kl = $cj = array_fill_keys([
        'license_months','windows_std','windows_dc','rds',
        'sql_web','sql_std','sql_ent','rhel_1_8','rhel_9_127'
    ], 0);

    // Put License Month in DEST column; put all counts in SOURCE column
    if ($isKL) {                 // DR runs in KL, source = CJ
        $kl['license_months'] = $licenseMonths;  // months at destination
        $cj['windows_std']    = $winStd;         // counts at source
        $cj['windows_dc']     = $winDc;
        $cj['rds']            = $rds;
        $cj['sql_web']        = $sqlWeb;
        $cj['sql_std']        = $sqlStd;
        $cj['sql_ent']        = $sqlEnt;
        $cj['rhel_1_8']       = $rhel_1_8;
        $cj['rhel_9_127']     = $rhel_9_127;
    } elseif ($isCJ) {           // DR runs in CJ, source = KL
        $cj['license_months'] = $licenseMonths;
        $kl['windows_std']    = $winStd;
        $kl['windows_dc']     = $winDc;
        $kl['rds']            = $rds;
        $kl['sql_web']        = $sqlWeb;
        $kl['sql_std']        = $sqlStd;
        $kl['sql_ent']        = $sqlEnt;
        $kl['rhel_1_8']       = $rhel_1_8;
        $kl['rhel_9_127']     = $rhel_9_127;
    }

    // Optional: debug
    logger('DR VM picked', $vm->map(fn($r) => [
        'region' => $r->region,
        'dr_act' => $r->dr_activation,
        'os'     => $r->license_operating_system,
        'sql'    => $r->license_microsoft_sql,
        'rds'    => $r->license_rds_license,
        'flavour'=> $r->ecs_flavour_mapping,
    ])->values()->toArray());

    return ['kl' => $kl, 'cj' => $cj];
}


}