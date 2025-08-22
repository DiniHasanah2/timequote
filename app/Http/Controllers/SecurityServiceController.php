<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Version;
use App\Models\SecurityService;
use App\Models\Region;
use Illuminate\Http\Request;

class SecurityServiceController extends Controller
{
    public function create($versionId)
    {
        // Dapatkan version 
        $version = Version::with(['project', 'security_service'])->findOrFail($versionId);

          // load pricing config
    $pricing = config('pricing');
        
        return view('projects.security_service.create', [
            'project' => $version->project,
            'version' => $version,
            'security_service' => $version->security_service,
            'pricing' => $pricing,
        ]);
    }
     
    public function store(Request $request, $versionId)
    {
        // Dapatkan version beserta project
        $version = Version::with('project')->findOrFail($versionId);
        
        $validated = $request->validate([

        //Managed Services
        'kl_managed_services_1' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
        'kl_managed_services_2' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
        'kl_managed_services_3' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
        'kl_managed_services_4' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
        'cyber_managed_services_1' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
        'cyber_managed_services_2' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
        'cyber_managed_services_3' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
        'cyber_managed_services_4' => 'required|in:None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR',
                  
        // Monitoring
        'kl_security_advanced' => 'nullable|integer|min:1|max:128',
        'cyber_security_advanced' => 'nullable|integer|min:1|max:128',
        'kl_insight_vmonitoring' => 'required|in:Yes,No',
        'cyber_insight_vmonitoring' => 'required|in:Yes,No',

        //Security Service
        'kl_cloud_vulnerability' => 'nullable|integer|min:1|max:128',
        'cyber_cloud_vulnerability' => 'nullable|integer|min:1|max:128',

        //Cloud Security
        'kl_firewall_fortigate' => 'nullable|integer|min:1|max:128',
        'cyber_firewall_fortigate' => 'nullable|integer|min:1|max:128',
        'kl_firewall_opnsense' => 'nullable|integer|min:1|max:128',
        'cyber_firewall_opnsense' => 'nullable|integer|min:1|max:128',
        'kl_shared_waf' => 'nullable|integer|min:1|max:128',
        'cyber_shared_waf' => 'nullable|integer|min:1|max:128',
        'kl_antivirus' => 'nullable|integer|min:1|max:128',
        'cyber_antivirus' => 'nullable|integer|min:1|max:128',

        //Other Services
        'kl_gslb' => 'nullable|integer|min:1|max:128',
        'cyber_gslb' => 'nullable|integer|min:1|max:128',
         
         
        ]);

        // Tambahkan data tambahan
        $validated['project_id'] = $version->project_id;
        $validated['customer_id'] = $version->project->customer_id;
        $validated['presale_id'] = $version->project->presale_id;

        // Simpan data
        SecurityService::updateOrCreate(
            ['version_id' => $version->id],
            $validated
        );

        return redirect()->back()->with('success', 'Security service data saved successfully!');
    }

}