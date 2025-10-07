<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Version;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;


class ProjectController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    if (!$user) abort(403, 'Unauthorized');

    // ==== filters from query ====
    $selectedCustomerId = $request->query('customer_id');
    $projectKeyword     = trim($request->query('project', ''));
    $status             = $request->query('status'); // 'pending' | 'committed' | null

    // ==== base query (start with Project::query()) ====
    $projectsQuery = Project::query();

    // ---- scope by role (presale sees own + assigned) ----
    if ($user->role === 'presale') {
        $projectsQuery->where(function ($q) use ($user) {
            $q->where('presale_id', $user->id)
              ->orWhereHas('assigned_presales', function ($sub) use ($user) {
                  $sub->where('users.id', $user->id);
              });
        });
    }

    // ---- filter projects by customer / name ----
    if (!empty($selectedCustomerId)) {
        $projectsQuery->where('customer_id', $selectedCustomerId);
    }
    if ($projectKeyword !== '') {
        $projectsQuery->where('name', 'LIKE', '%'.$projectKeyword.'%');
    }

    // ---- filter projects by STATUS via versions (so only projects that have matching versions appear) ----
    if ($status === 'committed') {
        // project must have at least one version with internal_summary.is_logged = 1
        $projectsQuery->whereHas('versions', function ($vq) {
            $vq->whereHas('internal_summary', function ($iq) {
                $iq->where('is_logged', 1);
            });
        });
    } elseif ($status === 'pending') {
        // project must have a version that is not committed (no summary OR is_logged = 0/null)
        $projectsQuery->whereHas('versions', function ($vq) {
            $vq->where(function ($sub) {
                $sub->whereDoesntHave('internal_summary')
                    ->orWhereHas('internal_summary', function ($iq) {
                        $iq->whereNull('is_logged')->orWhere('is_logged', 0);
                    });
            });
        });
    }

    // ---- eager load relations & constrain VERSIONS to the selected status ----
    $projectsQuery->with([
        'customer',
        'assigned_presales',
        'versions' => function ($q) use ($status) {
            $q->with(['internal_summary', 'quotations.products', 'latestQuotation'])
              ->orderBy('created_at', 'desc');

            if ($status === 'committed') {
                $q->whereHas('internal_summary', function ($iq) {
                    $iq->where('is_logged', 1);
                });
            } elseif ($status === 'pending') {
                $q->where(function ($sub) {
                    $sub->whereDoesntHave('internal_summary')
                        ->orWhereHas('internal_summary', function ($iq) {
                            $iq->whereNull('is_logged')->orWhere('is_logged', 0);
                        });
                });
            }
        },
    ]);

    $projects = $projectsQuery->orderBy('created_at', 'asc')->get();

    // dropdown data
    $customers = Customer::orderBy('name')->get();
    $presales  = User::where('role', 'presale')->get();

    return view('projects.index', compact(
        'projects', 'customers', 'presales',
        'selectedCustomerId', 'projectKeyword', 'status' // <-- pass status to view
    ));
}

  
  /*  public function index(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        abort(403, 'Unauthorized');
    }

    // Filters dari query
    $selectedCustomerId = $request->query('customer_id');
    $projectKeyword     = trim($request->query('project', ''));

    // Base query untuk senarai projek + relations
    $projectsQuery = Project::with([
        'customer',
        'versions.internal_summary',
        'versions.quotations.products',
        'versions.latestQuotation',
        'assigned_presales'
    ]);


    // Scope projek ikut role
if ($user->role === 'presale') {
    // presale: hanya projek di bawah dia / assigned
    $projectsQuery->where(function ($q) use ($user) {
        $q->where('presale_id', $user->id)
          ->orWhereHas('assigned_presales', function ($sub) use ($user) {
              $sub->where('users.id', $user->id);
          });
    });
}



    // Filter by customer (optional)
    if (!empty($selectedCustomerId)) {
        $projectsQuery->where('customer_id', $selectedCustomerId);
    }

    // Filter by project name (optional)
    if ($projectKeyword !== '') {
        $projectsQuery->where('name', 'LIKE', '%'.$projectKeyword.'%');
    }

    $projects = $projectsQuery->orderBy('created_at', 'asc')->get();

   

    // Everyone (admin, product, presale) sees ALL customers in the dropdown
    $customers = Customer::orderBy('name')->get();



    $presales = User::where('role', 'presale')->get();

    return view('projects.index', compact(
        'projects', 'customers', 'presales', 'selectedCustomerId', 'projectKeyword'
    ));
}*/



    public function store(Request $request)
    {
        $user = Auth::user();
        
        /*$rules = [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'version_name' => 'required|string|max:255',
            //'version_number' => 'required|string'
        ];

        if ($user->role === 'admin') {
            $rules['presale_id'] = 'required|exists:users,id';
        }*/
        $rules = [
    'customer_id'   => 'required|exists:customers,id',
    'name'          => 'required|string|max:255',
    'version_name'  => 'required|string|max:255',
];

// admin & product: wajib pilih presale
if (in_array($user->role, ['admin', 'product'])) {
    $rules['presale_id'] = 'required|exists:users,id';
}


        $validated = $request->validate($rules);
        $customer = Customer::findOrFail($validated['customer_id']);

        //That means if Presale2 is creating the project, the project’s presale_id will be Presale2.
        $presaleId = in_array($user->role, ['admin', 'product'])
    ? $validated['presale_id']
    : $user->id; 


            $customerId = $validated['customer_id'];
//$projectName = strtolower(trim($validated['name']));
$projectName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validated['name']));
$versionName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validated['version_name']));



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
        'quotation_value' => 0.00,
        'status'          => 'pending',
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

 $project->delete();
    
    return redirect()->route('projects.index')
        ->with('success', 'Project deleted!');
}



public function markCommitted($projectId)
{
    $project = Project::with('assigned_presales')->findOrFail($projectId);
    $user = Auth::user();

    if (in_array($user->role, ['presale','product']) &&
        $project->presale_id !== $user->id &&
        !$project->assigned_presales->contains('id', $user->id)) {
        abort(403, 'Unauthorized');
    }

    $project->update(['status' => 'committed']);
    return redirect()->route('projects.index')->with('success', 'Project marked as committed.');
}

public function markPending($projectId)
{
    $project = Project::with('assigned_presales')->findOrFail($projectId);
    $user = Auth::user();

    if (in_array($user->role, ['presale','product']) &&
        $project->presale_id !== $user->id &&
        !$project->assigned_presales->contains('id', $user->id)) {
        abort(403, 'Unauthorized');
    }

    $project->update(['status' => 'pending']);
    return redirect()->route('projects.index')->with('success', 'Project reverted to pending.');
}


public function duplicateVersion($versionId)
{
    $user = Auth::user();

    // Prevent heavy load relations (cycle less cycle)
    $version = Version::findOrFail($versionId);
    $project = Project::findOrFail($version->project_id);

    // Permission check
    if (in_array($user->role, ['presale', 'product']) &&
        $project->presale_id !== $user->id &&
        !$project->assigned_presales()->where('users.id', $user->id)->exists()) {
        abort(403, 'Unauthorized');
    }

    $newVersion = null;

    DB::transaction(function () use ($project, $version, &$newVersion) {
        // 1) next version number (ikut pattern +0.1)
        $latest = $project->versions()->max('version_number');
        $next   = $latest ? (float)$latest + 0.1 : 1.0;
        $next   = round($next, 1);

        // 2) Clone version (tanpa relations) dan SAVE (bukan push)
        $newVersion = $version->replicate();
        $newVersion->version_number = $next;
        $newVersion->version_name   = $version->version_name . ' (Copy)';

        // PENTING: kosongkan relations supaya save() tak cuba cascade
        $newVersion->setRelations([]);
        $newVersion->save();

        // Helper untuk clone rows by project_id + version_id
        $cloneRows = function ($modelClass) use ($project, $version, $newVersion) {
            if (!class_exists($modelClass)) return;

            $modelClass::where('project_id', $project->id)
                ->where('version_id', $version->id)
                ->get()
                ->each(function ($row) use ($project, $newVersion) {
                    $clone = $row->replicate();
                    $clone->project_id = $project->id;
                    $clone->version_id = $newVersion->id;
                    // Pastikan tiada relations tersangkut
                    if (method_exists($clone, 'setRelations')) {
                        $clone->setRelations([]);
                    }
                    $clone->save();
                });
        };

        // 3) Clone modules berkaitan
        $cloneRows(\App\Models\ECSConfiguration::class);
        $cloneRows(\App\Models\Region::class);
        $cloneRows(\App\Models\SecurityService::class);
        $cloneRows(\App\Models\MPDRaaS::class);
        $cloneRows(\App\Models\NonStandardItem::class);

        // TIP: Jangan clone InternalSummary; ia computed
        // $cloneRows(\App\Models\InternalSummary::class);

        // 4) Clone quotation + items (jika ada)
        $oldQuote = $version->quotations()->first();
        if ($oldQuote) {
            $newQuote = $oldQuote->replicate();
            $newQuote->project_id = $project->id;
            $newQuote->version_id = $newVersion->id;
            $newQuote->status     = 'pending';
            $newQuote->quote_code = 'Q-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
            $newQuote->save();

            if (method_exists($oldQuote, 'products')) {
                foreach ($oldQuote->products as $item) {
                    $clone = $item->replicate();
                    if (isset($clone->quotation_id)) {
                        $clone->quotation_id = $newQuote->id;
                    }
                    if (method_exists($clone, 'setRelations')) {
                        $clone->setRelations([]);
                    }
                    $clone->save();
                }
            }
        }
    });

    return redirect()
        ->route('versions.solution_type.create', $newVersion->id)
        ->with('success', "Duplicated to {$newVersion->version_name} (v{$newVersion->version_number}).");
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