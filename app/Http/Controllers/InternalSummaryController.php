<?php

namespace App\Http\Controllers;


use App\Models\InternalSummary;
use App\Models\Version;
use App\Models\MPDRaaS;
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

        $project         = $version->project ?? null;
        $region          = $version->region ?? null;
        $solution_type   = $version->solution_type ?? null;
        $securityService = $version->security_service ?? null;
        $ecsData         = collect($version->ecs_configuration ?? []);

        $missing = [];
        if (!$solution_type)   $missing[] = 'Solution Type';
    
$mp = \App\Models\MPDRaaS::where('version_id', $version->id)->first();

$mpSummary = [
    'activation_days' => (int)($mp->mpdraas_activation_days ?? 0),
    'location'        => $mp->mpdraas_location ?? ($version->region->dr_location ?? 'None'),
    'ddos'            => $mp->ddos_requirement ?? 'No',
    'bandwidth'       => (float)($mp->bandwidth_requirement ?? 0), // Mbps
    'storage_evs'     => [
        'main'              => (float)($mp->main ?? 0),
        'used'              => (float)($mp->used ?? 0),
        'delta'             => (float)($mp->delta ?? 0),
        'total_replication' => (float)($mp->total_replication ?? 0),
    ],
    'dr_network' => collect($mp->dr_network ?? [])->map(function ($row, $code) {
        return [
            'code'   => $code,
            'name'   => $row['name'] ?? $code,
            'unit'   => $row['unit'] ?? '',
            'kl_qty' => (float)($row['kl_qty'] ?? 0),
            'cj_qty' => (float)($row['cj_qty'] ?? 0),
        ];
    })->values()->all(),
];


$mpVms = \App\Models\MPDRaaSVM::where('version_id', $version->id)->get();


$mpCounts = $mpVms
    ->filter(fn($r) => trim((string)($r->flavour_mapping ?? '')) !== '')
    ->groupBy(fn($r) => strtolower(trim((string)$r->flavour_mapping)))
    ->map->count();


$mpUsedFlavours = $mpCounts->keys()->sort()->values();

$loc = trim((string)($mpSummary['location'] ?? ''));
$mpDrCountsKL = collect();
$mpDrCountsCJ = collect();

if (strcasecmp($loc, 'Kuala Lumpur') === 0) {
    $mpDrCountsKL = $mpCounts;
} elseif (strcasecmp($loc, 'Cyberjaya') === 0) {
    $mpDrCountsCJ = $mpCounts;
} else {
  
    $mpDrCountsKL = $mpCounts;
}


$allFlavours = \App\Models\ECSFlavour::all()
    ->keyBy(fn($f) => strtolower(trim((string)$f->flavour_name)));

$mpDrKeys = $mpUsedFlavours->map(fn($f) => strtolower(trim($f . '.dr')));

$mpFlavourDetails = collect($allFlavours)
    ->only($mpDrKeys)
    ->map(fn($f) => [
        'vcpu'                 => (int) $f->vcpu,
        'vram'                 => (int) $f->vram,
        'type'                 => $f->type,
        'generation'           => $f->generation,
        'memory_label'         => $f->memory_label,
        'windows_license_count'=> (int) ($f->windows_license_count ?? 0),
        'mssql'                => (int) ($f->mssql ?? 0),
        'rhel'                 => (int) ($f->rhel ?? 0),
        'dr'                   => $f->dr,
        'pin'                  => $f->pin,
        'gpu'                  => $f->gpu,
        'ddh'                  => $f->ddh,
    ]);


       
        if (!empty($missing)) {
            return view('projects.security_service.internal_summary', [
                'version'              => $version,
                'project'              => $project,
                'solution_type'        => $solution_type,
                'summary'              => null,              
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
                'usedFlavoursCompute'   => collect(),
                'computeFlavourDetails' => collect(),
                'missing'              => $missing,
                 'mpdraas' => $mpSummary,
               
'mpUsedFlavours'   => $mpUsedFlavours,
'mpFlavourDetails' => $mpFlavourDetails,
'mpDrCountsKL'     => $mpDrCountsKL,
'mpDrCountsCJ'     => $mpDrCountsCJ,
        
                
            ]);
        }

        $nonStandardItems = $version->non_standard_items;

        $drLoc = trim((string)($region->dr_location ?? ''));
        $isKL  = strcasecmp($drLoc, 'Kuala Lumpur') === 0;
        $isCJ  = strcasecmp($drLoc, 'Cyberjaya') === 0;

        $flavourMap = \App\Models\ECSFlavour::all()
         
            ->keyBy(function ($item) {
                return strtolower(trim((string) $item->flavour_name));
            })
         
            ->map(function ($item) {
                return [
                    'vcpu'                  => (int) $item->vcpu,
                    'windows_license_count' => (int) $item->windows_license_count,
                 
                    'mssql'                 => (int) (
                        $item->microsoft_sql_license_count
                        ?? $item->mssql
                        ?? 0
                    ),
                    'rhel'                  => (int) ($item->rhel ?? 0),
                ];
            });

        $ecsData = collect($version->ecs_configuration)->map(function ($row) {
            // normalise strings
            $row->region             = trim((string)($row->region ?? ''));
            $row->dr_activation      = strtoupper(trim((string)($row->dr_activation ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->csdr_needed        = strtoupper(trim((string)($row->csdr_needed ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->seed_vm_required   = strtoupper(trim((string)($row->seed_vm_required ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->required           = strtoupper(trim((string)($row->required ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->csbs_standard_policy = preg_replace('/\s+/', ' ', trim((string)($row->csbs_standard_policy ?? '')));
        
            $row->ecs_flavour_mapping      = strtolower(trim((string)($row->ecs_flavour_mapping ?? '')));
            $row->license_operating_system = trim((string)($row->license_operating_system ?? ''));
            $row->license_microsoft_sql    = trim((string)($row->license_microsoft_sql ?? ''));
            $row->license_rds_license      = trim((string)($row->license_rds_license ?? ''));
            return $row;
        });

       
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

        

            // KL
$klIncrementalBackupCapacity = $ecsData->where('region', 'Kuala Lumpur')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->sum(function ($item) {
        $initial = (float)($item->csbs_initial_data_size ?? 0);            
        $pct     = (float)($item->csbs_incremental_change ?? 0);       
        $copies  = (int)($item->incremental_backup_total_retention_incremental_copies ?? 0); 

        $incPerCopy = $initial * ($pct / 100.0); 
        return (int) ceil($incPerCopy * $copies); 
    });

// Cyberjaya
$cyberIncrementalBackupCapacity = $ecsData->where('region', 'Cyberjaya')
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->sum(function ($item) {
        $initial = (float)($item->csbs_initial_data_size ?? 0);
        $pct     = (float)($item->csbs_incremental_change ?? 0);
        $copies  = (int)($item->incremental_backup_total_retention_incremental_copies ?? 0);

        $incPerCopy = $initial * ($pct / 100.0);
        return (int) ceil($incPerCopy * $copies);
    });


       
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

        
        $coldDrDaysKL = 0;
        $coldDrDaysCJ = 0;

        if ($ecsData->where('dr_activation', 'Yes')->count() > 0) {
            $coldDrDaysKL = $region->dr_location === 'Kuala Lumpur' ? $region->kl_dr_activation_days : 0;
            $coldDrDaysCJ = $region->dr_location === 'Cyberjaya' ? $region->cyber_dr_activation_days : 0;
        }

     
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

     
        $yesCountAll = $ecsData->where('csdr_needed', 'Yes')->count();

        $drReplicationKL = $isKL ? $yesCountAll : 0;
        $drReplicationCJ = $isCJ ? $yesCountAll : 0;


        $drDeclarationKL = $drReplicationKL > 0 ? (int)($region->kl_dr_activation_days ?? 0)    : 0;
        $drDeclarationCJ = $drReplicationCJ > 0 ? (int)($region->cyber_dr_activation_days ?? 0) : 0;

       
        $drManagedServiceKL = $drReplicationKL;
        $drManagedServiceCJ = $drReplicationCJ;

      
        $drLic = $this->getDrLicenseSummary(
            $ecsData,
            $flavourMap,
            $isKL,
            $isCJ,
            (int)$coldDrDaysKL,
            (int)$coldDrDaysCJ
        );

       
        $klEvsDR = $ecsData
            ->where('region', 'Cyberjaya')     
            ->where('dr_activation', 'Yes')
            ->sum(fn ($i) => ($i->storage_system_disk ?? 0) + ($i->storage_data_disk ?? 0)) * 2;

        $cyberEvsDR = $ecsData
            ->where('region', 'Kuala Lumpur')   
            ->where('dr_activation', 'Yes')
            ->sum(fn ($i) => ($i->storage_system_disk ?? 0) + ($i->storage_data_disk ?? 0)) * 2;


          
$fromKL = $ecsData->filter(function ($r) {
    return strcasecmp((string)$r->region, 'Kuala Lumpur') === 0
        && (string)$r->dr_activation === 'Yes'
        && trim((string)$r->ecs_flavour_mapping) !== '';
});

$fromCJ = $ecsData->filter(function ($r) {
    return strcasecmp((string)$r->region, 'Cyberjaya') === 0
        && (string)$r->dr_activation === 'Yes'
        && trim((string)$r->ecs_flavour_mapping) !== '';
});

$drCountsKL   = collect();
$drCountsCJ   = collect();
$usedFlavours = collect();

if ($isKL) {
  
    $drCountsKL = $fromCJ
        ->groupBy(fn($r) => strtolower(trim((string)$r->ecs_flavour_mapping)))
        ->map->count();

    $usedFlavours = $drCountsKL->keys()->sort()->values();
} elseif ($isCJ) {
   
    $drCountsCJ = $fromKL
        ->groupBy(fn($r) => strtolower(trim((string)$r->ecs_flavour_mapping)))
        ->map->count();

    $usedFlavours = $drCountsCJ->keys()->sort()->values();
} else {
  
    $drCountsKL = $fromCJ
        ->groupBy(fn($r) => strtolower(trim((string)$r->ecs_flavour_mapping)))
        ->map->count();

    $drCountsCJ = $fromKL
        ->groupBy(fn($r) => strtolower(trim((string)$r->ecs_flavour_mapping)))
        ->map->count();

    $usedFlavours = $drCountsKL->keys()->merge($drCountsCJ->keys())->unique()->sort()->values();
}



$allFlavours = \App\Models\ECSFlavour::all()
    ->keyBy(fn($f) => strtolower(trim((string) $f->flavour_name)));


$drKeys = $usedFlavours
    ->map(fn($f) => strtolower($f . '.dr'))
    ->values()
    ->all();


$flavourDetails = collect($allFlavours)
    ->only($drKeys)
    ->map(fn($f) => [
        'vcpu' => (int) $f->vcpu,
        'vram' => (int) $f->vram,
        'type' => $f->type,
        'generation' => $f->generation,
        'memory_label' => $f->memory_label,
        'windows_license_count' => (int) ($f->windows_license_count ?? 0),
        'rhel'  => (int) ($f->rhel ?? 0),
        'mssql' => (int) ($f->mssql ?? 0),
        'dr'    => $f->dr,
        'pin'   => $f->pin,
        'gpu'   => $f->gpu,
        'ddh'   => $f->ddh,
    ]);



    
   



        $licenseSummary = $this->getLicenseSummary($ecsData, $flavourMap);

        $ecsSummary = $ecsData->groupBy(['region', 'ecs_flavour_mapping'])
            ->map(function ($regionGroup) {
                return $regionGroup->map->count();
            });

     
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

       

        // vPLL
        $klDrVpll     = $isKL ? (int) ceil(($region->kl_db_bandwidth ?? 0) / 10) : 0;
        $cyberDrVpll  = $isCJ ? (int) ceil(($region->cyber_db_bandwidth ?? 0) / 10) : 0;

        // DR Elastic IP (Unit Per Day) 
        $klDrEip      = $isKL ? (int) ($region->kl_elastic_ip_dr ?? 0) : 0;
        $cyberDrEip   = $isCJ ? (int) ($region->cyber_elastic_ip_dr ?? 0) : 0;

    
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

        // DR Cloud Firewall – count how many tier being choose every type; put on DR destination only
        $fortiCount = (int) (($region->tier1_dr_security === 'fortigate') + ($region->tier2_dr_security === 'fortigate'));
        $opnCount   = (int) (($region->tier1_dr_security === 'opn_sense') + ($region->tier2_dr_security === 'opn_sense'));

        $klDrForti    = $isKL ? $fortiCount : 0;
        $cyberDrForti = $isCJ ? $fortiCount : 0;
        $klDrOpn      = $isKL ? $opnCount   : 0;
        $cyberDrOpn   = $isCJ ? $opnCount   : 0;

        // --- Snapshot 
        $summary  = \App\Models\InternalSummary::where('version_id', $version->id)->first();
        $isLocked = (bool) optional($summary)->is_logged;

     
        if ($isLocked) {
        
            $licenseSummary = [
                'windows_std' => [
                    'Kuala Lumpur' => (int) ($summary->kl_windows_std ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_windows_std ?? 0),
                ],
                'windows_dc'  => [
                    'Kuala Lumpur' => (int) ($summary->kl_windows_dc ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_windows_dc ?? 0),
                ],
                'rds'         => [
                    'Kuala Lumpur' => (int) ($summary->kl_rds ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_rds ?? 0),
                ],
                'sql_web'     => [
                    'Kuala Lumpur' => (int) ($summary->kl_sql_web ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_sql_web ?? 0),
                ],
                'sql_std'     => [
                    'Kuala Lumpur' => (int) ($summary->kl_sql_std ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_sql_std ?? 0),
                ],
                'sql_ent'     => [
                    'Kuala Lumpur' => (int) ($summary->kl_sql_ent ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_sql_ent ?? 0),
                ],
                'rhel_1_8'    => [
                    'Kuala Lumpur' => (int) ($summary->kl_rhel_1_8 ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_rhel_1_8 ?? 0),
                ],
                'rhel_9_127'  => [
                    'Kuala Lumpur' => (int) ($summary->kl_rhel_9_127 ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_rhel_9_127 ?? 0),
                ],
            ];

            
            $klEvsDR    = (int) ($summary->kl_evs_dr ?? $klEvsDR ?? 0);
            $cyberEvsDR = (int) ($summary->cyber_evs_dr ?? $cyberEvsDR ?? 0);

           
            if (is_array($summary->ecs_flavour_summary ?? null)) {
                $ecsSummary = $summary->ecs_flavour_summary;
            }
        }

       
        if ($isLocked) {
           
            $licenseSummary = [
                'windows_std' => [
                    'Kuala Lumpur' => (int) ($summary->kl_windows_std ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_windows_std ?? 0),
                ],
                'windows_dc'  => [
                    'Kuala Lumpur' => (int) ($summary->kl_windows_dc ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_windows_dc ?? 0),
                ],
                'rds'         => [
                    'Kuala Lumpur' => (int) ($summary->kl_rds ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_rds ?? 0),
                ],
                'sql_web'     => [
                    'Kuala Lumpur' => (int) ($summary->kl_sql_web ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_sql_web ?? 0),
                ],
                'sql_std'     => [
                    'Kuala Lumpur' => (int) ($summary->kl_sql_std ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_sql_std ?? 0),
                ],
                'sql_ent'     => [
                    'Kuala Lumpur' => (int) ($summary->kl_sql_ent ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_sql_ent ?? 0),
                ],
                'rhel_1_8'    => [
                    'Kuala Lumpur' => (int) ($summary->kl_rhel_1_8 ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_rhel_1_8 ?? 0),
                ],
                'rhel_9_127'  => [
                    'Kuala Lumpur' => (int) ($summary->kl_rhel_9_127 ?? 0),
                    'Cyberjaya'    => (int) ($summary->cyber_rhel_9_127 ?? 0),
                ],
            ];

           
            $klEvsDR    = (int) ($summary->kl_evs_dr ?? $klEvsDR ?? 0);
            $cyberEvsDR = (int) ($summary->cyber_evs_dr ?? $cyberEvsDR ?? 0);

           
            if (is_array($summary->ecs_flavour_summary)) {
                $ecsSummary = $summary->ecs_flavour_summary;
            }
        }

        $managedTypes = [
            'Managed Operating System',
            'Managed Backup and Restore',
            'Managed Patching',
            'Managed DR',
        ];

       
        $klManagedServices = [
            'Managed Operating System' => 0,
            'Managed Backup and Restore' => 0,
            'Managed Patching' => 0,
            'Managed DR' => 0,
        ];

        foreach (range(1, 4) as $i) {
            $service = $securityService->{'kl_managed_services_' . $i} ?? null;
            if (in_array($service, $managedTypes)) {
                $klManagedServices[$service]++;
            }
        }

        
        $cyberManagedServices = [
            'Managed Operating System' => 0,
            'Managed Backup and Restore' => 0,
            'Managed Patching' => 0,
            'Managed DR' => 0,
        ];

        foreach (range(1, 4) as $i) {
            $service = $securityService->{'cyber_managed_services_' . $i} ?? null;
            if (in_array($service, $managedTypes)) {
                $cyberManagedServices[$service]++;
            }
        }

       
        $summarySnap = \App\Models\InternalSummary::where('version_id', $version->id)->first();
        $isLockedFinal = (bool) optional($summarySnap)->is_logged;

        $summaryLive = (object)[
         
            'kl_bandwidth' => $region->kl_bandwidth ?? 0,
            'kl_bandwidth_with_antiddos' => $region->kl_bandwidth_with_antiddos ?? 0,
            'kl_included_elastic_ip' => $region->kl_included_elastic_ip ?? 0,
            'kl_elastic_ip' => $region->kl_elastic_ip ?? 0,
            'kl_elastic_load_balancer' => $region->kl_elastic_load_balancer ?? 0,
            'kl_direct_connect_virtual' => $region->kl_direct_connect_virtual ?? 0,
            'kl_l2br_instance' => $region->kl_l2br_instance ?? 0,
            'kl_virtual_private_leased_line' => $region->kl_virtual_private_leased_line ?? 0,
            'kl_vpll_l2br' => $region->kl_vpll_l2br ?? 0,
            'kl_nat_gateway_small' => $region->kl_nat_gateway_small ?? 0,
            'kl_nat_gateway_medium' => $region->kl_nat_gateway_medium ?? 0,
            'kl_nat_gateway_large' => $region->kl_nat_gateway_large ?? 0,
            'kl_nat_gateway_xlarge' => $region->kl_nat_gateway_xlarge ?? 0,

            'cyber_bandwidth' => $region->cyber_bandwidth ?? 0,
            'cyber_bandwidth_with_antiddos' => $region->cyber_bandwidth_with_antiddos ?? 0,
            'cyber_included_elastic_ip' => $region->cyber_included_elastic_ip ?? 0,
            'cyber_elastic_ip' => $region->cyber_elastic_ip ?? 0,
            'cyber_elastic_load_balancer' => $region->cyber_elastic_load_balancer ?? 0,
            'cyber_direct_connect_virtual' => $region->cyber_direct_connect_virtual ?? 0,
            'cyber_l2br_instance' => $region->cyber_l2br_instance ?? 0,
            'cyber_nat_gateway_small' => $region->cyber_nat_gateway_small ?? 0,
            'cyber_nat_gateway_medium' => $region->cyber_nat_gateway_medium ?? 0,
            'cyber_nat_gateway_large' => $region->cyber_nat_gateway_large ?? 0,
            'cyber_nat_gateway_xlarge' => $region->cyber_nat_gateway_xlarge ?? 0,

            // Storage & IMS
            'kl_scalable_file_service'    => $region->kl_scalable_file_service ?? 0,
            'cyber_scalable_file_service' => $region->cyber_scalable_file_service ?? 0,
            'kl_object_storage_service'   => $region->kl_object_storage_service ?? 0,
            'cyber_object_storage_service'=> $region->cyber_object_storage_service ?? 0,
            'kl_evs' => $klEvs ?? 0,
            'cyber_evs' => $cyberEvs ?? 0,
            'kl_snapshot_storage'   => $klSnapshot ?? 0,
            'cyber_snapshot_storage'=> $cyberSnapshot ?? 0,
            'kl_image_storage'      => $klImage ?? 0,
            'cyber_image_storage'   => $cyberImage ?? 0,

            // Professional services
            'mandays' => $region->mandays ?? 0,
            'kl_license_count' => $region->kl_license_count ?? 0,
            'cyber_license_count' => $region->cyber_license_count ?? 0,
            'kl_duration' => $region->kl_duration ?? 0,
            'cyber_duration' => $region->cyber_duration ?? 0,

            // Monitoring & Security Services
            'kl_security_advanced'      => $securityService->kl_security_advanced ?? 0,
            'cyber_security_advanced'   => $securityService->cyber_security_advanced ?? 0,
            'kl_insight_vmonitoring'    => ($securityService->kl_insight_vmonitoring ?? 'No') === 'Yes' ? 1 : 0,
            'cyber_insight_vmonitoring' => ($securityService->cyber_insight_vmonitoring ?? 'No') === 'Yes' ? 1 : 0,
            'kl_cloud_vulnerability'    => $securityService->kl_cloud_vulnerability ?? 0,
            'cyber_cloud_vulnerability' => $securityService->cyber_cloud_vulnerability ?? 0,
            'kl_firewall_fortigate'     => $securityService->kl_firewall_fortigate ?? 0,
            'cyber_firewall_fortigate'  => $securityService->cyber_firewall_fortigate ?? 0,
            'kl_firewall_opnsense'      => $securityService->kl_firewall_opnsense ?? 0,
            'cyber_firewall_opnsense'   => $securityService->cyber_firewall_opnsense ?? 0,
            'kl_shared_waf'             => $securityService->kl_shared_waf ?? 0,
            'cyber_shared_waf'          => $securityService->cyber_shared_waf ?? 0,
            'kl_antivirus'              => $securityService->kl_antivirus ?? 0,
            'cyber_antivirus'           => $securityService->cyber_antivirus ?? 0,
            'kl_gslb'                   => $securityService->kl_gslb ?? 0,
            'cyber_gslb'                => $securityService->cyber_gslb ?? 0,

            // Backup
            'kl_full_backup_capacity'             => $klFullBackupCapacity ?? 0,
            'cyber_full_backup_capacity'          => $cyberFullBackupCapacity ?? 0,
            'kl_incremental_backup_capacity'      => $klIncrementalBackupCapacity ?? 0,
            'cyber_incremental_backup_capacity'   => $cyberIncrementalBackupCapacity ?? 0,
            'kl_replication_retention_capacity'   => $klReplicationRetentionCapacity ?? 0,
            'cyber_replication_retention_capacity'=> $cyberReplicationRetentionCapacity ?? 0,

            // DR rows
            'kl_cold_dr_days'          => $coldDrDaysKL ?? 0,
            'cyber_cold_dr_days'       => $coldDrDaysCJ ?? 0,
            'kl_cold_dr_seeding_vm'    => $coldDrSeedingVMKL ?? 0,
            'cyber_cold_dr_seeding_vm' => $coldDrSeedingVMCJ ?? 0,
            'kl_dr_storage'            => $drStorageKL ?? 0,
            'cyber_dr_storage'         => $drStorageCJ ?? 0,
            'kl_dr_replication'        => $drReplicationKL ?? 0,
            'cyber_dr_replication'     => $drReplicationCJ ?? 0,
            'kl_dr_declaration'        => $drDeclarationKL ?? 0,
            'cyber_dr_declaration'     => $drDeclarationCJ ?? 0,
            'kl_dr_managed_service'    => $drManagedServiceKL ?? 0,
            'cyber_dr_managed_service' => $drManagedServiceCJ ?? 0,

            // DR Net/Sec
            'kl_dr_vpll' => $klDrVpll ?? 0,
            'cyber_dr_vpll' => $cyberDrVpll ?? 0,
            'kl_dr_elastic_ip' => $klDrEip ?? 0,
            'cyber_dr_elastic_ip' => $cyberDrEip ?? 0,
            'kl_dr_bandwidth' => $klDrBw ?? 0,
            'cyber_dr_bandwidth' => $cyberDrBw ?? 0,
            'kl_dr_bandwidth_antiddos' => $klDrBwAnti ?? 0,
            'cyber_dr_bandwidth_antiddos' => $cyberDrBwAnti ?? 0,
            'kl_dr_firewall_fortigate' => $klDrForti ?? 0,
            'cyber_dr_firewall_fortigate' => $cyberDrForti ?? 0,
            'kl_dr_firewall_opnsense' => $klDrOpn ?? 0,
            'cyber_dr_firewall_opnsense' => $cyberDrOpn ?? 0,

           
            'kl_evs_dr' => $klEvsDR ?? 0,
            'cyber_evs_dr' => $cyberEvsDR ?? 0,

         
            'ecs_flavour_summary' => $ecsSummary ?? [],
            'is_logged' => false,




    'kl_dr_license_months'    => $drLic['kl']['license_months'] ?? 0,
    'cyber_dr_license_months' => $drLic['cj']['license_months'] ?? 0,


    'kl_dr_windows_std'       => $drLic['kl']['windows_std'] ?? 0,
    'cyber_dr_windows_std'    => $drLic['cj']['windows_std'] ?? 0,
    'kl_dr_windows_dc'        => $drLic['kl']['windows_dc'] ?? 0,
    'cyber_dr_windows_dc'     => $drLic['cj']['windows_dc'] ?? 0,
    'kl_dr_rds'               => $drLic['kl']['rds'] ?? 0,
    'cyber_dr_rds'            => $drLic['cj']['rds'] ?? 0,
    'kl_dr_sql_web'           => $drLic['kl']['sql_web'] ?? 0,
    'cyber_dr_sql_web'        => $drLic['cj']['sql_web'] ?? 0,
    'kl_dr_sql_std'           => $drLic['kl']['sql_std'] ?? 0,
    'cyber_dr_sql_std'        => $drLic['cj']['sql_std'] ?? 0,
    'kl_dr_sql_ent'           => $drLic['kl']['sql_ent'] ?? 0,
    'cyber_dr_sql_ent'        => $drLic['cj']['sql_ent'] ?? 0,
    'kl_dr_rhel_1_8'          => $drLic['kl']['rhel_1_8'] ?? 0,
    'cyber_dr_rhel_1_8'       => $drLic['cj']['rhel_1_8'] ?? 0,
    'kl_dr_rhel_9_127'        => $drLic['kl']['rhel_9_127'] ?? 0,
    'cyber_dr_rhel_9_127'     => $drLic['cj']['rhel_9_127'] ?? 0,

        ];

        
        $summaryForView = $isLockedFinal ? $summarySnap : $summaryLive;

       
        $usedFlavoursCompute = collect($ecsSummary['Kuala Lumpur'] ?? [])
            ->keys()
            ->merge(collect($ecsSummary['Cyberjaya'] ?? [])->keys())
            ->unique()
            ->values();

        $computeFlavourDetails = \App\Models\ECSFlavour::whereIn('flavour_name', $usedFlavoursCompute->all())
            ->get()
            ->keyBy('flavour_name')
            ->map(fn($f)=>['vcpu'=>$f->vcpu,'vram'=>$f->vram]);




     
$mp = MPDRaaS::where('version_id', $version->id)->first();

$mpSummary = [
    'activation_days' => (int)($mp->mpdraas_activation_days ?? 0),
    'location'        => $mp->mpdraas_location ?? ($version->region->dr_location ?? 'None'),
    'ddos'            => $mp->ddos_requirement ?? 'No',
    'bandwidth'       => (float)($mp->bandwidth_requirement ?? 0), // Mbps

    
    'storage_evs' => [
        'main'               => (float)($mp->main ?? 0),
        'used'               => (float)($mp->used ?? 0),
        'delta'              => (float)($mp->delta ?? 0),
        'total_replication'  => (float)($mp->total_replication ?? 0),
    ],

    
    'dr_network' => collect($mp->dr_network ?? [])->map(function($row, $code){
        return [
            'code'   => $code,
            'name'   => $row['name'] ?? $code,
            'unit'   => $row['unit'] ?? '',
            'kl_qty' => (float)($row['kl_qty'] ?? 0),
            'cj_qty' => (float)($row['cj_qty'] ?? 0),
        ];
    })->values()->all(),
];


        return view('projects.security_service.internal_summary', [
            'version'               => $version,
            'project'               => $project,
            'solution_type'         => $solution_type,
          
            'summary'               => $summaryForView,
            'klManagedServices'     => $klManagedServices,
            'cyberManagedServices'  => $cyberManagedServices,
            'nonStandardItems'      => $nonStandardItems,
            'ecsSummary'            => $ecsSummary,
            'licenseSummary'        => $licenseSummary,
            'klEvs'                 => $klEvs,
            'cyberEvs'              => $cyberEvs,
            'klEvsDR'               => $klEvsDR,
            'cyberEvsDR'            => $cyberEvsDR,
           
            'usedFlavours'          => $usedFlavours,
            'flavourDetails'        => $flavourDetails,
            'drCountsKL'            => $drCountsKL,
            'drCountsCJ'            => $drCountsCJ,
          
            'usedFlavoursCompute'   => $usedFlavoursCompute,
            'computeFlavourDetails' => $computeFlavourDetails,
            'mpdraas' => $mpSummary,
           
'mpUsedFlavours'   => $mpUsedFlavours,
'mpFlavourDetails' => $mpFlavourDetails,
'mpDrCountsKL'     => $mpDrCountsKL,
'mpDrCountsCJ'     => $mpDrCountsCJ,


        ]);
    }

    public function commit($versionId)
    {
        $version = Version::with([
            'project.customer',
            'project.presale',
            'region',
            'security_service',
            'ecs_configuration',
            'non_standard_items',
            'solution_type',
        ])->findOrFail($versionId);

       
$missing = [];
if (!$version->solution_type)   $missing[] = 'Solution Type';
if (!$version->project)         $missing[] = 'Project';
if (!$version->region)          $missing[] = 'Professional Services (Region)';
if (!$version->security_service)$missing[] = 'Security Services';
if (empty($version->ecs_configuration) || count($version->ecs_configuration) === 0) {
    $missing[] = 'ECS & Backup (at least 1 VM/row)';
}

if (!empty($missing)) {
    return redirect()
        ->route('versions.internal_summary.show', $version->id)
        ->with('error', 'Cannot commit. Missing: '.implode(', ', $missing));
}


       
        if (InternalSummary::where('version_id', $version->id)->where('is_logged', true)->exists()) {
            return redirect()
                ->route('versions.internal_summary.show', $version->id)
                ->with('error', 'This summary is already committed.');
        }

        $project         = $version->project;
        $region          = $version->region;
        $securityService = $version->security_service;

       
        $flavourMap = \App\Models\ECSFlavour::all()
            ->keyBy(fn($x) => strtolower(trim((string)$x->flavour_name)))
            ->map(fn($x) => [
                'vcpu'                  => (int)$x->vcpu,
                'windows_license_count' => (int)($x->windows_license_count ?? 0),
                'mssql'                 => (int)($x->microsoft_sql_license_count ?? $x->mssql ?? 0),
                'rhel'                  => (int)($x->rhel ?? 0),
            ]);

        // --- ECS rows ---
        $ecsData = collect($version->ecs_configuration ?? [])->map(function ($row) {
     
            $row->region            = trim((string)($row->region ?? ''));
            $row->dr_activation     = strtoupper(trim((string)($row->dr_activation ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->csdr_needed       = strtoupper(trim((string)($row->csdr_needed ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->seed_vm_required  = strtoupper(trim((string)($row->seed_vm_required ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->required          = strtoupper(trim((string)($row->required ?? 'No'))) === 'YES' ? 'Yes' : 'No';
            $row->csbs_standard_policy = preg_replace('/\s+/', ' ', trim((string)($row->csbs_standard_policy ?? '')));
            $row->ecs_flavour_mapping   = strtolower(trim((string)($row->ecs_flavour_mapping ?? '')));
            $row->license_operating_system = trim((string)($row->license_operating_system ?? ''));
            $row->license_microsoft_sql   = trim((string)($row->license_microsoft_sql ?? ''));
            $row->license_rds_license     = trim((string)($row->license_rds_license ?? ''));
            return $row;
        });

        
        $sumFull = fn($regionName) => $ecsData->where('region', $regionName)
            ->where('csbs_standard_policy', '!=', 'No Backup')
            ->sum(fn($i) => ($i->csbs_initial_data_size ?? 0) * ( ($i->full_backup_total_retention_full_copies ?? 0) + 1 ));



        $sumInc = fn($regionName) => $ecsData->where('region', $regionName)
    ->where('csbs_standard_policy', '!=', 'No Backup')
    ->sum(function ($i) {
        $initial = (float)($i->csbs_initial_data_size ?? 0);
        $pct     = (float)($i->csbs_incremental_change ?? 0);
        $copies  = (int)($i->incremental_backup_total_retention_incremental_copies ?? 0);

        $incPerCopy = $initial * ($pct / 100.0);
        return (int) ceil($incPerCopy * $copies);
    });


        $klFullBackupCapacity    = $sumFull('Kuala Lumpur');
        $cyberFullBackupCapacity = $sumFull('Cyberjaya');
        $klIncrementalBackupCapacity    = $sumInc('Kuala Lumpur');
        $cyberIncrementalBackupCapacity = $sumInc('Cyberjaya');

     
        $klReplicationRetentionCapacity = $ecsData->where('region','Cyberjaya')
            ->where('csbs_standard_policy','!=','No Backup')
            ->where('required','Yes')
            ->sum(fn($i)=>($i->csbs_total_storage ?? 0) + ($i->additional_storage ?? 0));
        $klReplicationRetentionCapacity += $ecsData->where('region','Cyberjaya')
            ->where('csbs_standard_policy','!=','No Backup')
            ->where('required','No')->where('dr_activation','Yes')
            ->sum(fn($i)=>($i->csbs_total_storage ?? 0) + ($i->additional_storage ?? 0));

        $cyberReplicationRetentionCapacity = $ecsData->where('region','Kuala Lumpur')
            ->where('csbs_standard_policy','!=','No Backup')
            ->where('required','Yes')
            ->sum(fn($i)=>($i->csbs_total_storage ?? 0) + ($i->additional_storage ?? 0));
        $cyberReplicationRetentionCapacity += $ecsData->where('region','Kuala Lumpur')
            ->where('csbs_standard_policy','!=','No Backup')
            ->where('required','No')->where('dr_activation','Yes')
            ->sum(fn($i)=>($i->csbs_total_storage ?? 0) + ($i->additional_storage ?? 0));

        // ===== IMS totals =====
        $klEvs = $ecsData->where('region','Kuala Lumpur')
            ->sum(fn($i)=>(($i->storage_system_disk ?? 0)+($i->storage_data_disk ?? 0)));
        $cyberEvs = $ecsData->where('region','Cyberjaya')
            ->sum(fn($i)=>(($i->storage_system_disk ?? 0)+($i->storage_data_disk ?? 0)));

        $calcSnap = function($regionName) use ($ecsData){
            return $ecsData->where('region',$regionName)->sum(function($i){
                $base = ($i->snapshot_copies ?? 0) == 0 ? 0 :
                    ($i->snapshot_copies * ( ($i->storage_system_disk ?? 0) + ($i->storage_data_disk ?? 0) ));
                return $base + ($i->additional_capacity ?? 0);
            });
        };
        $klSnapshot    = $calcSnap('Kuala Lumpur');
        $cyberSnapshot = $calcSnap('Cyberjaya');

        $calcImage = function($regionName) use ($ecsData){
            return $ecsData->where('region',$regionName)->sum(function($i){
                return ($i->image_copies ?? 0) == 0 ? 0 :
                    ($i->image_copies * ( ($i->storage_system_disk ?? 0) + ($i->storage_data_disk ?? 0) ));
            });
        };
        $klImage    = $calcImage('Kuala Lumpur');
        $cyberImage = $calcImage('Cyberjaya');

        // ===== ECS summary by flavour =====
        $ecsSummary = $ecsData->groupBy(['region','ecs_flavour_mapping'])
            ->map(fn($grp)=>$grp->map->count())
            ->toArray();

        // ===== License summaries =====
        $licenseSummary = $this->getLicenseSummary($ecsData, $flavourMap);

        // ===== DR basics =====
        $drLoc = trim((string)($region->dr_location ?? ''));
        $isKL  = strcasecmp($drLoc,'Kuala Lumpur') === 0;
        $isCJ  = strcasecmp($drLoc,'Cyberjaya') === 0;

        // Cold DR days (only if any DR Activation = Yes)
        $hasDrActivation = $ecsData->where('dr_activation','Yes')->count() > 0;
        $coldDrDaysKL = $hasDrActivation && $isKL ? (int)($region->kl_dr_activation_days ?? 0) : 0;
        $coldDrDaysCJ = $hasDrActivation && $isCJ ? (int)($region->cyber_dr_activation_days ?? 0) : 0;

        // Cold DR – seeding VM
        $coldDrSeedingVMKL = 0; $coldDrSeedingVMCJ = 0;
        if ($isKL) {
            $coldDrSeedingVMKL = $ecsData->where('csbs_standard_policy','!=','No Backup')
                ->where('dr_activation','Yes')->where('region','Cyberjaya')->where('seed_vm_required','Yes')->count();
        } elseif ($isCJ) {
            $coldDrSeedingVMCJ = $ecsData->where('csbs_standard_policy','!=','No Backup')
                ->where('dr_activation','Yes')->where('region','Kuala Lumpur')->where('seed_vm_required','Yes')->count();
        }

        // DR storage (at destination)
        $drStorageKL = 0; $drStorageCJ = 0;
        if ($isKL) {
            $drStorageKL = $ecsData->where('region','Cyberjaya')->where('csdr_needed','Yes')->sum('csbs_total_storage');
        } elseif ($isCJ) {
            $drStorageCJ = $ecsData->where('region','Kuala Lumpur')->where('csdr_needed','Yes')->sum('csbs_total_storage');
        }

        
        $yesCountAll = $ecsData->where('csdr_needed','Yes')->count();
        $drReplicationKL = $isKL ? $yesCountAll : 0;
        $drReplicationCJ = $isCJ ? $yesCountAll : 0;

        // Declaration days (if replication>0)
        $drDeclarationKL = $drReplicationKL > 0 ? (int)($region->kl_dr_activation_days ?? 0) : 0;
        $drDeclarationCJ = $drReplicationCJ > 0 ? (int)($region->cyber_dr_activation_days ?? 0) : 0;

        // Managed Service per day = replication
        $drManagedServiceKL = $drReplicationKL;
        $drManagedServiceCJ = $drReplicationCJ;

       
        $klEvsDR = $ecsData->where('region','Cyberjaya')->where('dr_activation','Yes')
            ->sum(fn($i)=>(($i->storage_system_disk ?? 0)+($i->storage_data_disk ?? 0))) * 2;
        $cyberEvsDR = $ecsData->where('region','Kuala Lumpur')->where('dr_activation','Yes')
            ->sum(fn($i)=>(($i->storage_system_disk ?? 0)+($i->storage_data_disk ?? 0))) * 2;

       
        $drCountsKL = $ecsData->where('region','Cyberjaya')->where('dr_activation','Yes')
            ->groupBy('ecs_flavour_mapping')->map->count();
        $drCountsCJ = $ecsData->where('region','Kuala Lumpur')->where('dr_activation','Yes')
            ->groupBy('ecs_flavour_mapping')->map->count();

     
        $drLic = $this->getDrLicenseSummary($ecsData, $flavourMap, $isKL, $isCJ, (int)$coldDrDaysKL, (int)$coldDrDaysCJ);

     
        
        $klDrVpll    = $isKL ? (int) ceil(($region->kl_db_bandwidth ?? 0) / 10)      : 0;
        $cyberDrVpll = $isCJ ? (int) ceil(($region->cyber_db_bandwidth ?? 0) / 10)   : 0;

        $klDrEip     = $isKL ? (int)($region->kl_elastic_ip_dr ?? 0)     : 0;
        $cyberDrEip  = $isCJ ? (int)($region->cyber_elastic_ip_dr ?? 0)  : 0;

        $klDrBw = $cyberDrBw = $klDrBwAnti = $cyberDrBwAnti = 0;
        if ($isKL) {
            if (($region->dr_bandwidth_type ?? 'bandwidth') === 'bandwidth') $klDrBw = (int)($region->kl_db_bandwidth ?? 0);
            else $klDrBwAnti = (int)($region->kl_db_bandwidth ?? 0);
        }
        if ($isCJ) {
            if (($region->dr_bandwidth_type ?? 'bandwidth') === 'bandwidth') $cyberDrBw = (int)($region->cyber_db_bandwidth ?? 0);
            else $cyberDrBwAnti = (int)($region->cyber_db_bandwidth ?? 0);
        }

        $fortiCount = (int)(($region->tier1_dr_security === 'fortigate') + ($region->tier2_dr_security === 'fortigate'));
        $opnCount   = (int)(($region->tier1_dr_security === 'opn_sense') + ($region->tier2_dr_security === 'opn_sense'));
        $klDrForti = $isKL ? $fortiCount : 0;   $cyberDrForti = $isCJ ? $fortiCount : 0;
        $klDrOpn   = $isKL ? $opnCount   : 0;   $cyberDrOpn   = $isCJ ? $opnCount   : 0;

       
        $payload = [
            'id'         => \Illuminate\Support\Str::uuid(),
            'version_id' => $version->id,
            'project_id'  => $project->id ?? null,
            'customer_id' => $project->customer_id ?? null,
            'presale_id'  => $project->presale_id ?? null,

          
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

            // Storage & IMS
            'kl_scalable_file_service'    => $region->kl_scalable_file_service,
            'cyber_scalable_file_service' => $region->cyber_scalable_file_service,
            'kl_object_storage_service'   => $region->kl_object_storage_service,
            'cyber_object_storage_service'=> $region->cyber_object_storage_service,
            'kl_evs' => $klEvs,
            'cyber_evs' => $cyberEvs,

            // Professional services
            'mandays' => $region->mandays,
            'kl_license_count' => $region->kl_license_count,
            'cyber_license_count' => $region->cyber_license_count,
            'kl_duration' => $region->kl_duration,
            'cyber_duration' => $region->cyber_duration,

            // Monitoring & Security Services
            'kl_security_advanced'      => $securityService->kl_security_advanced,
            'cyber_security_advanced'   => $securityService->cyber_security_advanced,
            'kl_insight_vmonitoring'    => $securityService->kl_insight_vmonitoring === 'Yes' ? 1 : 0,
            'cyber_insight_vmonitoring' => $securityService->cyber_insight_vmonitoring === 'Yes' ? 1 : 0,
            'kl_cloud_vulnerability'    => $securityService->kl_cloud_vulnerability,
            'cyber_cloud_vulnerability' => $securityService->cyber_cloud_vulnerability,
            'kl_firewall_fortigate'     => $securityService->kl_firewall_fortigate,
            'cyber_firewall_fortigate'  => $securityService->cyber_firewall_fortigate,
            'kl_firewall_opnsense'      => $securityService->kl_firewall_opnsense,
            'cyber_firewall_opnsense'   => $securityService->cyber_firewall_opnsense,
            'kl_shared_waf'             => $securityService->kl_shared_waf,
            'cyber_shared_waf'          => $securityService->cyber_shared_waf,
            'kl_antivirus'              => $securityService->kl_antivirus,
            'cyber_antivirus'           => $securityService->cyber_antivirus,
            'kl_gslb'                   => $securityService->kl_gslb,
            'cyber_gslb'                => $securityService->cyber_gslb,

            // IMS totals
            'kl_snapshot_storage'   => $klSnapshot,
            'cyber_snapshot_storage'=> $cyberSnapshot,
            'kl_image_storage'      => $klImage,
            'cyber_image_storage'   => $cyberImage,

            // ECS summary & totals
            'ecs_flavour_summary' => $ecsSummary,
            'ecs_flavour_mapping' => $ecsData->pluck('ecs_flavour_mapping')->implode(','),
            'ecs_vcpu'            => $ecsData->sum('ecs_vcpu'),
            'ecs_vram'            => $ecsData->sum('ecs_vram'),

            // Backup capacities
            'kl_full_backup_capacity'             => $klFullBackupCapacity,
            'cyber_full_backup_capacity'          => $cyberFullBackupCapacity,
            'kl_incremental_backup_capacity'      => $klIncrementalBackupCapacity,
            'cyber_incremental_backup_capacity'   => $cyberIncrementalBackupCapacity,
            'kl_replication_retention_capacity'   => $klReplicationRetentionCapacity,
            'cyber_replication_retention_capacity'=> $cyberReplicationRetentionCapacity,

            // DR (rows)
            'kl_cold_dr_days'          => $coldDrDaysKL,
            'cyber_cold_dr_days'       => $coldDrDaysCJ,
            'kl_cold_dr_seeding_vm'    => $coldDrSeedingVMKL,
            'cyber_cold_dr_seeding_vm' => $coldDrSeedingVMCJ,
            'kl_dr_storage'            => $drStorageKL,
            'cyber_dr_storage'         => $drStorageCJ,
            'kl_dr_replication'        => $drReplicationKL,
            'cyber_dr_replication'     => $drReplicationCJ,
            'kl_dr_declaration'        => $drDeclarationKL,
            'cyber_dr_declaration'     => $drDeclarationCJ,
            'kl_dr_managed_service'    => $drManagedServiceKL,
            'cyber_dr_managed_service' => $drManagedServiceCJ,

            // DR Net/Sec
            'kl_dr_vpll'                 => $klDrVpll,
            'cyber_dr_vpll'              => $cyberDrVpll,
            'kl_dr_elastic_ip'           => $klDrEip,
            'cyber_dr_elastic_ip'        => $cyberDrEip,
            'kl_dr_bandwidth'            => $klDrBw,
            'cyber_dr_bandwidth'         => $cyberDrBw,
            'kl_dr_bandwidth_antiddos'   => $klDrBwAnti,
            'cyber_dr_bandwidth_antiddos'=> $cyberDrBwAnti,
            'kl_dr_firewall_fortigate'   => $klDrForti,
            'cyber_dr_firewall_fortigate'=> $cyberDrForti,
            'kl_dr_firewall_opnsense'    => $klDrOpn,
            'cyber_dr_firewall_opnsense' => $cyberDrOpn,

            // License Summary (normal)
            'kl_windows_std' => $licenseSummary['windows_std']['Kuala Lumpur'] ?? 0,
            'cyber_windows_std' => $licenseSummary['windows_std']['Cyberjaya'] ?? 0,
            'kl_windows_dc' => $licenseSummary['windows_dc']['Kuala Lumpur'] ?? 0,
            'cyber_windows_dc' => $licenseSummary['windows_dc']['Cyberjaya'] ?? 0,
            'kl_rds' => $licenseSummary['rds']['Kuala Lumpur'] ?? 0,
            'cyber_rds' => $licenseSummary['rds']['Cyberjaya'] ?? 0,
            'kl_sql_web' => $licenseSummary['sql_web']['Kuala Lumpur'] ?? 0,
            'cyber_sql_web' => $licenseSummary['sql_web']['Cyberjaya'] ?? 0,
            'kl_sql_std' => $licenseSummary['sql_std']['Kuala Lumpur'] ?? 0,
            'cyber_sql_std' => $licenseSummary['sql_std']['Cyberjaya'] ?? 0,
            'kl_sql_ent' => $licenseSummary['sql_ent']['Kuala Lumpur'] ?? 0,
            'cyber_sql_ent' => $licenseSummary['sql_ent']['Cyberjaya'] ?? 0,
            'kl_rhel_1_8' => $licenseSummary['rhel_1_8']['Kuala Lumpur'] ?? 0,
            'cyber_rhel_1_8' => $licenseSummary['rhel_1_8']['Cyberjaya'] ?? 0,
            'kl_rhel_9_127' => $licenseSummary['rhel_9_127']['Kuala Lumpur'] ?? 0,
            'cyber_rhel_9_127' => $licenseSummary['rhel_9_127']['Cyberjaya'] ?? 0,

            // DR Licenses (helper result)
            'kl_dr_license_months' => $drLic['kl']['license_months'] ?? 0,
            'cyber_dr_license_months' => $drLic['cj']['license_months'] ?? 0,
            'kl_dr_windows_std' => $drLic['kl']['windows_std'] ?? 0,
            'cyber_dr_windows_std' => $drLic['cj']['windows_std'] ?? 0,
            'kl_dr_windows_dc' => $drLic['kl']['windows_dc'] ?? 0,
            'cyber_dr_windows_dc' => $drLic['cj']['windows_dc'] ?? 0,
            'kl_dr_rds' => $drLic['kl']['rds'] ?? 0,
            'cyber_dr_rds' => $drLic['cj']['rds'] ?? 0,
            'kl_dr_sql_web' => $drLic['kl']['sql_web'] ?? 0,
            'cyber_dr_sql_web' => $drLic['cj']['sql_web'] ?? 0,
            'kl_dr_sql_std' => $drLic['kl']['sql_std'] ?? 0,
            'cyber_dr_sql_std' => $drLic['cj']['sql_std'] ?? 0,
            'kl_dr_sql_ent' => $drLic['kl']['sql_ent'] ?? 0,
            'cyber_dr_sql_ent' => $drLic['cj']['sql_ent'] ?? 0,
            'kl_dr_rhel_1_8' => $drLic['kl']['rhel_1_8'] ?? 0,
            'cyber_dr_rhel_1_8' => $drLic['cj']['rhel_1_8'] ?? 0,
            'kl_dr_rhel_9_127' => $drLic['kl']['rhel_9_127'] ?? 0,
            'cyber_dr_rhel_9_127' => $drLic['cj']['rhel_9_127'] ?? 0,

            // DR EVS during activation
            'kl_evs_dr'    => $klEvsDR,
            'cyber_evs_dr' => $cyberEvsDR,
            

          
            'is_logged' => true,
            'logged_at' => now(),
        ];

        InternalSummary::updateOrCreate(['version_id' => $version->id], $payload);

        return redirect()
            ->route('versions.internal_summary.show', $version->id)
            ->with('status', 'Internal Summary committed & locked.');
    }

    private function getLicenseSummary($ecsData, $flavourMap)
    {
        $regions = ['Kuala Lumpur', 'Cyberjaya'];
        $summary = [];

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

   
    private function getDrLicenseSummary(
        \Illuminate\Support\Collection $ecsData,
        \Illuminate\Support\Collection|array $flavourMap,
        bool $isKL,
        bool $isCJ,
        int $coldDrDaysKL,
        int $coldDrDaysCJ
    ): array {
       
        $destSite   = $isKL ? 'Kuala Lumpur' : ($isCJ ? 'Cyberjaya' : null);
        $sourceSite = $isKL ? 'Cyberjaya'    : ($isCJ ? 'Kuala Lumpur' : null);

       
        if (!$destSite || !$sourceSite) {
            $zero = array_fill_keys([
                'license_months','windows_std','windows_dc','rds',
                'sql_web','sql_std','sql_ent','rhel_1_8','rhel_9_127'
            ], 0);
            return ['kl' => $zero, 'cj' => $zero];
        }

        
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

    
        $vm = $ecsData->filter(function ($r) use ($sourceSite) {
            return strcasecmp((string)$r->region, $sourceSite) === 0
                && (string)$r->dr_activation === 'Yes';
        });

     
        $licenseMonths = $isKL
            ? ($coldDrDaysKL > 0 ? (int)ceil($coldDrDaysKL / 30) : 0)
            : ($coldDrDaysCJ > 0 ? (int)ceil($coldDrDaysCJ / 30) : 0);

      
        $winStd = $vm->filter($isWinStd)
                     ->sum(fn($r) => $getInMap($r->ecs_flavour_mapping, 'windows_license_count'));

        $winDc  = $vm->filter($isWinDC)
                     ->sum(fn($r) => $getInMap($r->ecs_flavour_mapping, 'windows_license_count'));

        
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

       
        $kl = $cj = array_fill_keys([
            'license_months','windows_std','windows_dc','rds',
            'sql_web','sql_std','sql_ent','rhel_1_8','rhel_9_127'
        ], 0);

      
        if ($isKL) {                
            $kl['license_months'] = $licenseMonths;  
            $cj['windows_std']    = $winStd;         
            $cj['windows_dc']     = $winDc;
            $cj['rds']            = $rds;
            $cj['sql_web']        = $sqlWeb;
            $cj['sql_std']        = $sqlStd;
            $cj['sql_ent']        = $sqlEnt;
            $cj['rhel_1_8']       = $rhel_1_8;
            $cj['rhel_9_127']     = $rhel_9_127;
        } elseif ($isCJ) {          
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