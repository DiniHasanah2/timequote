<?php

namespace App\Imports;

use App\Models\ECSImport;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class ECSConfigurationImport implements ToCollection, WithEvents
{
    protected $versionId;

    public function __construct($versionId)
    {
        $this->versionId = $versionId;
    }

    // Baca "values only" jika reader support; kalau tak, biar default.
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                // Hanya set kalau reader ada delegate (elak call atas Spreadsheet)
                if (isset($event->reader) && method_exists($event->reader, 'getDelegate')) {
                    $delegate = $event->reader->getDelegate();
                    if (method_exists($delegate, 'setReadDataOnly')) {
                        $delegate->setReadDataOnly(true);
                    }
                }
            },
        ];
    }

    public function collection(Collection $rows)
    {
        $data = [];

        // Helper selamat: object (StructuredReference/RichText) -> null/plain text
        $cell = function ($row, $i) {
            if (!isset($row[$i])) return null;
            $v = $row[$i];

            // RichText ke plain text
            if (is_object($v) && $v instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                return trim($v->getPlainText());
            }
            // Apa-apa object lain (contoh StructuredReference) -> null
            if (is_object($v)) return null;

            return is_string($v) ? trim($v) : $v;
        };

        // Skip 3 baris header (row4 = data pertama)
        foreach ($rows->skip(3) as $row) {
            $region = (string) ($row[0] ?? '');
            $vm     = (string) ($row[1] ?? '');
            $vcpu   = (int)    ($row[5] ?? 0);
            $vram   = (int)    ($row[6] ?? 0);

            if ($region === '' && $vm === '' && $vcpu === 0 && $vram === 0) {
                continue;
            }

            $data[] = [
                'region'                                          => $cell($row, 0),
                'vm_name'                                         => $cell($row, 1),
                'ecs_pin'                                         => $cell($row, 2),
                'ecs_gpu'                                         => $cell($row, 3),
                'ecs_ddh'                                         => $cell($row, 4),
                'ecs_vcpu'                                        => $cell($row, 5),
                'ecs_vram'                                        => $cell($row, 6),
                'ecs_flavour_mapping'                             => $cell($row, 7),
                'storage_system_disk'                             => $cell($row, 8),
                'storage_data_disk'                               => $cell($row, 9),
                'license_operating_system'                        => $cell($row,10),
                'license_rds_license'                             => $cell($row,11),
                'license_microsoft_sql'                           => $cell($row,12),
                'snapshot_copies'                                 => $cell($row,13),
                'additional_capacity'                             => $cell($row,14),
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
}*/