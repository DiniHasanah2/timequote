<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Version;
use App\Models\MPDRaaS;
use App\Models\Region;
use Illuminate\Http\Request;

class MPDRaaSController extends Controller
{
    public function create($versionId)
    {
        // Dapatkan version 
        $version = Version::with(['project', 'mpdraas'])->findOrFail($versionId);

         $region = Region::where('version_id', $versionId)->firstOrFail();
        
        return view('projects.mpdraas.create', [
            'project' => $version->project,
            'version' => $version,
            'mpdraas' => $version->mpdraas
        ]);
    }
     
    public function store(Request $request, $versionId)
    {
        // Dapatkan version beserta project
        $version = Version::with('project')->findOrFail($versionId);
        
        $validated = $request->validate([

        'mpdraas_activation_days' => 'nullable|integer|min:0',
        'starter_promotion' => 'nullable|in:Yes,No',
        'mpdraas_location' => 'required|in:None,Kuala Lumpur,Cyberjaya',
        'num_proxy' => 'nullable|integer',
        'vm_name' => 'nullable|string|max:255',
        'always_on'=> 'required|in:Yes,No',
        'pin' => 'required|in:Yes,No',
        'vcpu' => 'nullable|integer|min:0',
        'vram' => 'nullable|integer|min:0',
        'flavour_mapping'=> 'nullable|string|max:255',
        'system_disk'=> 'nullable|integer|min:0',
        'data_disk'=> 'nullable|integer|min:0',
        'operating_system'=> 'required|in:Linux,Microsoft Windows Std,Microsoft Windows DC,Red Hat Enterprise Linux',
        'rds_count' => 'nullable|integer|min:0',
        'm_sql' => 'required|in:None,Web,Standard,Enterprise',
        'used_system_disk'=> 'nullable|integer|min:0',
        'used_data_disk'=> 'nullable|integer|min:0',
        'solution_type'=> 'required|in:None,EVS,OBS',
        'rto_expected'=> 'nullable|integer|min:0',
        'dd_change'=> 'nullable|integer|min:0',
        'data_change'=> 'nullable|numeric|min:0',
        'data_change_size'=> 'nullable|numeric|min:0',
        'replication_frequency'=> 'nullable|integer|min:0',
        'num_replication'=> 'nullable|numeric|min:0',
        'amount_data_change'=> 'nullable|numeric|min:0',
        'replication_bandwidth'=> 'nullable|numeric|min:0',

        'rpo_achieved'=> 'nullable|numeric|min:0',
        'ddos_requirement' =>'required|in:Yes,No',
        'bandwidth_requirement'=> 'nullable|numeric|min:0',

        'main'=> 'nullable|numeric|min:0',
        'used'=> 'nullable|numeric|min:0',
        'delta'=> 'nullable|numeric|min:0',
        'total_replication'=> 'nullable|numeric|min:0',


        

                        
                    


         
        ]);

        // Tambahkan data tambahan
        $validated['project_id'] = $version->project_id;
        $validated['customer_id'] = $version->project->customer_id;
        $validated['presale_id'] = $version->project->presale_id;

        // Simpan data
        MPDRaaS::updateOrCreate(
            ['version_id' => $version->id],
            $validated,
        );

        return redirect()->back()->with('success', 'MPDRaaS data saved successfully!');

    }



private function calculateFlavourMapping(?int $vcpu, ?int $vram): string
{
    $vcpu = $vcpu ?? 0;
    $vram = $vram ?? 0;
    
    $flavours = [
        ['name' => 'm3.micro', 'vcpu' => 1, 'vram' => 1],
        ['name'=> 'm3.small', 'vcpu'=>1, 'vram'=> 2 ],
        ['name' => 'c3.large', 'vcpu' => 2, 'vram' => 4 ],
        ['name'=> 'm3.large', 'vcpu' => 2, 'vram' => 8 ],
        ['name'=> 'r3.large', 'vcpu' => 2, 'vram' => 16 ],
        ['name' => 'c3.xlarge', 'vcpu' => 4, 'vram'=> 8 ],
        ['name'=> 'm3.xlarge', 'vcpu' => 4, 'vram' => 16 ],
        ['name' => 'r3.xlarge', 'vcpu'=> 4, 'vram'=> 32 ],
        
        ['name' => 'c3.2xlarge', 'vcpu'=> 8, 'vram'=> 16 ],
        ['name' => 'm3.2xlarge', 'vcpu'=> 8, 'vram'=> 32 ],
        ['name'=> 'r3.2xlarge', 'vcpu'=> 8, 'vram'=> 64 ],
        ['name'=> 'm3.3xlarge', 'vcpu'=> 12, 'vram'=> 48 ],

        ['name' => 'c3.4xlarge', 'vcpu' => 16, 'vram' => 32],
    ['name' => 'm3.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'r3.4xlarge', 'vcpu' => 16, 'vram' => 128],
    ['name' => 'm3.6xlarge', 'vcpu' => 24, 'vram' => 96],
    ['name' => 'c3.8xlarge', 'vcpu' => 32, 'vram' => 64],
    ['name' => 'm3.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'r3.8xlarge', 'vcpu' => 32, 'vram' => 256],
    ['name' => 'r3.12xlarge', 'vcpu' => 48, 'vram' => 384],
    ['name' => 'c3.16xlarge', 'vcpu' => 64, 'vram' => 128],
    ['name' => 'm3.16xlarge', 'vcpu' => 64, 'vram' => 256],
    ['name' => 'r3.16xlarge', 'vcpu' => 64, 'vram' => 512],
    ['name' => 'c3p.xlarge', 'vcpu' => 4, 'vram' => 8],
    ['name' => 'm3p.xlarge', 'vcpu' => 4, 'vram' => 16],
    ['name' => 'r3p.xlarge', 'vcpu' => 4, 'vram' => 32],
    ['name' => 'c3p.2xlarge', 'vcpu' => 8, 'vram' => 16],
    ['name' => 'm3p.2xlarge', 'vcpu' => 8, 'vram' => 32],
    ['name' => 'r3p.2xlarge', 'vcpu' => 8, 'vram' => 64],
    ['name' => 'm3p.3xlarge', 'vcpu' => 12, 'vram' => 48],
    ['name' => 'c3p.4xlarge', 'vcpu' => 16, 'vram' => 32],
    ['name' => 'm3p.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'r3p.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'm3p.6xlarge', 'vcpu' => 24, 'vram' => 96],
    ['name' => 'c3p.8xlarge', 'vcpu' => 32, 'vram' => 64],
    ['name' => 'm3p.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'r3p.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'm3p.12xlarge', 'vcpu' => 48, 'vram' => 192],
    ['name' => 'r3p.12xlarge', 'vcpu' => 48, 'vram' => 384],
    ['name' => 'm3p.16xlarge', 'vcpu' => 64, 'vram' => 256],
    ['name' => 'r3p.16xlarge', 'vcpu' => 64, 'vram' => 512],
    ['name' => 'r3p.46xlarge.metal', 'vcpu' => 64, 'vram' => 1408],
    ['name' => 'm3gnt4.xlarge', 'vcpu' => 4, 'vram' => 16],
    ['name' => 'm3gnt4.2xlarge', 'vcpu' => 8, 'vram' => 32],
    ['name' => 'm3gnt4.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'm3gnt4.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'm3gnt4.16xlarge', 'vcpu' => 64, 'vram' => 256],
    ['name' => 'r3p.46xlarge.ddh', 'vcpu' => 342, 'vram' => 1480],

 



   
    ];

    $suitable = collect($flavours)
    ->where('vcpu', '>=', $vcpu)
    ->where('vram', '>=', $vram)
    ->sortBy([
        ['vcpu', 'asc'],
        ['vram', 'asc'],
    ])
    ->first();

    return $suitable['name'] ?? 'No suitable flavour';
}


    }

