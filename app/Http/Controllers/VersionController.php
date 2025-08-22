<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Models\Project;
use App\Models\Region;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    // For nested route: GET /projects/{project}/versions
    public function index($projectId)
    {
        $versions = Version::where('project_id', $projectId)
                        ->with('project')
                        ->latest()
                        ->get();
        
        return view('versions.index', compact('versions'));
    }

    // For nested route: GET /projects/{project}/versions/create
    // Modify the create method
    public function create($projectId)
    {
        // Handle project selection case
        if ($projectId === 'select') {
            // Change this line to get all projects the user should see
            $projects = Project::with('customer')->get();
            
            if ($projects->isEmpty()) {
                return redirect()->route('projects.index')
                    ->with('warning', 'No projects available to add versions to.');
            }
            return view("projects.versions.create", [
                'projects' => $projects,
                'projectId' => 'select'
            ]);
        }
    
    // Handle specific project case
    $project = Project::with('customer')->findOrFail($projectId);
    $projects = Project::with('customer')->get();
    
    return view("projects.versions.create", [
        'project' => $project,
        'projects' => $projects,
        'customer' => $project->customer
    ]);
}

    // For nested route: POST /projects/{project}/versions
    public function store(Request $request, $projectId)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'version_name' => 'required|string|max:255'
    ]);
    
    // Use the project_id from the form, not the route parameter
    $project = Project::findOrFail($validated['project_id']);
    
    // Version number logic remains the same
    $latestVersion = $project->versions()->latest()->first();
    $newVersion = '1.0';
    
    if ($latestVersion) {
        $parts = explode('.', $latestVersion->version_number);
        $newMinor = (int)$parts[1] + 1;
        $newVersion = $newMinor >= 10 
            ? (int)$parts[0] + 1 . '.0'
            : $parts[0] . '.' . $newMinor;
    }
    
    $project->versions()->create([
        'version_name' => $validated['version_name'],
        'version_number' => $newVersion
    ]);
    
    return redirect()->route('projects.index')
        ->with('success', 'Version added successfully!');
}

    // For standalone access (if needed)
    public function allVersions()
    {
        $versions = Version::with('project')->latest()->get();
        return view('versions.all', compact('versions'));
    }
    public function destroy($versionId)
{
    $version = Version::findOrFail($versionId);
    $project = $version->project;
    
    $version->delete();
    
    // Check if this was the last version of the project
    if ($project->versions()->count() === 0) {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Version and Project deleted successfully!');
    }
    
    return redirect()->back()->with('success', 'Version deleted successfully!');
}


}
