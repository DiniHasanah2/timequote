<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Version;
use App\Models\SolutionType;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function create($versionId)
{

    $version = Version::with(['project', 'region', 'solution_type'])->findOrFail($versionId);


          // load pricing config
    $pricing = config('pricing');

    $region = Region::firstOrCreate(
        ['version_id' => $version->id],
        [
            'project_id' => $version->project_id,
            'customer_id' => $version->project->customer_id,
            'presale_id' => $version->project->presale_id
        ]
    );

    // Dapatkan balik region lepas create
    $region = Region::where('version_id', $version->id)->first();

    return view('projects.region.professional_services.create', [
        'project' => $version->project,
        'version' => $version,
        'region' => $region, 
        'solution_type' => $version->solution_type,
         'pricing' => $pricing,
    ]);
}



    public function createNetwork($versionId)
{
    $version = Version::with('project.customer')->findOrFail($versionId);
    $region = Region::where('version_id', $versionId)->first();
    $project = $version->project;
       // add pricing config
    $pricing = config('pricing');

    return view('projects.region.network.create', compact('version', 'region', 'project','pricing'));
}

public function createDr($versionId)
{
    $version = Version::with('project.customer')->findOrFail($versionId);
    $region = Region::where('version_id', $versionId)->first();
    $project = $version->project;
    

    return view('projects.region.dr_settings.create', compact('version', 'region', 'project'));
}


public function storeProfessional(Request $request, $versionId)
{
    $version = Version::with('project')->findOrFail($versionId);

    $validated = $request->validate([
        'region' => 'required|in:None,Kuala Lumpur,Cyberjaya',
        'deployment_method' => 'required|in:self-provisioning,professional-services',
        'mandays' => 'nullable|integer',
        'scope_of_work' => 'nullable|string',
        'kl_license_count' => 'nullable|integer',
        'cyber_license_count' => 'nullable|integer',
        'kl_duration' => 'nullable|integer',
        'cyber_duration' => 'nullable|integer',
        
    ]);

    $validated['project_id'] = $version->project_id;
    $validated['customer_id'] = $version->project->customer_id;
    $validated['presale_id'] = $version->project->presale_id;

    Region::updateOrCreate(
        ['version_id' => $version->id],
        $validated
    );

    return redirect()->route('versions.region.create', $versionId)
        ->with('success', 'Professional Services saved!');
}



     

public function storeNetwork(Request $request, $versionId)
{
    $validated = $request->validate([
        'kl_bandwidth' => 'nullable|integer',
        'kl_bandwidth_with_antiddos' => 'nullable|integer',
        'kl_included_elastic_ip' => 'nullable|integer',
        'kl_elastic_ip' => 'nullable|integer',
        'kl_elastic_load_balancer' => 'nullable|integer',
        'kl_direct_connect_virtual' => 'nullable|integer',
        'kl_l2br_instance' => 'nullable|integer',
        'kl_virtual_private_leased_line' => 'nullable|integer',
        'kl_vpll_l2br' => 'nullable|integer',
        'kl_nat_gateway_small' => 'nullable|integer',
        'kl_nat_gateway_medium' => 'nullable|integer',
        'kl_nat_gateway_large' => 'nullable|integer',
        'kl_nat_gateway_xlarge' => 'nullable|integer',
     
        'cyber_bandwidth' => 'nullable|integer',
        'cyber_bandwidth_with_antiddos' => 'nullable|integer',
        'cyber_included_elastic_ip' => 'nullable|integer',
        'cyber_elastic_ip' => 'nullable|integer',
        'cyber_elastic_load_balancer' => 'nullable|integer',
        'cyber_direct_connect_virtual' => 'nullable|integer',
        'cyber_l2br_instance' => 'nullable|integer',
        'cyber_nat_gateway_small' => 'nullable|integer',
        'cyber_nat_gateway_medium' => 'nullable|integer',
        'cyber_nat_gateway_large' => 'nullable|integer',
        'cyber_nat_gateway_xlarge' => 'nullable|integer',
      
        'kl_content_delivery_network' => 'nullable|integer',
        'cyber_content_delivery_network' => 'nullable|integer',
        'kl_scalable_file_service' => 'nullable|integer',
        'cyber_scalable_file_service' => 'nullable|integer',
        'kl_object_storage_service' => 'nullable|integer',
        'cyber_object_storage_service' => 'nullable|integer',
    ]);

    $region = Region::where('version_id', $versionId)->firstOrFail();
    $region->update($validated);

    return redirect()->route('versions.region.network.create', $versionId)->with('success', 'Network settings saved!');
}


public function storeDr(Request $request, $versionId)
{
    $validated = $request->validate([
        'dr_location' => 'required|in:None,Kuala Lumpur,Cyberjaya',
        'dr_bandwidth_type' => 'required|in:bandwidth,anti-ddos',
        'tier1_dr_security' => 'required|in:none,fortigate,opn_sense',
        'tier2_dr_security' => 'required|in:none,fortigate,opn_sense',
        'dr_activation_days' => 'nullable|integer',
        'db_bandwidth' => 'nullable|integer',
        'elastic_ip_dr' => 'nullable|integer',
    ]);

    $region = Region::where('version_id', $versionId)->firstOrFail();

    // Split DR values to correct columns
    if ($validated['dr_location'] === 'None') {
        $region->kl_dr_activation_days = $validated['dr_activation_days'] ?? null;
        $region->kl_db_bandwidth = $validated['db_bandwidth'] ?? null;
        $region->kl_elastic_ip_dr = $validated['elastic_ip_dr'] ?? null;
    }
    else if($validated['dr_location'] === 'Kuala Lumpur') {
        $region->kl_dr_activation_days = $validated['dr_activation_days'] ?? null;
        $region->kl_db_bandwidth = $validated['db_bandwidth'] ?? null;
        $region->kl_elastic_ip_dr = $validated['elastic_ip_dr'] ?? null;
    } else {
        $region->cyber_dr_activation_days = $validated['dr_activation_days'] ?? null;
        $region->cyber_db_bandwidth = $validated['db_bandwidth'] ?? null;
        $region->cyber_elastic_ip_dr = $validated['elastic_ip_dr'] ?? null;
    }

    // Save common DR fields
    $region->dr_location = $validated['dr_location'];
    $region->dr_bandwidth_type = $validated['dr_bandwidth_type'];
    $region->tier1_dr_security = $validated['tier1_dr_security'];
    $region->tier2_dr_security = $validated['tier2_dr_security'];

    $region->save();

    return redirect()->route('versions.region.dr.create', $versionId)
        ->with('success', 'DR settings saved!');
}




    public function store(Request $request, $versionId)
    {
        // Dapatkan version beserta project
        $version = Version::with('project')->findOrFail($versionId);
        
        $validated = $request->validate([
            // Professional Services
            'region' => 'required|in:None,Kuala Lumpur,Cyberjaya',
            'deployment_method' => 'required|in:self-provisioning,professional-services',
            'mandays' => 'nullable|integer|min:0',
            'scope_of_work' => 'nullable|string|min:5000',
            'kl_license_count' => 'nullable|integer|min:0',
            'cyber_license_count' => 'nullable|integer|min:0',
            'kl_duration' => 'nullable|integer|min:0',
            'cyber_duration' => 'nullable|integer|min:0',

            


            'kl_content_delivery_network' => 'nullable|integer|min:0',
            'cyber_content_delivery_network' => 'nullable|integer|min:0',
            'dr_activation_days' => 'nullable|integer|min:0',
            'elastic_ip_dr' => 'nullable|integer|min:0',
            'db_bandwidth' => 'nullable|integer|min:0',
            'kl_content_delivery_network' => 'nullable|integer|min:0',
            'cyber_content_delivery_network' => 'nullable|integer|min:0',
            
            // DR Settings
            'kl_dr_activation_days' => 'nullable|integer|min:0',
            'cyber_dr_activation_days' => 'nullable|integer|min:0',
            'dr_location' => 'required|in:None,Kuala Lumpur,Cyberjaya',
            'kl_db_bandwidth' => 'nullable|integer|min:0',
            'cyber_db_bandwidth' => 'nullable|integer|min:0',
            'dr_bandwidth_type' => 'required|in:bandwidth,anti-ddos',
            'kl_elastic_ip_dr' => 'nullable|integer|min:0',
            'cyber_elastic_ip_dr' => 'nullable|integer|min:0',
            'tier1_dr_security' => 'required|in:none,fortigate,opn_sense',
            'tier2_dr_security' => 'required|in:none,fortigate,opn_sense',
            
            // Network - KL
            'kl_bandwidth' => 'nullable|integer|min:0',
            'kl_bandwidth_with_antiddos' => 'nullable|integer|min:0',
            'kl_included_elastic_ip' => 'nullable|integer|min:0',
            'kl_elastic_ip' => 'nullable|integer|min:0',
            'kl_elastic_load_balancer' => 'nullable|integer|min:0',
            'kl_direct_connect_virtual' => 'nullable|integer|min:0',
            'kl_l2br_instance' => 'nullable|integer|min:0',
            'kl_virtual_private_leased_line' => 'nullable|integer|min:0',
            'kl_vpll_l2br' => 'nullable|integer|min:0',
            'kl_nat_gateway_small' => 'nullable|integer|min:0',
            'kl_nat_gateway_medium' => 'nullable|integer|min:0',
            'kl_nat_gateway_large' => 'nullable|integer|min:0',
            'kl_nat_gateway_xlarge' => 'nullable|integer|min:0',
            
            // Network - Cyber
            'cyber_bandwidth' => 'nullable|integer|min:0',
            'cyber_bandwidth_with_antiddos' => 'nullable|integer|min:0',
            'cyber_included_elastic_ip' => 'nullable|integer|min:0',
            'cyber_elastic_ip' => 'nullable|integer|min:0',
            'cyber_elastic_load_balancer' => 'nullable|integer|min:0',
            'cyber_direct_connect_virtual' => 'nullable|integer|min:0',
            'cyber_l2br_instance' => 'nullable|integer|min:0',
            'cyber_nat_gateway_small' => 'nullable|integer|min:0',
            'cyber_nat_gateway_medium' => 'nullable|integer|min:0',
            'cyber_nat_gateway_large' => 'nullable|integer|min:0',
            'cyber_nat_gateway_xlarge' => 'nullable|integer|min:0',
            
            // Storage
            'kl_scalable_file_service' => 'nullable|integer|min:0',
            'cyber_scalable_file_service' => 'nullable|integer|min:0',
            'kl_object_storage_service' => 'nullable|integer|min:0',
            'cyber_object_storage_service' => 'nullable|integer|min:0',
        ]);

        // Tambahkan data tambahan
        $validated['project_id'] = $version->project_id;
        $validated['customer_id'] = $version->project->customer_id;
        $validated['presale_id'] = $version->project->presale_id;


         $drLocation = $validated['dr_location'];

    // Dapatkan existing region (kalau update)
    $existingRegion = Region::where('version_id', $version->id)->first();

    // Handle content_delivery_network
    
    if ($drLocation === 'Kuala Lumpur') {

        $validated['kl_mandays'] = $validated['mandays'] ?? null;
        $validated['cyber_mandays'] = $existingRegion->cyber_mandays ?? null;

        $validated['kl_content_delivery_network'] = $validated['content_delivery_network'] ?? null;
        $validated['cyber_content_delivery_network'] = $existingRegion->cyber_content_delivery_network ?? null;

        $validated['kl_dr_activation_days'] = $validated['dr_activation_days'] ?? null;
        $validated['cyber_dr_activation_days'] = $existingRegion->cyber_dr_activation_days ?? null;

        $validated['kl_elastic_ip_dr'] = $validated['elastic_ip_dr'] ?? null;
        $validated['cyber_elastic_ip_dr'] = $existingRegion->cyber_elastic_ip_dr ?? null;

        $validated['kl_db_bandwidth'] = $validated['db_bandwidth'] ?? null;
        $validated['cyber_db_bandwidth'] = $existingRegion->cyber_db_bandwidth ?? null;

    } elseif ($drLocation === 'Cyberjaya') {

        $validated['cyber_mandays'] = $validated['mandays'] ?? null;
        $validated['kl_mandays'] = $existingRegion->kl_mandays ?? null;
        
        $validated['cyber_content_delivery_network'] = $validated['content_delivery_network'] ?? null;
        $validated['kl_content_delivery_network'] = $existingRegion->kl_content_delivery_network ?? null;

        $validated['cyber_dr_activation_days'] = $validated['dr_activation_days'] ?? null;
        $validated['kl_dr_activation_days'] = $existingRegion->kl_dr_activation_days ?? null;

        $validated['cyber_elastic_ip_dr'] = $validated['elastic_ip_dr'] ?? null;
        $validated['kl_elastic_ip_dr'] = $existingRegion->kl_elastic_ip_dr ?? null;

        $validated['cyber_db_bandwidth'] = $validated['db_bandwidth'] ?? null;
        $validated['kl_db_bandwidth'] = $existingRegion->kl_db_bandwidth ?? null;
    } 

    
    unset($validated['content_delivery_network']);
    unset($validated['dr_activation_days']);
    unset($validated['elastic_ip_dr']);
    unset($validated['db_bandwidth']);

        // Simpan data
        Region::updateOrCreate(
            ['version_id' => $version->id],
            $validated

        );

        return redirect()->back()->with('success', 'Region data saved successfully!');
    }

public function autoSave(Request $request, $versionId)
{
    \Log::info('🔁 AutoSave Hit!', [
        'version_id' => $versionId,
        'payload' => $request->all()
    ]);

    $allowed = [
        'region',
        'deployment_method',
        'mandays',
        'scope_of_work',
        'kl_content_delivery_network',
        'cyber_content_delivery_network',
        'kl_license_count',
        'cyber_license_count',
        'kl_duration',
        'cyber_duration',
        'kl_dr_activation_days',
        'cyber_dr_activation_days',
        'dr_location',
        'kl_db_bandwidth',
        'cyber_db_bandwidth',
        'dr_bandwidth_type',
        'kl_elastic_ip_dr',
        'cyber_elastic_ip_dr',
        'tier1_dr_security',
        'tier2_dr_security',
        'kl_bandwidth',
        'kl_bandwidth_with_antiddos',
        'kl_included_elastic_ip',
        'kl_elastic_ip',
        'kl_elastic_load_balancer',
        'kl_direct_connect_virtual',
        'kl_l2br_instance',
        'kl_virtual_private_leased_line',
        'kl_vpll_l2br',
        'kl_nat_gateway_small',
        'kl_nat_gateway_medium',
        'kl_nat_gateway_large',
        'kl_nat_gateway_xlarge',
        'cyber_bandwidth',
        'cyber_bandwidth_with_antiddos',
        'cyber_included_elastic_ip',
        'cyber_elastic_ip',
        'cyber_elastic_load_balancer',
        'cyber_direct_connect_virtual',
        'cyber_l2br_instance',
        'cyber_nat_gateway_small',
        'cyber_nat_gateway_medium',
        'cyber_nat_gateway_large',
        'cyber_nat_gateway_xlarge',
        'kl_scalable_file_service',
        'cyber_scalable_file_service',
        'kl_object_storage_service',
        'cyber_object_storage_service',
    ];


    \Log::debug('🟩 Allowed keys:', $allowed);
    \Log::debug('🟥 Dihantar field:', array_keys($request->all()));

    $version = Version::with('project')->findOrFail($versionId);
    $region = Region::firstOrNew(['version_id' => $versionId]);

    foreach ($request->all() as $key => $value) {
        if (in_array($key, $allowed)) {
            $region->$key = $value;
        } else {
            \Log::warning("⛔️ AutoSave ignored unallowed field: $key = $value");
        }
    }

    $region->project_id = $region->project_id ?? $version->project_id;
    $region->customer_id = $region->customer_id ?? $version->project->customer_id;
    $region->presale_id = $region->presale_id ?? $version->project->presale_id;

    $region->save();

    \Log::info('✅ Region auto-saved!', [
        'id' => $region->id,
        'updated_fields' => $request->all()
    ]);

    return response()->json(['status' => 'ok']);
}

    
/*public function autoSave(Request $request, $versionId)
{
    $allowed = [
        'region',
        'deployment_method',
        'mandays',
        'scope_of_work',
        'kl_content_delivery_network',
        'cyber_content_delivery_network',
         'kl_license_count',
        'cyber_license_count',
        'kl_duration',
        'cyber_duration',
        
        // DR Settings
        'kl_dr_activation_days',
        'cyber_dr_activation_days',
        'dr_location',
        'kl_db_bandwidth',
         'cyber_db_bandwidth',
        'dr_bandwidth_type',
        'kl_elastic_ip_dr',
          'cyber_elastic_ip_dr',
        'tier1_dr_security',
        'tier2_dr_security',
        
        // Network - KL
        'kl_bandwidth',
        'kl_bandwidth_with_antiddos',
        'kl_included_elastic_ip',
        'kl_elastic_ip',
        'kl_elastic_load_balancer',
        'kl_direct_connect_virtual',
        'kl_l2br_instance',
        'kl_virtual_private_leased_line',
        'kl_vpll_l2br',
        'kl_nat_gateway_small',
        'kl_nat_gateway_medium',
        'kl_nat_gateway_large',
        'kl_nat_gateway_xlarge',
        
        // Network - Cyber
        'cyber_bandwidth',
        'cyber_bandwidth_with_antiddos',
        'cyber_included_elastic_ip',
        'cyber_elastic_ip',
        'cyber_elastic_load_balancer',
        'cyber_direct_connect_virtual',
        'cyber_l2br_instance',
        'cyber_nat_gateway_small',
        'cyber_nat_gateway_medium',
        'cyber_nat_gateway_large',
        'cyber_nat_gateway_xlarge',
        
        // Storage
        'kl_scalable_file_service',
        'cyber_scalable_file_service',
        'kl_object_storage_service',
        'cyber_object_storage_service',
    ];

    $version = Version::with('project')->findOrFail($versionId);
    $region = Region::firstOrNew(['version_id' => $versionId]);

    foreach ($request->all() as $key => $value) {
        if (in_array($key, $allowed)) {
            $region->$key = $value;
        }
    }

    $region->project_id = $region->project_id ?? $version->project_id;
    $region->customer_id = $region->customer_id ?? $version->project->customer_id;
    $region->presale_id = $region->presale_id ?? $version->project->presale_id;

    $region->save();

    return response()->json(['status' => 'ok']);
}*/

    public function serviceDescription(Project $project)
    {
        return view('projects.region.service_description', [
            'project' => $project
        ]);
    }
}