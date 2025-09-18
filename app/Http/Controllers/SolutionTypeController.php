<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Version;
use App\Models\SolutionType;
use Illuminate\Http\Request;
use App\Models\InternalSummary;

class SolutionTypeController extends Controller
{
    public function create($versionId)
    {
        // Dapatkan version 
        $version = Version::with(['project', 'solution_type'])->findOrFail($versionId);
         $summary  = InternalSummary::where('version_id', $versionId)->first();
$isLocked = (bool) optional($summary)->is_logged;
$lockedAt = optional($summary)->logged_at;
        
        return view('projects.solution_type.create', [
            'project' => $version->project,
            'version' => $version,
            'solution_type' => $version->solution_type,
               'isLocked'         => $isLocked,
    'lockedAt'         => $lockedAt,
        ]);
    }


public function store(Request $request, $versionId)
{
    $version = Version::with('project')->findOrFail($versionId);
       // lock guard
    $summary = InternalSummary::where('version_id', $versionId)->first();
    if (optional($summary)->is_logged) {
        return back()->with('error', '🔒 This version is locked in Internal Summary. Editing is disabled. Please unlock there if you need to make changes.');
    }

    /*$validated = $request->validate([
        'solution_type' => 'required|in:TCS Only,MP-DRaaS Only,Both',
        'production_region' => 'required|in:None,Kuala Lumpur,Cyberjaya',
        'mpdraas_region' => 'required|in:None,Kuala Lumpur,Cyberjaya',
        'dr_region' => 'required|in:None,Kuala Lumpur,Cyberjaya',
          
    ]);*/
     $rules = [
        'solution_type' => 'required|in:TCS Only,MP-DRaaS Only,Both',
    ];

    // condition-based validation
    if ($request->solution_type === 'TCS Only') {
        $rules['production_region'] = 'required|in:None,Kuala Lumpur,Cyberjaya';
        $rules['dr_region'] = 'required|in:None,Kuala Lumpur,Cyberjaya';
    } elseif ($request->solution_type === 'MP-DRaaS Only') {
        $rules['mpdraas_region'] = 'required|in:None,Kuala Lumpur,Cyberjaya';
    } elseif ($request->solution_type === 'Both') {
        $rules['production_region'] = 'required|in:None,Kuala Lumpur,Cyberjaya';
        $rules['dr_region'] = 'required|in:None,Kuala Lumpur,Cyberjaya';
        $rules['mpdraas_region'] = 'required|in:None,Kuala Lumpur,Cyberjaya';
    }

    $validated = $request->validate($rules);

    $validated['project_id'] = $version->project_id;
    $validated['customer_id'] = $version->project->customer_id;
    $validated['presale_id'] = $version->project->presale_id;

    SolutionType::updateOrCreate(
        ['version_id' => $version->id],
        $validated
    );

    return redirect()->route('versions.solution_type.create', $versionId)
        ->with('success', 'Solution Type saved!');
}


 

}