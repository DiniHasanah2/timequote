<?php

namespace App\Imports;

use App\Models\ECSImport;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ECSConfigurationImport implements ToCollection, WithCalculatedFormulas
{
    protected $versionId;

    public function __construct($versionId)
    {
        $this->versionId = $versionId;
    }

    public function collection(Collection $rows)
    {
        $data = [];

        // Helper ringkas untuk ambil nilai sel dengan selamat
        $cell = function ($row, $i) {
            return isset($row[$i]) ? (is_string($row[$i]) ? trim($row[$i]) : $row[$i]) : null;
        };

        // Skip 3 baris header (ikut file anda)
        foreach ($rows->skip(3) as $row) {

            // Ambil key fields (guna default kalau kosong)
            $region = (string) ($row[0] ?? '');
            $vm     = (string) ($row[1] ?? '');
            $vcpu   = (int)    ($row[5] ?? 0);
            $vram   = (int)    ($row[6] ?? 0);

            // Hanya skip kalau MEMANG kosong semua key fields
            if ($region === '' && $vm === '' && $vcpu === 0 && $vram === 0) {
                continue;
            }

            // Map semua kolum dengan ?? null (supaya tak crash/skip kalau kurang lajur)
            $data[] = [
                'region'                                          => $cell($row, 0),
                'vm_name'                                         => $cell($row, 1),
                'ecs_pin'                                         => $cell($row, 2),
                'ecs_gpu'                                         => $cell($row, 3),
                'ecs_ddh'                                         => $cell($row, 4),
                'ecs_vcpu'                                        => $cell($row, 5),
                'ecs_vram'                                        => $cell($row, 6),
                'ecs_flavour_mapping'                             => $cell($row, 7),
                'storage_system_disk'                              => $cell($row, 8),
                'storage_data_disk'                                => $cell($row, 9),
                'license_operating_system'                         => $cell($row,10),
                'license_rds_license'                              => $cell($row,11),
                'license_microsoft_sql'                            => $cell($row,12),
                'snapshot_copies'                                  => $cell($row,13),
                'additional_capacity'                              => $cell($row,14),
                'image_copies'                                     => $cell($row,15),
                'csbs_standard_policy'                             => $cell($row,16),
                'csbs_local_retention_copies'                      => $cell($row,17),
                'csbs_total_storage'                               => $cell($row,18),
                'csbs_initial_data_size'                           => $cell($row,19),
                'csbs_incremental_change'                          => $cell($row,20),
                'csbs_estimated_incremental_data_change'           => $cell($row,21),
                'full_backup_daily'                                => $cell($row,22),
                'full_backup_weekly'                               => $cell($row,23),
                'full_backup_monthly'                              => $cell($row,24),
                'full_backup_yearly'                               => $cell($row,25),
                'full_backup_total_retention_full_copies'          => $cell($row,26),
                'suggestion_estimated_storage_full_backup'         => $cell($row,27),
                'estimated_storage_full_backup'                    => $cell($row,28),
                'incremental_backup_daily'                         => $cell($row,29),
                'incremental_backup_weekly'                        => $cell($row,30),
                'incremental_backup_monthly'                       => $cell($row,31),
                'incremental_backup_yearly'                        => $cell($row,32),
                'incremental_backup_total_retention_incremental_copies' => $cell($row,33),
                'suggestion_estimated_storage_incremental_backup'  => $cell($row,34),
                'estimated_storage_incremental_backup'             => $cell($row,35),
                'required'                                         => $cell($row,36),
                'total_replication_copy_retained_second_site'      => $cell($row,37),
                'additional_storage'                               => $cell($row,38),
                'rto'                                              => $cell($row,39),
                'rpo'                                              => $cell($row,40),
                'suggestion_estimated_storage_csbs_replication'    => $cell($row,41),
                'estimated_storage_csbs_replication'               => $cell($row,42),
                'ecs_dr'                                           => $cell($row,43),
                'dr_activation'                                    => $cell($row,44),
                'seed_vm_required'                                 => $cell($row,45),
                'csdr_needed'                                      => $cell($row,46),
                'csdr_storage'                                     => $cell($row,47),
            ];
        }

        \Log::info('Excel import complete, total rows:', ['count' => count($data)]);

        ECSImport::create([
            'id'         => (string) Str::uuid(),
            'version_id' => $this->versionId,
            'import_data'=> $data,
        ]);
    }
}


/*namespace App\Imports;


use App\Models\ECSImport;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ECSConfigurationImport implements ToCollection,  WithCalculatedFormulas
{
    protected $versionId;

    public function __construct($versionId)
    {
        $this->versionId = $versionId;
    }

    public function collection(Collection $rows)
    {
        $data = [];

        foreach ($rows->skip(3) as $row) {

              // SKIP baris kosong atau tak cukup column
        if (count($row) < 48 || empty($row[0])) {
            continue;
        }

            $data[] = [
                'region' => $row[0],
                'vm_name' => $row[1],
                'ecs_pin' => $row[2],
                'ecs_gpu' => $row[3],
                'ecs_ddh' => $row[4],
                'ecs_vcpu' => $row[5],
                'ecs_vram' => $row[6],
                'ecs_flavour_mapping' => $row[7],
                'storage_system_disk' => $row[8],
                'storage_data_disk' => $row[9],
                'license_operating_system' => $row[10],
                'license_rds_license' => $row[11],
                'license_microsoft_sql' => $row[12],
                'snapshot_copies' => $row[13],
                'additional_capacity' => $row[14],
                'image_copies' => $row[15],
                'csbs_standard_policy' => $row[16],
                'csbs_local_retention_copies' => $row[17],
                'csbs_total_storage' => $row[18],
                'csbs_initial_data_size' => $row[19],
                'csbs_incremental_change' => $row[20],
                'csbs_estimated_incremental_data_change' => $row[21],
                'full_backup_daily' => $row[22],
                'full_backup_weekly' => $row[23],
                'full_backup_monthly' => $row[24],
                'full_backup_yearly' => $row[25],
                'full_backup_total_retention_full_copies' => $row[26],
                'suggestion_estimated_storage_full_backup' => $row[27],
                'estimated_storage_full_backup' => $row[28],
                'incremental_backup_daily' => $row[29],
                'incremental_backup_weekly' => $row[30],
                'incremental_backup_monthly' => $row[31],
                'incremental_backup_yearly' => $row[32],
                'incremental_backup_total_retention_incremental_copies' => $row[33],
                'suggestion_estimated_storage_incremental_backup' => $row[34],
                'estimated_storage_incremental_backup' => $row[35],
                'required' => $row[36],
                'total_replication_copy_retained_second_site' => $row[37],
                'additional_storage' => $row[38],
                'rto' => $row[39],
                'rpo' => $row[40],
                'suggestion_estimated_storage_csbs_replication' => $row[41],
                'estimated_storage_csbs_replication' => $row[42],
                'ecs_dr' => $row[43],
                'dr_activation' => $row[44],
                'seed_vm_required' => $row[45],
                'csdr_needed' => $row[46],
                'csdr_storage' => $row[47],
            ];
        }


        \Log::info('Excel import complete, total rows:', ['count' => count($data)]);


        ECSImport::create([
            'id' => (string) Str::uuid(),
            'version_id' => $this->versionId,
            'import_data' => $data,
            //'export_data' => $data,
        ]);
    }
}*/

