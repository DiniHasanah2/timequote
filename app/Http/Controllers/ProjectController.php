<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Version;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
    
        if (!$user) {
            abort(403, 'Unauthorized');
        }
    
        // change query projects based role user (ONLY SEE OWNS PROJECT)
       
        
        /*if ($user->role === 'presale') {
            $projectsQuery->where('presale_id', $user->id);//view personal project
        }*/




        //PRESALE SEE ALL PROJECTS:  $projectsQuery = Project::with(['customer', 'versions']);

        //presale hanya nampak project dia cipta (presale_id) atau yang admin assign kat dia (project_presale table)
        
        //$projectsQuery = Project::with(['customer', 'versions', 'assigned_presales']);
        $projectsQuery = Project::with([
    'customer',
    'versions.quotations.products',
    'assigned_presales'
]);



//if ($user->role === 'presale') 


if (in_array($user->role, ['presale', 'product'])) {


    $projectsQuery->where(function ($q) use ($user) {
        $q->where('presale_id', $user->id)
          ->orWhereHas('assigned_presales', function ($sub) use ($user) {
              $sub->where('users.id', $user->id);
          });
    });
}

        
        //$projects = $projectsQuery->get();
        $projects = $projectsQuery->orderBy('created_at', 'asc')->get();

        
        //$customers = Customer::all();
        $customers = Customer::orderBy('name')->get();
      



        /*ORIGINAL ONE
        $customers = Customer::whereHas('presale', function ($query) use ($user) {
    $query->where('department', $user->department);
})->get();*/

        
        //$customers = Customer::where('presale_id', $user->id)->get(); //show customer that only owns by the current presale
        $presales = User::where('role', 'presale')->get();
        
        return view('projects.index', compact('projects', 'customers', 'presales'));
        
  
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'version_name' => 'required|string|max:255',
            //'version_number' => 'required|string'
        ];

        if ($user->role === 'admin') {
            $rules['presale_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);
        $customer = Customer::findOrFail($validated['customer_id']);

        $presaleId = $user->role === 'admin' 
            ? $validated['presale_id'] 
            : $user->id;

            $customerId = $validated['customer_id'];
//$projectName = strtolower(trim($validated['name']));
$projectName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validated['name']));
$versionName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validated['version_name']));

//$versionName = strtolower(trim($validated['version_name']));

// Cari projek yang sama untuk customer ini
/*$existingProject = Project::where('customer_id', $customerId)
    ->whereRaw('LOWER(name) = ?', [$projectName])
    ->whereHas('versions', function ($query) use ($versionName) {
        $query->whereRaw('LOWER(version_name) = ?', [$versionName]);
    })
    ->first();*/


   $existingProjects = Project::where('customer_id', $customerId)
    ->with('versions')
    ->get();

foreach ($existingProjects as $project) {
    $projectNameClean = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($project->name));
    if ($projectNameClean === $projectName) {
        foreach ($project->versions as $version) {
            $versionNameClean = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($version->version_name));
            if ($versionNameClean === $versionName) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This project and version name already exists for this customer.');
            }
        }
    }
}





      // Create project
    $project = Project::create([
        'customer_id' => $validated['customer_id'],
        'presale_id' => $presaleId,
        'name' => $validated['name'],
        'quotation_value' => 0.00
    ]);

    // Auto-assign presale creator to assigned_presales
$project->assigned_presales()->syncWithoutDetaching([$presaleId]); 

 // With (auto-increment version):
$latestVersion = $project->versions()->max('version_number');
$nextVersion = $latestVersion ? $latestVersion + 0.1 : 1.0; // e.g., 1.0 → 1.1 → 1.2

$project->versions()->create([
    'version_name' => $validated['version_name'],
    'version_number' => $nextVersion
]);

return redirect()->route('projects.index')
    ->with('success', 'Project created!');
}
public function edit($id)
{
    //$project = Project::with('versions')->findOrFail($id);


    $project = Project::with('versions', 'assigned_presales')->findOrFail($id);

    $user = Auth::user();

if (in_array($user->role, ['presale', 'product']) && 
    $project->presale_id !== $user->id && 
    !$project->assigned_presales->contains('id', $user->id)) {
    abort(403, 'Unauthorized');
}


// Protect unauthorized presale
/*if ($user->role === 'presale' && 
    $project->presale_id !== $user->id && 
    !$project->assigned_presales->contains('id', $user->id)) {
    abort(403, 'Unauthorized');
}*/



    return view('projects.edit', compact('project'));
}

public function update(Request $request, $id)
{
    //$project = Project::findOrFail($id);

    $project = Project::with('assigned_presales')->findOrFail($id);

    $user = Auth::user();

if (in_array($user->role, ['presale', 'product']) && 
    $project->presale_id !== $user->id && 
    !$project->assigned_presales->contains('id', $user->id)) {
    abort(403, 'Unauthorized');
}


/*if ($user->role === 'presale' && 
    $project->presale_id !== $user->id && 
    !$project->assigned_presales->contains('id', $user->id)) {
    abort(403, 'Unauthorized');
}*/

    
    $project->update([
        'name' => $request->name
    ]);
    
    // Update first version if exists
    if($project->versions->first()) {
        $project->versions()->first()->update([
            'version_name' => $request->version_name
        ]);
    }
    
    return redirect()->route('projects.index')
        ->with('success', 'Project updated!');
}

public function nextVersion(Project $project)
{
    $latestVersion = $project->versions()->max('version_number');
    $nextVersion = $latestVersion ? $latestVersion + 1 : 1;
    return response()->json(['nextVersion' => $nextVersion]);
}

public function destroy($id)
{
    //$project = Project::findOrFail($id);
   

    $project = Project::with('assigned_presales')->findOrFail($id);

    $user = Auth::user();

if (in_array($user->role, ['presale', 'product']) && 
    $project->presale_id !== $user->id && 
    !$project->assigned_presales->contains('id', $user->id)) {
    abort(403, 'Unauthorized');
}


/*if ($user->role === 'presale' && 
    $project->presale_id !== $user->id && 
    !$project->assigned_presales->contains('id', $user->id)) {
    abort(403, 'Unauthorized');
}*/

 $project->delete();
    
    return redirect()->route('projects.index')
        ->with('success', 'Project deleted!');
}





public function internalSummary($projectId, $versionId)
{
    // Fetch ECS Configuration untuk project+version
    $ecsData = \App\Models\ECSConfiguration::where('project_id', $projectId)
                ->where('version_id', $versionId)
                ->get();

    // Fetch Region service data
    $regionData = \App\Models\Region::where('project_id', $projectId)
                  ->where('version_id', $versionId)
                  ->get();

    // Fetch Security service data
    $securityData = \App\Models\SecurityService::where('project_id', $projectId)
                      ->where('version_id', $versionId)
                      ->get();

    // Hantar ke view
    return view('projects.security_service.internal_summary', compact(
        'ecsData', 'regionData', 'securityData'
    ));
}


public function assignPresalesForm($projectId)
{
    $project = Project::with('assigned_presales')->findOrFail($projectId);
    $presales = User::whereIn('role', ['presale', 'product'])->get();

    //$presales = User::where('role', 'presale')->get();

    return view('projects.assign_presales', compact('project', 'presales'));
}

public function assignPresales(Request $request, $projectId)
{
    $project = Project::findOrFail($projectId);
    $presaleIds = $request->input('presale_ids', []);

    $project->assigned_presales()->sync($presaleIds); // attach/detach in one go

    return redirect()->route('projects.index')->with('success', 'Presales updated');
}


}