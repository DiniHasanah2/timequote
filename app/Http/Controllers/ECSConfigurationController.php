<?php

namespace App\Http\Controllers;

use App\Models\ECSConfiguration;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ECSImport;
use App\Imports\ECSConfigurationImport;
use App\Models\InternalSummary;


class ECSConfigurationController extends Controller
{
    public function create($versionId)
    {
       


        $version = Version::with(['project', 'ecs_configuration'])->findOrFail($versionId);
        
    $summary  = InternalSummary::where('version_id', $versionId)->first();
$isLocked = (bool) optional($summary)->is_logged;
$lockedAt = optional($summary)->logged_at;

    $view = request()->route()->getName() === 'versions.backup.create'
        ? 'projects.backup.create'
        : 'projects.ecs_configuration.create';

         //$ddhSummary = $this->getDdhSummaryForVersion($version->id);

         $ddhSummary = $this->getDdhSummaryForVersion($version->getKey());

        
        //return view('projects.ecs_configuration.create', [
        return view($view, [ 
            'project' => $version->project,
            'version' => $version,
            //'ecs_configuration' => $version->ecs_configuration ?? new ECSConfiguration()
            'ecs_configurations' => $version->ecs_configuration,



            'ddhSummary' => $ddhSummary,
         
    'isLocked'        => $isLocked,
    'lockedAt'        => $lockedAt,

            
        ]);
    }


   private function calculateAutoFields(array &$validated)
{
       $validated['csdr_needed'] = $validated['csdr_needed'] ?? 'No';

    // Step 1: csbs_local_retention_copies & Step 6: total_replication_copy_retained_second_site
    if (($validated['csbs_standard_policy'] ?? '') === 'No Backup') {
        $validated['csbs_local_retention_copies'] = 0;
        $validated['total_replication_copy_retained_second_site'] = 0;
    } elseif (($validated['csbs_standard_policy'] ?? '') === 'Custom') {
        $fullCopies = $validated['full_backup_total_retention_full_copies'] ?? 0;
        $incrementalCopies = $validated['incremental_backup_total_retention_incremental_copies'] ?? 0;
        $validated['csbs_local_retention_copies'] = $fullCopies + $incrementalCopies + 1;
    }

   


$initialSize   = (float) ($validated['csbs_initial_data_size'] ?? 0);
$changePercent = (float) ($validated['csbs_incremental_change'] ?? 0);

// keep decimal (contoh 6.3), simpan paparan sebagai int kalau perlu
$estChangeRaw  = $initialSize * ($changePercent / 100.0);
//$validated['csbs_estimated_incremental_data_change'] = (int) $estChangeRaw;

$validated['csbs_estimated_incremental_data_change'] = round($estChangeRaw, 2);

$fullCopies        = (int) ($validated['full_backup_total_retention_full_copies'] ?? 0);
$incrementalCopies = (int) ($validated['incremental_backup_total_retention_incremental_copies'] ?? 0);



$validated['csbs_total_storage'] = (int) ceil(
    $initialSize + ($initialSize * $fullCopies) + ($estChangeRaw * $incrementalCopies)
);



    // Step 6: total_replication_copy_retained_second_site (if required=Yes)
    if (($validated['required'] ?? '') === 'Yes') {
        $validated['total_replication_copy_retained_second_site'] = $validated['csbs_local_retention_copies'] ?? 0;
    }

    // Step 7: RPO
   if (!empty($validated['rto']) && is_numeric($validated['rto'])) {
    $validated['rpo'] = '24 hours';
} else {
    $validated['rpo'] = 'N/A';
}

    // Step 8: csdr_storage
    $systemDisk = $validated['storage_system_disk'] ?? 0;
    $dataDisk = $validated['storage_data_disk'] ?? 0;
    $validated['csdr_storage'] = ($validated['csdr_needed'] === 'Yes') ? ($systemDisk + $dataDisk) : 0;



    // === SUGGESTION FIELDS (tambah di hujung calculateAutoFields) ===
$fullCopies  = (int) ($validated['full_backup_total_retention_full_copies'] ?? 0);
$incrCopies  = (int) ($validated['incremental_backup_total_retention_incremental_copies'] ?? 0);

$systemDisk  = (int) ($validated['storage_system_disk'] ?? 0);
$dataDisk    = (int) ($validated['storage_data_disk'] ?? 0);

$estFull     = (int) ($validated['estimated_storage_full_backup'] ?? 0);
$estIncrChg  = (int) ($validated['csbs_estimated_incremental_data_change'] ?? 0);

// 1) suggestion_estimated_storage_full_backup
$extraFullCopies = max(0, $fullCopies - 1);
$validated['suggestion_estimated_storage_full_backup']
    = $systemDisk + $dataDisk + ($extraFullCopies * $estFull);

// 2) suggestion_estimated_storage_incremental_backup
/*$validated['suggestion_estimated_storage_incremental_backup']
    = $incrCopies * $estIncrChg;*/


    // 2) suggestion_estimated_storage_incremental_backup (guna DECIMAL)
$validated['suggestion_estimated_storage_incremental_backup']
    = (int) ($incrCopies * $estChangeRaw);


// 3) suggestion_estimated_storage_csbs_replication
$validated['suggestion_estimated_storage_csbs_replication']
    = ($validated['suggestion_estimated_storage_full_backup'] ?? 0)
    + ($validated['suggestion_estimated_storage_incremental_backup'] ?? 0);




}



private function persistImportedRows(Version $version, array $rows): void
{
    DB::transaction(function () use ($version, $rows) {
        ECSConfiguration::where('version_id', $version->id)->delete();

        foreach ($rows as $row) {
            /*$region = $row['region'] ?? null;
            $drAct  = $row['dr_activation'] ?? 'No';
            $base   = $row['ecs_flavour_mapping'] ?? null;

            // Cyberjaya + DR Yes -> base .dr (guna helper sedia ada)
            $ecsDr  = ($drAct === 'Yes' && $region === 'Cyberjaya' && $base)
                        ? $this->makeDrName($base)
                        : null;*/



            $region = $row['region'] ?? null;
$drAct  = $row['dr_activation'] ?? 'No';

// Always recompute base mapping IGNORING DDH
$baseMap = $this->calculateFlavourMapping(
    (int)($row['ecs_vcpu'] ?? 0),
    (int)($row['ecs_vram'] ?? 0),
    $row['ecs_pin'] ?? 'No',
    $row['ecs_gpu'] ?? 'No',
    'No' // force-ignore DDH for base mapping
);

// Cyberjaya + DR Yes -> baseMap .dr
$ecsDr  = ($drAct === 'Yes' && $region === 'Cyberjaya' && $baseMap)
            ? $this->makeDrName($baseMap)
            : null;



            ECSConfiguration::create([
                // relationships
                'version_id'  => $version->id,
                'project_id'  => $version->project_id,
                'customer_id' => $version->project->customer_id,
                'presale_id'  => $version->project->presale_id,

                // Production & ECS
                'region'                => $row['region'] ?? 'Kuala Lumpur',
                'vm_name'               => $row['vm_name'] ?? '',
                'ecs_pin'               => $row['ecs_pin'] ?? 'No',
                'ecs_gpu'               => $row['ecs_gpu'] ?? 'No',
                'ecs_ddh'               => $row['ecs_ddh'] ?? 'No',
                'ecs_vcpu'              => (int)($row['ecs_vcpu'] ?? 0),
                'ecs_vram'              => (int)($row['ecs_vram'] ?? 0),
                //'ecs_flavour_mapping'   => $row['ecs_flavour_mapping'] ?? null,
                'ecs_flavour_mapping'   => $baseMap,


                // Storage
                'storage_system_disk'   => (int)($row['storage_system_disk'] ?? 40),
                'storage_data_disk'     => (int)($row['storage_data_disk'] ?? 0),

                // License
                'license_operating_system' => $row['license_operating_system'] ?? 'Linux',
                'license_rds_license'      => (int)($row['license_rds_license'] ?? 0),
                'license_microsoft_sql'    => $row['license_microsoft_sql'] ?? 'None',

                // Image & Snapshot
                'snapshot_copies'       => (int)($row['snapshot_copies'] ?? 0),
                'additional_capacity'   => (int)($row['additional_capacity'] ?? 0),
                'image_copies'          => (int)($row['image_copies'] ?? 0),

                // CSBS
                'csbs_standard_policy'                 => $row['csbs_standard_policy'] ?? 'No Backup',
                'csbs_local_retention_copies'          => (int)($row['csbs_local_retention_copies'] ?? 0),
                'csbs_total_storage'                   => (int)($row['csbs_total_storage'] ?? 0),
                'csbs_initial_data_size'               => (int)($row['csbs_initial_data_size'] ?? 0),
                'csbs_incremental_change'              => (int)($row['csbs_incremental_change'] ?? 0),
                'csbs_estimated_incremental_data_change'=> (int)($row['csbs_estimated_incremental_data_change'] ?? 0),

                // Full backup
                'full_backup_daily'                    => (int)($row['full_backup_daily'] ?? 0),
                'full_backup_weekly'                   => (int)($row['full_backup_weekly'] ?? 0),
                'full_backup_monthly'                  => (int)($row['full_backup_monthly'] ?? 0),
                'full_backup_yearly'                   => (int)($row['full_backup_yearly'] ?? 0),
                'full_backup_total_retention_full_copies' => (int)($row['full_backup_total_retention_full_copies'] ?? 0),
                'suggestion_estimated_storage_full_backup' => (int)($row['suggestion_estimated_storage_full_backup'] ?? 0),
                'estimated_storage_full_backup'        => (int)($row['estimated_storage_full_backup'] ?? 0),

                // Incremental backup
                'incremental_backup_daily'             => (int)($row['incremental_backup_daily'] ?? 0),
                'incremental_backup_weekly'            => (int)($row['incremental_backup_weekly'] ?? 0),
                'incremental_backup_monthly'           => (int)($row['incremental_backup_monthly'] ?? 0),
                'incremental_backup_yearly'            => (int)($row['incremental_backup_yearly'] ?? 0),
                'incremental_backup_total_retention_incremental_copies' => (int)($row['incremental_backup_total_retention_incremental_copies'] ?? 0),
                'suggestion_estimated_storage_incremental_backup'       => (int)($row['suggestion_estimated_storage_incremental_backup'] ?? 0),
                'estimated_storage_incremental_backup' => (int)($row['estimated_storage_incremental_backup'] ?? 0),

                // Replication / DR
                'required'                              => $row['required'] ?? 'No',
                'total_replication_copy_retained_second_site' => (int)($row['total_replication_copy_retained_second_site'] ?? 0),
                'additional_storage'                    => (int)($row['additional_storage'] ?? 0),
                'rto'                                   => (int)($row['rto'] ?? 0),
                'rpo'                                   => $row['rpo'] ?? 'N/A',
                'suggestion_estimated_storage_csbs_replication' => (int)($row['suggestion_estimated_storage_csbs_replication'] ?? 0),
                'estimated_storage_csbs_replication'    => (int)($row['estimated_storage_csbs_replication'] ?? 0),

                // DR req & CSDR
                'ecs_dr'          => $ecsDr,
                'dr_activation'   => $row['dr_activation'] ?? 'No',
                'seed_vm_required'=> $row['seed_vm_required'] ?? 'No',
                'csdr_needed'     => $row['csdr_needed'] ?? 'No',
                'csdr_storage'    => (int)($row['csdr_storage'] ?? 0),
            ]);
        }

        Log::info('persistImportedRows: rows persisted', ['version_id' => $version->id, 'count' => count($rows)]);
    });
}

public function import(Request $request)
{
       // 🔒 Block import kalau locked
    $summary  = InternalSummary::where('version_id', $request->version_id)->first();
    if (optional($summary)->is_logged) {
        return back()->with('error', '🔒 This version is locked in Internal Summary. Import is disabled.');
    }

    $request->validate([
        'import_file' => 'required|file|mimes:xlsx|max:81920',
        'version_id'  => 'required|exists:versions,id',
    ]);

    try {
        // 1) Import ke ecs_imports (preview storage)
        Excel::import(new ECSConfigurationImport($request->version_id), $request->file('import_file'));

        // 2) Ambil import terbaru utk version ni
        $import = ECSImport::where('version_id', $request->version_id)->latest()->first();

        if (!$import) {
            return back()->withErrors('Import gagal: tiada data disimpan.');
        }

        // 3) Decode preview rows → array
        $decoded = is_array($import->import_data)
            ? $import->import_data
            : (json_decode($import->import_data, true) ?? []);

        if (empty($decoded)) {
            return back()->withErrors('The file was read but no valid rows were found. Please make sure at least Region, VM Name, vCPU, and vRAM are filled in.');
        }

        // 4) Auto-persist (skip preview UI)
        $version = Version::with('project')->findOrFail($request->version_id);
        $this->persistImportedRows($version, $decoded);

        // 5) Clear session preview (kalau ada)
        session()->forget('importPreview');

        // 6) Redirect balik ke page ECS & Backup
        $routeName = $request->input('source', 'ecs') === 'backup'
            ? 'versions.backup.create'
            : 'versions.ecs_configuration.create';

        return redirect()
            ->route($routeName, $request->version_id)
            ->with('success', 'Imported backup data saved successfully!');
    } catch (\Throwable $e) {
        Log::error('Import failed', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return back()->withErrors('Import failed: ' . $e->getMessage());
    }
}





public function store(Request $request, $versionId)
{
   

    $version = Version::with('project')->findOrFail($versionId);

        $summary  = InternalSummary::where('version_id', $versionId)->first();
    if (optional($summary)->is_logged) {
        return back()->with('error', '🔒 This version is locked in Internal Summary. Editing is disabled. Please unlock there if you need to make changes.');
    }


    try {
        DB::transaction(function () use ($request, $version) {

            // === CSV or Multi-row import ===
            if ($request->has('rows')) {
              

                $rows = $request->input('rows', []);

// STEP 1: Tapis rows yang user isi at least `region` dan `vm_name`
$filteredRows = array_filter($rows, function ($row) {
    return !empty($row['region']) && !empty($row['vm_name']);
});

// STEP 2: Kalau semua row kosong, tolak balik
if (empty($filteredRows)) {
    throw new \Exception("At least one row must have 'Region' and 'VM Name' filled.");
}

// STEP 3: Validate only valid rows
foreach ($filteredRows as $i => $row) {
    // Fallback default 0 untuk certain field

                    // Fallback default 0
                    $defaults = [
                        'storage_system_disk', 'storage_data_disk', 'snapshot_copies', 'additional_capacity',
                        'image_copies', 'csbs_initial_data_size', 'csbs_incremental_change',
                        'full_backup_daily', 'full_backup_weekly', 'full_backup_monthly', 'full_backup_yearly',
                        'incremental_backup_daily', 'incremental_backup_weekly', 'incremental_backup_monthly',
                        'incremental_backup_yearly', 'additional_storage'
                    ];
                    foreach ($defaults as $field) {
                        if (!isset($row[$field]) || $row[$field] === '') {
                            $row[$field] = 0;
                        }
                    }


                    

                    // === Validation ===
                    $validated = validator($row, [
                        'region' => 'required|in:Kuala Lumpur,Cyberjaya',
                        'vm_name' => 'required|string|max:255',

                        // ECS
                        'ecs_pin' => 'required|in:Yes,No',
                        'ecs_gpu' => 'required|in:Yes,No',
                        'ecs_ddh' => 'required|in:Yes,No',
                        'ecs_vcpu' => 'required|integer|min:0|max:128',
                        'ecs_vram' => 'required|integer|min:0|max:512',
                        'ecs_flavour_mapping' => 'nullable|string|max:50',

                        // Container Worker
                        'vcpu_count' => 'nullable|integer|min:0|max:128',
                        'vram_count' => 'nullable|integer|min:0|max:512',
                        'worker_flavour_mapping' => 'nullable|string|max:50',

                        // Storage
                        'storage_system_disk' => 'required|integer|min:40|max:2048',
                        'storage_data_disk' => 'nullable|integer|min:0|max:10000',

                        // License
                        'license_operating_system' => 'nullable|in:Linux,Microsoft Windows Std,Microsoft Windows DC,Red Hat Enterprise Linux',
                        'license_rds_license' => 'nullable|integer|min:0|max:100',
                        'license_microsoft_sql' => 'nullable|in:None,Web,Standard,Enterprise',

                        // Image/Snapshot
                        'snapshot_copies' => 'nullable|integer|min:0|max:365',
                        'additional_capacity' => 'nullable|integer|min:0|max:1000',
                        'image_copies' => 'nullable|integer|min:0|max:100',

                        // CSBS
                        'csbs_standard_policy' => 'nullable|in:No Backup,Custom',
                        //'csbs_local_retention_copies' => 'nullable|integer|min:1|max:365',
                        'csbs_local_retention_copies' => 'nullable|integer|min:0|max:365',
                        //'csbs_total_storage' => 'nullable|integer|min:1|max:10000',
                        'csbs_total_storage' => 'nullable|integer|min:0|max:10000',
                        //'csbs_initial_data_size' => 'nullable|integer|min:1|max:10000',
                        'csbs_initial_data_size' => 'nullable|integer|min:0|max:10000',
                        'csbs_incremental_change' => 'nullable|integer|min:0|max:100',
                        'csbs_estimated_incremental_data_change' => 'nullable|numeric|min:0|max:10000',

                        // Full Backup
                        'full_backup_daily' => 'nullable|integer|min:0|max:30',
                        'full_backup_weekly' => 'nullable|integer|min:0|max:52',
                        'full_backup_monthly' => 'nullable|integer|min:0|max:12',
                        'full_backup_yearly' => 'nullable|integer|min:0|max:10',
                        'full_backup_total_retention_full_copies' => 'nullable|integer|min:0|max:1000',
                        'estimated_storage_full_backup'=> 'nullable|integer|min:0|max:1000',
                        // Incremental Backup
                        'incremental_backup_daily' => 'nullable|integer|min:0|max:30',
                        'incremental_backup_weekly' => 'nullable|integer|min:0|max:52',
                        'incremental_backup_monthly' => 'nullable|integer|min:0|max:12',
                        'incremental_backup_yearly' => 'nullable|integer|min:0|max:10',
                        'incremental_backup_total_retention_incremental_copies' => 'nullable|integer|min:0|max:1000',
                        'estimated_storage_incremental_backup'=> 'nullable|integer|min:0|max:1000',

                        // CSBS Replication
                        'required' => 'nullable|in:Yes,No',
                        'total_replication_copy_retained_second_site' => 'nullable|integer|min:0|max:1000',
                        'additional_storage' => 'nullable|integer|min:0|max:10000',
                        'rto' => 'nullable|integer',
                        'rpo' => 'nullable|string|in:24 hours,N/A',
                        'estimated_storage_csbs_replication'=> 'nullable|integer|min:0|max:1000',

                        // DR Requirement
                        'ecs_dr' => 'nullable|string',//'nullable|in:Yes,No'
                        'dr_activation' => 'nullable|in:Yes,No',
                        'seed_vm_required' => 'nullable|in:Yes,No',

                        // CSDR
                        'csdr_needed' => 'nullable|in:Yes,No',
                        'csdr_storage' => 'nullable|integer|min:0|max:10000',

                        'suggestion_estimated_storage_full_backup' => 'nullable|numeric|min:0',
    'suggestion_estimated_storage_incremental_backup' => 'nullable|numeric|min:0',
    'suggestion_estimated_storage_csbs_replication' => 'nullable|numeric|min:0',
                    ])->validate();

                    // Derived values
                    //$validated['ecs_flavour_mapping'] = $this->calculateFlavourMapping($validated['ecs_vcpu'] ?? 0, $validated['ecs_vram'] ?? 0);
                    // Derived values (pass pin, gpu, ddh supaya ikut sama dengan JS logic)
$validated['ecs_flavour_mapping'] = $this->calculateFlavourMapping(
    $validated['ecs_vcpu'] ?? 0,
    $validated['ecs_vram'] ?? 0,
    $validated['ecs_pin'] ?? 'No',
    $validated['ecs_gpu'] ?? 'No',
    $validated['ecs_ddh'] ?? 'No'
);

                    $this->calculateAutoFields($validated);

                    $region = $validated['region'] ?? null;
$drAct  = $validated['dr_activation'] ?? 'No';
$base   = $validated['ecs_flavour_mapping'] ?? null;

if ($drAct === 'Yes' && $region === 'Cyberjaya') {
    $validated['ecs_dr'] = $this->makeDrName($base);
} else {
    $validated['ecs_dr'] = null;
}


                    // Related foreign keys
                    $validated['project_id'] = $version->project_id;
                    $validated['version_id'] = $version->id;
                    $validated['customer_id'] = $version->project->customer_id;
                    $validated['presale_id'] = $version->project->presale_id;

                   
                    if (!empty($row['id'])) {
    // Update existing
    $ecs = ECSConfiguration::find($row['id']);
    if ($ecs) {
        $ecs->update($validated);
    }
} else {
    // New create
    ECSConfiguration::create($validated);
}

                }
            }

            else {

    Log::info('Manual ECS Submit:', $request->all());

    $validated = $this->validateRequest($request);

    // Kosong string → null
    foreach ($validated as $key => $value) {
        if ($value === '') $validated[$key] = null;
    }

    // (1) KIRA RETENTION TOTALS DULU (PERLU sebelum calculateAutoFields)
    $validated['full_backup_total_retention_full_copies'] =
        (int)($validated['full_backup_daily'] ?? 0) +
        (int)($validated['full_backup_weekly'] ?? 0) +
        (int)($validated['full_backup_monthly'] ?? 0) +
        (int)($validated['full_backup_yearly'] ?? 0);

    $validated['incremental_backup_total_retention_incremental_copies'] =
        (int)($validated['incremental_backup_daily'] ?? 0) +
        (int)($validated['incremental_backup_weekly'] ?? 0) +
        (int)($validated['incremental_backup_monthly'] ?? 0) +
        (int)($validated['incremental_backup_yearly'] ?? 0);

    // (2) Flavour mapping (ikut pin/gpu/ddh)
    $validated['ecs_flavour_mapping'] = $this->calculateFlavourMapping(
        $validated['ecs_vcpu'] ?? 0,
        $validated['ecs_vram'] ?? 0,
        $validated['ecs_pin']  ?? 'No',
        $validated['ecs_gpu']  ?? 'No',
        $validated['ecs_ddh']  ?? 'No'
    );

    // (3) Auto fields ikut OPTION A (kau dah ubah dalam calculateAutoFields())
    $this->calculateAutoFields($validated);

    // (4) Nama DR (.dr) bila Cyberjaya + DR Yes
    $region = $validated['region'] ?? null;
    $drAct  = $validated['dr_activation'] ?? 'No';
    $base   = $validated['ecs_flavour_mapping'] ?? null;
    $validated['ecs_dr'] = ($drAct === 'Yes' && $region === 'Cyberjaya' && $base)
        ? $this->makeDrName($base)
        : null;


    ECSConfiguration::updateOrCreate(
        ['version_id' => $version->id],
        $validated + [
            'project_id'  => $version->project_id,
            'customer_id' => $version->project->customer_id,
            'presale_id'  => $version->project->presale_id,
        ]
    );
}

        });

        session()->forget('importPreview');

        return redirect()->back()->with('success', 'Data saved successfully!');
    } catch (\Exception $e) {
        Log::error('Error saving ECS Configuration:', ['error' => $e->getMessage()]);
        return back()->withInput()->with('error', 'Failed to save: ' . $e->getMessage());
    }
}

    



    private function validateRequest(Request $request): array
    {
       return $request->validate([
    // Basic Info
    'region' => 'required|in:Kuala Lumpur,Cyberjaya',
    'vm_name' => 'required|string|max:255',
    
    // ECS
    'ecs_pin' => 'required|in:Yes,No',
    'ecs_gpu' => 'required|in:Yes,No',
    'ecs_ddh' => 'required|in:Yes,No',
    'ecs_vcpu' => 'required|integer|min:0|max:128', //sometimes|nullable
    'ecs_vram' => 'required|integer|min:0|max:512',
    'ecs_flavour_mapping' => 'nullable|string',
    
    // Container Worker (hidden)
    
    'vcpu_count' => 'sometimes|nullable|integer|min:0|max:128',
    'vram_count' => 'sometimes|nullable|integer|min:0|max:512',
    'worker_flavour_mapping' => 'nullable|string|max:50',
    
    // Storage
    'storage_system_disk' => 'required|integer|min:40|max:2048',
    'storage_data_disk' => 'nullable|integer|min:0|max:10000',
    
    // License
    'license_operating_system' => 'nullable|in:Linux,Microsoft Windows Std,Microsoft Windows DC,Red Hat Enterprise Linux',
    'license_rds_license' => 'nullable|integer|max:100',
    'license_microsoft_sql' => 'nullable|in:None,Web,Standard,Enterprise',
    
    // Image/Snapshot
    'snapshot_copies' => 'nullable|integer|min:0|max:365',
    'additional_capacity' => 'nullable|integer|min:0|max:1000',
    'image_copies' => 'nullable|integer|min:0|max:100',
    
    // CSBS
    'csbs_standard_policy' => 'nullable|in:No Backup,Custom',
    'csbs_local_retention_copies' => 'nullable|integer|min:0|max:365',
    'csbs_total_storage' => 'nullable|integer|min:0|max:10000',
    'csbs_initial_data_size' => 'nullable|integer|min:0|max:10000',
    'csbs_incremental_change' => 'nullable|integer|min:0|max:100',
    'csbs_estimated_incremental_data_change' => 'sometimes|nullable|numeric|min:0|max:10000',
    
    // Full Backup
    'full_backup_daily' => 'nullable|integer|min:0|max:30',
    'full_backup_weekly' => 'nullable|integer|min:0|max:52',
    'full_backup_monthly' => 'nullable|integer|min:0|max:12',
    'full_backup_yearly' => 'nullable|integer|min:0|max:10',
    'full_backup_total_retention_full_copies' => 'sometimes|nullable|integer|min:0|max:1000',
    'estimated_storage_full_backup'=> 'nullable|integer|min:0|max:1000',
    
    // Incremental Backup
    'incremental_backup_daily' => 'nullable|integer|min:0|max:30',
    'incremental_backup_weekly' => 'nullable|integer|min:0|max:52',
    'incremental_backup_monthly' => 'nullable|integer|min:0|max:12',
    'incremental_backup_yearly' => 'nullable|integer|min:0|max:10',
    'incremental_backup_total_retention_incremental_copies' => 'sometimes|nullable|integer|min:0|max:1000',
    'estimated_storage_incremental_backup'=> 'nullable|integer|min:0|max:1000',
    
    // CSBS Replication
    'required' => 'nullable|in:Yes,No',
    'total_replication_copy_retained_second_site' => 'nullable|integer|min:0|max:1000',
    'additional_storage' => 'nullable|integer|min:0|max:10000',
    'rto' => 'nullable|integer',
    'rpo' => 'nullable|string|in:24 hours,N/A',
    'estimated_storage_csbs_replication'=> 'nullable|integer|min:0|max:1000',
    
    // DR Requirement
    'ecs_dr' => 'nullable|string',//'nullable|in:Yes,No'
    'dr_activation' => 'nullable|in:Yes,No',
    'seed_vm_required' => 'nullable|in:Yes,No',
    
    // CSDR
    'csdr_needed' => 'nullable|in:Yes,No',
    'csdr_storage' => 'nullable|integer|min:0|max:10000',


    'suggestion_estimated_storage_full_backup' => 'nullable|numeric|min:0',
    'suggestion_estimated_storage_incremental_backup' => 'nullable|numeric|min:0',
    'suggestion_estimated_storage_csbs_replication' => 'nullable|numeric|min:0',
]);
    }

    //private function calculateFlavourMapping(?int $vcpu, ?int $vram): string
    private function calculateFlavourMapping(?int $vcpu, ?int $vram, string $pin = 'No', string $gpu = 'No', string $ddh = 'No'): string
{
    $vcpu = $vcpu ?? 0;
    $vram = $vram ?? 0;
   
    $flavours = [
    ['name' => 'm3.micro', 'vcpu' => 1, 'vram' => 1, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name'=> 'm3.small', 'vcpu'=> 1, 'vram'=> 2, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3.large', 'vcpu' => 2, 'vram' => 4, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name'=> 'm3.large', 'vcpu' => 2, 'vram' => 8, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name'=> 'r3.large', 'vcpu' => 2, 'vram' => 16, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3.xlarge', 'vcpu' => 4, 'vram'=> 8, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name'=> 'm3.xlarge', 'vcpu' => 4, 'vram' => 16, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3.xlarge', 'vcpu'=> 4, 'vram'=> 32, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3.2xlarge', 'vcpu'=> 8, 'vram'=> 16, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3.2xlarge', 'vcpu'=> 8, 'vram'=> 32, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name'=> 'r3.2xlarge', 'vcpu'=> 8, 'vram'=> 64, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name'=> 'm3.3xlarge', 'vcpu'=> 12, 'vram'=> 48, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3.4xlarge', 'vcpu' => 16, 'vram' => 32, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3.4xlarge', 'vcpu' => 16, 'vram' => 64, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3.4xlarge', 'vcpu' => 16, 'vram' => 128, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3.6xlarge', 'vcpu' => 24, 'vram' => 96, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3.8xlarge', 'vcpu' => 32, 'vram' => 64, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3.8xlarge', 'vcpu' => 32, 'vram' => 128, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3.8xlarge', 'vcpu' => 32, 'vram' => 256, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3.12xlarge', 'vcpu' => 48, 'vram' => 384, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3.16xlarge', 'vcpu' => 64, 'vram' => 128, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3.16xlarge', 'vcpu' => 64, 'vram' => 256, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3.16xlarge', 'vcpu' => 64, 'vram' => 512, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3p.xlarge', 'vcpu' => 4, 'vram' => 8, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.xlarge', 'vcpu' => 4, 'vram' => 16, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3p.xlarge', 'vcpu' => 4, 'vram' => 32, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3p.2xlarge', 'vcpu' => 8, 'vram' => 16, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.2xlarge', 'vcpu' => 8, 'vram' => 32, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3p.2xlarge', 'vcpu' => 8, 'vram' => 64, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.3xlarge', 'vcpu' => 12, 'vram' => 48, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3p.4xlarge', 'vcpu' => 16, 'vram' => 32, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.4xlarge', 'vcpu' => 16, 'vram' => 64, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3p.4xlarge', 'vcpu' => 16, 'vram' => 64, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.6xlarge', 'vcpu' => 24, 'vram' => 96, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'c3p.8xlarge', 'vcpu' => 32, 'vram' => 64, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.8xlarge', 'vcpu' => 32, 'vram' => 128, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3p.8xlarge', 'vcpu' => 32, 'vram' => 128, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.12xlarge', 'vcpu' => 48, 'vram' => 192, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3p.12xlarge', 'vcpu' => 48, 'vram' => 384, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3p.16xlarge', 'vcpu' => 64, 'vram' => 256, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3p.16xlarge', 'vcpu' => 64, 'vram' => 512, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'r3p.46xlarge.metal', 'vcpu' => 64, 'vram' => 1408, 'pin' => 'Yes', 'gpu' => 'No', 'ddh' => 'No'],
    ['name' => 'm3gnt4.xlarge', 'vcpu' => 4, 'vram' => 16, 'pin' => 'No', 'gpu' => 'Yes', 'ddh' => 'No'],
    ['name' => 'm3gnt4.2xlarge', 'vcpu' => 8, 'vram' => 32, 'pin' => 'No', 'gpu' => 'Yes', 'ddh' => 'No'],
    ['name' => 'm3gnt4.4xlarge', 'vcpu' => 16, 'vram' => 64, 'pin' => 'No', 'gpu' => 'Yes', 'ddh' => 'No'],
    ['name' => 'm3gnt4.8xlarge', 'vcpu' => 32, 'vram' => 128, 'pin' => 'No', 'gpu' => 'Yes', 'ddh' => 'No'],
    ['name' => 'm3gnt4.16xlarge', 'vcpu' => 64, 'vram' => 256, 'pin' => 'No', 'gpu' => 'Yes', 'ddh' => 'No'],
    ['name' => 'r3p.46xlarge.ddh', 'vcpu' => 342, 'vram' => 1480, 'pin' => 'No', 'gpu' => 'No', 'ddh' => 'Yes'],
];


     $pool = collect($flavours)->where('ddh', 'No');

    // Try exact match on PIN/GPU + capacity
    $suitable = $pool
        ->where('vcpu', '>=', $vcpu)
        ->where('vram', '>=', $vram)
        ->where('pin', $pin)
        ->where('gpu', $gpu)
        ->sortBy([['vcpu', 'asc'], ['vram', 'asc']])
        ->first();

    // Fallback: only capacity
    if (!$suitable) {
        $suitable = $pool
            ->where('vcpu', '>=', $vcpu)
            ->where('vram', '>=', $vram)
            ->sortBy([['vcpu', 'asc'], ['vram', 'asc']])
            ->first();
    }

    return $suitable['name'] ?? 'No suitable flavour';
}


  /*$suitable = collect($flavours)
        ->where('vcpu', '>=', $vcpu)
        ->where('vram', '>=', $vram)
        ->where('pin', $pin)
        ->where('gpu', $gpu)
        ->where('ddh', $ddh)
        ->sortBy([
            ['vcpu', 'asc'],
            ['vram', 'asc'],
        ])
        ->first();

    return $suitable['name'] ?? 'No suitable flavour';
}*/




public function destroy($id)
{
    $config = ECSConfiguration::findOrFail($id);
      // 🔒 Block delete kalau locked
    $summary  = InternalSummary::where('version_id', $config->version_id)->first();
    if (optional($summary)->is_logged) {
        return response()->json([
            'success' => false,
            'message' => '🔒 Locked: cannot delete rows.'
        ], 423); // 423 Locked
    }

    $config->delete();

    

    return response()->json(['success' => true]);
}


public function bulkDestroy(Request $request)
{
    $validated = $request->validate([
        'ids'        => 'required|array',
        'ids.*'      => 'string',
        'version_id' => 'required|string',
    ]);

    // 🔒 Block bulk delete kalau locked
    $summary  = InternalSummary::where('version_id', $validated['version_id'])->first();
    if (optional($summary)->is_logged) {
        return response()->json([
            'success' => false,
            'message' => '🔒 Locked: bulk delete disabled.'
        ], 423);
    }

    $deleted = ECSConfiguration::where('version_id', $validated['version_id'])
        ->whereIn('id', $validated['ids'])
        ->delete();

    return response()->json(['success' => true, 'deleted' => $deleted]);
}






public function storePreview(Request $request, $versionId)
{
      $summary  = InternalSummary::where('version_id', $versionId)->first();
    if (optional($summary)->is_logged) {
        return back()->with('error', '🔒 This version is locked in Internal Summary. Saving preview is disabled.');
    }
    $previewData = session('importPreview');

    if (!is_array($previewData) || empty($previewData)) {
        return back()->with('error', 'No preview data found. Sila attach fail dulu.');
    }

    $version = Version::with('project')->findOrFail($versionId);

    DB::transaction(function () use ($version, $previewData) {
        // 1) Buang data lama utk version ni (ikut nota "Data will automatically be replaced...")
        ECSConfiguration::where('version_id', $version->id)->delete();

        // 2) Simpan row baharu
        foreach ($previewData as $row) {
            // Kira ecs_dr: Cyberjaya + DR Yes => base .dr
            $region = $row['region'] ?? null;
            $drAct  = $row['dr_activation'] ?? 'No';
            $base   = $row['ecs_flavour_mapping'] ?? null;
            $ecsDr  = ($drAct === 'Yes' && $region === 'Cyberjaya' && $base) ? $this->makeDrName($base) : null;


            ECSConfiguration::create([
                // relationships
                'version_id'  => $version->id,
                'project_id'  => $version->project->id,
                'customer_id' => $version->project->customer_id,
                'presale_id'  => $version->project->presale_id,

                // Production & ECS
                'region'                => $row['region'] ?? 'Kuala Lumpur',
                'vm_name'               => $row['vm_name'] ?? '',
                'ecs_pin'               => $row['ecs_pin'] ?? 'No',
                'ecs_gpu'               => $row['ecs_gpu'] ?? 'No',
                'ecs_ddh'               => $row['ecs_ddh'] ?? 'No',
                'ecs_vcpu'              => $row['ecs_vcpu'] ?? 0,
                'ecs_vram'              => $row['ecs_vram'] ?? 0,
                //'ecs_flavour_mapping'   => $row['ecs_flavour_mapping'] ?? null,
                'ecs_flavour_mapping'   => $baseMap,


                // Storage
                'storage_system_disk'   => $row['storage_system_disk'] ?? 40,
                'storage_data_disk'     => $row['storage_data_disk'] ?? 0,

                // License
                'license_operating_system' => $row['license_operating_system'] ?? 'Linux',
                'license_rds_license'      => $row['license_rds_license'] ?? 0,
                'license_microsoft_sql'    => $row['license_microsoft_sql'] ?? 'None',

                // Image & Snapshot
                'snapshot_copies'       => $row['snapshot_copies'] ?? 0,
                'additional_capacity'   => $row['additional_capacity'] ?? 0,
                'image_copies'          => $row['image_copies'] ?? 0,

                // CSBS
                'csbs_standard_policy'                 => $row['csbs_standard_policy'] ?? 'No Backup',
                'csbs_local_retention_copies'          => $row['csbs_local_retention_copies'] ?? 0,
                'csbs_total_storage'                   => $row['csbs_total_storage'] ?? 0,
                'csbs_initial_data_size'               => $row['csbs_initial_data_size'] ?? 0,
                'csbs_incremental_change'              => $row['csbs_incremental_change'] ?? 0,
                'csbs_estimated_incremental_data_change'=> $row['csbs_estimated_incremental_data_change'] ?? 0,

                // Full backup
                'full_backup_daily'                    => $row['full_backup_daily'] ?? 0,
                'full_backup_weekly'                   => $row['full_backup_weekly'] ?? 0,
                'full_backup_monthly'                  => $row['full_backup_monthly'] ?? 0,
                'full_backup_yearly'                   => $row['full_backup_yearly'] ?? 0,
                'full_backup_total_retention_full_copies' => $row['full_backup_total_retention_full_copies'] ?? 0,
                'suggestion_estimated_storage_full_backup' => $row['suggestion_estimated_storage_full_backup'] ?? 0,
                'estimated_storage_full_backup'        => $row['estimated_storage_full_backup'] ?? 0,

                // Incremental backup
                'incremental_backup_daily'             => $row['incremental_backup_daily'] ?? 0,
                'incremental_backup_weekly'            => $row['incremental_backup_weekly'] ?? 0,
                'incremental_backup_monthly'           => $row['incremental_backup_monthly'] ?? 0,
                'incremental_backup_yearly'            => $row['incremental_backup_yearly'] ?? 0,
                'incremental_backup_total_retention_incremental_copies' => $row['incremental_backup_total_retention_incremental_copies'] ?? 0,
                'suggestion_estimated_storage_incremental_backup'       => $row['suggestion_estimated_storage_incremental_backup'] ?? 0,
                'estimated_storage_incremental_backup' => $row['estimated_storage_incremental_backup'] ?? 0,

                // Replication / DR
                'required'                              => $row['required'] ?? 'No',
                'total_replication_copy_retained_second_site' => $row['total_replication_copy_retained_second_site'] ?? 0,
                'additional_storage'                    => $row['additional_storage'] ?? 0,
                'rto'                                   => $row['rto'] ?? 0,
                'rpo'                                   => $row['rpo'] ?? 'N/A',
                'suggestion_estimated_storage_csbs_replication' => $row['suggestion_estimated_storage_csbs_replication'] ?? 0,
                'estimated_storage_csbs_replication'    => $row['estimated_storage_csbs_replication'] ?? 0,

                // DR req & CSDR
                'ecs_dr'          => $ecsDr,
                'dr_activation'   => $row['dr_activation'] ?? 'No',
                'seed_vm_required'=> $row['seed_vm_required'] ?? 'No',
                'csdr_needed'     => $row['csdr_needed'] ?? 'No',
                'csdr_storage'    => $row['csdr_storage'] ?? 0,
            ]);
        }
    });

    session()->forget('importPreview');


    return redirect()
        ->route('versions.backup.create', $versionId)
        ->with('success', 'Imported backup data saved successfully!');
}


    

    private function calculateRdsLicense(array $data): string
    {
        if (str_contains($data['license_operating_system'], 'Windows')) {
            return 'RDS License required';
        }
        return 'No RDS License required';
    }

    private function calculateIncrementalData(int $initialSize, int $changePercent): int
    {
        return (int) round($initialSize * ($changePercent / 100));
    }




private function makeDrName(?string $base): ?string
{
    if (!$base) return null;
    $candidate = trim($base) . '.dr';

    $pricing = config('pricing', []);

    // Cari dalam semua items pricing yang ada 'name' == $candidate
    foreach ($pricing as $code => $item) {
        if (is_array($item) && isset($item['name']) && $item['name'] === $candidate) {
            return $candidate; // jumpa, confirm
        }
    }

   
    return $candidate;

}




private function getDdhSummaryForVersion($versionId)
{
    $rows = ECSConfiguration::query()
        ->where('version_id', $versionId)
        ->whereRaw('LOWER(TRIM(ecs_ddh)) = ?', ['yes'])
        ->selectRaw('TRIM(region) as region')
        ->selectRaw('SUM(ecs_vcpu) as total_vcpu')
        ->selectRaw('SUM(ecs_vram) as total_vram')
        ->groupBy('region')
        ->get();

    $kl = $rows->firstWhere('region', 'Kuala Lumpur');
    $cj = $rows->firstWhere('region', 'Cyberjaya');

    $kl_vcpu = (int)($kl->total_vcpu ?? 0);
    $kl_vram = (int)($kl->total_vram ?? 0);
    $cj_vcpu = (int)($cj->total_vcpu ?? 0);
    $cj_vram = (int)($cj->total_vram ?? 0);

    $kl_num_ddh = ($kl_vcpu||$kl_vram) ? (int)ceil(max($kl_vcpu/144, $kl_vram/1408)) : 0;
    $cj_num_ddh = ($cj_vcpu||$cj_vram) ? (int)ceil(max($cj_vcpu/144, $cj_vram/1408)) : 0;

    return [
        'kl' => ['total_vcpu'=>$kl_vcpu,'total_vram'=>$kl_vram,'num_ddh'=>$kl_num_ddh],
        'cj' => ['total_vcpu'=>$cj_vcpu,'total_vram'=>$cj_vram,'num_ddh'=>$cj_num_ddh],
    ];
}



    
}