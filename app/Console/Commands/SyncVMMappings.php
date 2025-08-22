<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ECSConfiguration;
use App\Models\ECSFlavour; 
use App\Models\VMMappings;
use Illuminate\Support\Str;

class SyncVMMappings extends Command
{
    protected $signature = 'vm:sync';
    protected $description = 'Sync ECSConfiguration data to VM Mapping table';

    public function handle()
    {
        $ecsList = ECSConfiguration::with('version.project.customer', 'version.quotations')->get();
        

        foreach ($ecsList as $ecs) {
            $flavour = ECSFlavour::where('flavour_name', $ecs->ecs_flavour_mapping)->first();
$ecs_code = $flavour ? $flavour->ecs_code : null;
          /*VMMappings::updateOrCreate(
                     ['id' => Str::uuid()],
    [
        'vm_name' => $ecs->vm_name,
        'quotation_id' => optional($ecs->version->quotations->first())->id,
        'customer_name' => optional($ecs->version->project->customer)->name,
        'project_id' => optional($ecs->version->project)->id,
        'ecs_flavour_mapping' => $ecs->ecs_flavour_mapping,
        'ecs_code' => $ecs_code,
    ],
);*/
VMMappings::updateOrCreate(
    [
        'vm_name' => $ecs->vm_name,
        'quotation_id' => optional($ecs->version->quotations->first())->id,
        'project_id' => optional($ecs->version->project)->id,
    ],
    [
        'id' => (string) Str::uuid(), 
        'customer_name' => optional($ecs->version->project->customer)->name,
        'ecs_flavour_mapping' => $ecs->ecs_flavour_mapping,
        'ecs_code' => $ecs_code,
    ],
);


          
        }

        $this->info('VM Mapping successfully synced.');
    }
}
