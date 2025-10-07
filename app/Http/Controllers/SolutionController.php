<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solution;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Project;
use App\Models\Customer;

class SolutionController extends Controller
{
    /*public function index(Request $request)
    {
        // --- Filters from query string ---
        $customerId = $request->query('customer_id');
        $projectId  = $request->query('project_id');
        $status     = $request->query('status'); 

        // Hanya benarkan status ini (case-insensitive handling)
        $allowedStatuses = ['pending','committed','Pending','Committed','PENDING','COMMITTED'];

        // === Existing Solutions (yang sudah dibuat) ===
        $existingSolutionsQuery = Solution::with(['quotation', 'version', 'customer', 'project'])
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($projectId,  fn($q) => $q->where('project_id',  $projectId))
            ->whereIn('status', $allowedStatuses);

        if ($status) {
            $existingSolutionsQuery->whereIn('status', [$status, ucfirst($status), strtoupper($status)]);
        }

        $existingSolutions = $existingSolutionsQuery->get();

        // IDs quotation yang sudah ada solution
        $existingQuotationIds = $existingSolutions->pluck('quotation_id')->toArray();

        // === Quotations yang belum ada Solution (auto rows) ===
        $quotationsQuery = Quotation::with(['version', 'project.customer'])
            ->whereNotIn('id', $existingQuotationIds)
            ->when($customerId, fn($q) => $q->whereHas('project', fn($qq) => $qq->where('customer_id', $customerId)))
            ->when($projectId,  fn($q) => $q->where('project_id', $projectId))
            ->whereIn('status', $allowedStatuses);

        if ($status) {
            $quotationsQuery->whereIn('status', [$status, ucfirst($status), strtoupper($status)]);
        }

        $quotations = $quotationsQuery->get();

        // Auto solutions dari quotations yang belum dibuat Solution
        $autoSolutions = $quotations->map(function ($q) {
            return (object)[
                'version_name'  => optional($q->version)->version_name ?? '-',
                'customer_name' => optional($q->project->customer)->name ?? '-',
                'project_name'  => optional($q->project)->name ?? '-',
                'status'        => $q->status ?? '-',
                'quotation_id'  => $q->id,
                'version_id'    => $q->version_id,
                'is_auto'       => true,
            ];
        });

        // Gabung existing + auto
        $allSolutions = $existingSolutions->map(function ($s) {
            return (object)[
                'version_name'  => $s->version_name ?? optional($s->version)->version_name ?? '-',
                'customer_name' => $s->customer_name ?? optional($s->customer)->name ?? '-',
                'project_name'  => $s->project_name ?? optional($s->project)->name ?? '-',
                'status'        => $s->status,
                'quotation_id'  => $s->quotation_id,
                'version_id'    => $s->version_id,
                'is_auto'       => false,
            ];
        })->concat($autoSolutions);

        // Dropdown sources
        
        $user   = auth()->user();
$userId = $user->id;

if ($user->role === 'admin') {
    // Admin nampak semua
    $customers = Customer::orderBy('name')->get(['id','name']);
    $projects  = Project::orderBy('name')->get(['id','name']);
} else {
    // Presale: hanya customer/projek yang dia assigned
    $customers = Customer::whereHas('projects', function ($q) use ($userId) {
        $q->where(function ($qq) use ($userId) {
            // support dua jenis assignment: legacy presale_id atau pivot project_presale
            $qq->where('presale_id', $userId)
               ->orWhereHas('assigned_presales', function ($q2) use ($userId) {
                   $q2->where('users.id', $userId);
               });
        });
    })
    ->orderBy('name')
    ->get(['id','name']);

    $projects = Project::where(function ($q) use ($userId) {
        $q->where('presale_id', $userId)
          ->orWhereHas('assigned_presales', function ($q2) use ($userId) {
              $q2->where('users.id', $userId);
          });
    })
    ->orderBy('name')
    ->get(['id','name']);
}


      
        $existingIdsAll = Solution::pluck('quotation_id')->toArray();
        

        $availableQuotationsQuery = Quotation::with(['version','project.customer'])
    ->whereNotIn('id', $existingIdsAll)
    ->whereIn('status', $allowedStatuses);

if ($user->role !== 'admin') {
    $availableQuotationsQuery->where(function ($q) use ($userId) {
        $q->where('presale_id', $userId)
          ->orWhereHas('project.assigned_presales', function ($q2) use ($userId) {
              $q2->where('users.id', $userId);
          });
    });
}

$availableQuotations = $availableQuotationsQuery->orderByDesc('created_at')->get();


        return view('solutions.index', [
            'solutions'           => $allSolutions,
            'availableQuotations' => $availableQuotations,
            'presales'            => User::where('role', 'presale')->get(),
            'customers'           => $customers,
            'projects'            => $projects,
            'filters'             => [
                'customer_id' => $customerId,
                'project_id'  => $projectId,
                'status'      => $status,
            ],
        ]);
    }*/

    public function index(Request $request)
{
    $customerId = $request->query('customer_id');
    $projectId  = $request->query('project_id');
    $statusRaw  = $request->query('status');          // 'pending' | 'committed' | null
    $status     = $statusRaw ? strtolower($statusRaw) : null;

    // ========= EXISTING SOLUTIONS (yang dah create) =========
    $existingSolutionsQuery = \App\Models\Solution::with([
            'quotation',
            'customer',
            'project',
       
            'version.internal_summary',
        ])
        ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
        ->when($projectId,  fn($q) => $q->where('project_id',  $projectId));

    // tapis status berdasarkan internal_summaries.is_logged
    if ($status === 'committed') {
        $existingSolutionsQuery->whereHas('version.internal_summary', fn($iq) => $iq->where('is_logged', 1));
    } elseif ($status === 'pending') {
        $existingSolutionsQuery->where(function ($q) {
            $q->whereDoesntHave('version.internal_summary')
              ->orWhereHas('version.internal_summary', fn($iq) => $iq->whereNull('is_logged')->orWhere('is_logged', 0));
        });
    }

    $existingSolutions = $existingSolutionsQuery->get();

    // ========= QUOTATIONS TANPA SOLUTION (auto-rows) =========
    $existingQuotationIds = $existingSolutions->pluck('quotation_id')->toArray();

    $quotationsQuery = \App\Models\Quotation::with([
            'version.internal_summary',
            'project.customer'
        ])
        ->whereNotIn('id', $existingQuotationIds)
        ->when($customerId, fn($q) => $q->whereHas('project', fn($qq) => $qq->where('customer_id', $customerId)))
        ->when($projectId,  fn($q) => $q->where('project_id', $projectId));

    if ($status === 'committed') {
        $quotationsQuery->whereHas('version.internal_summary', fn($iq) => $iq->where('is_logged', 1));
    } elseif ($status === 'pending') {
        $quotationsQuery->where(function ($q) {
            $q->whereDoesntHave('version.internal_summary')
              ->orWhereHas('version.internal_summary', fn($iq) => $iq->whereNull('is_logged')->orWhere('is_logged', 0));
        });
    }

    $quotations = $quotationsQuery->get();

    
    // Existing solutions → status DIKIRA dari internal_summary (bukan field solution/quotation)
    $existingRows = $existingSolutions->map(function ($s) {
        $isCommitted = optional(optional($s->version)->internal_summary)->is_logged == 1;
        return (object)[
            'version_name'  => $s->version_name ?? optional($s->version)->version_name ?? '-',
            'customer_name' => $s->customer_name ?? optional($s->customer)->name ?? '-',
            'project_name'  => $s->project_name  ?? optional($s->project)->name ?? '-',
            'status'        => $isCommitted ? 'Committed' : 'Pending',
            'quotation_id'  => $s->quotation_id,
            'version_id'    => $s->version_id,
            'is_auto'       => false,
        ];
    });

    // Auto rows dari quotations
    $autoRows = $quotations->map(function ($q) {
        $isCommitted = optional(optional($q->version)->internal_summary)->is_logged == 1;
        return (object)[
            'version_name'  => optional($q->version)->version_name ?? '-',
            'customer_name' => optional($q->project->customer)->name ?? '-',
            'project_name'  => optional($q->project)->name ?? '-',
            'status'        => $isCommitted ? 'Committed' : 'Pending',
            'quotation_id'  => $q->id,
            'version_id'    => $q->version_id,
            'is_auto'       => true,
        ];
    });

    $allSolutions = $existingRows->concat($autoRows);

    // Dropdown sources (kekalkan logic role seperti sedia ada)
    $user   = auth()->user();
    $userId = $user->id;

    if ($user->role === 'admin') {
        $customers = \App\Models\Customer::orderBy('name')->get(['id','name']);
        $projects  = \App\Models\Project::orderBy('name')->get(['id','name']);
    } else {
        $customers = \App\Models\Customer::whereHas('projects', function ($q) use ($userId) {
                $q->where(function ($qq) use ($userId) {
                    $qq->where('presale_id', $userId)
                       ->orWhereHas('assigned_presales', fn($q2) => $q2->where('users.id', $userId));
                });
            })
            ->orderBy('name')->get(['id','name']);

        $projects = \App\Models\Project::where(function ($q) use ($userId) {
                $q->where('presale_id', $userId)
                  ->orWhereHas('assigned_presales', fn($q2) => $q2->where('users.id', $userId));
            })
            ->orderBy('name')->get(['id','name']);
    }

    // Quotation untuk "Add Solution" (tak perlu tapis status lagi)
    $existingIdsAll = \App\Models\Solution::pluck('quotation_id')->toArray();
    $availableQuotations = \App\Models\Quotation::with(['version','project.customer'])
        ->whereNotIn('id', $existingIdsAll)
        ->orderByDesc('created_at')
        ->get();

    return view('solutions.index', [
        'solutions'           => $allSolutions,
        'availableQuotations' => $availableQuotations,
        'presales'            => \App\Models\User::where('role', 'presale')->get(),
        'customers'           => $customers,
        'projects'            => $projects,
        'filters'             => [
            'customer_id' => $customerId,
            'project_id'  => $projectId,
            'status'      => $statusRaw,
        ],
    ]);
}


    public function store(Request $request)
    {
        // Jadikan status optional; default 'pending'
        $request->validate([
            'quotation_id' => 'required|uuid|exists:quotations,id',
            'presale_id'   => 'required|uuid|exists:users,id',
            'status'       => 'nullable|in:pending,committed,Pending,Committed,PENDING,COMMITTED',
        ]);

        $status = $request->input('status', 'pending');

        $quotation = Quotation::with(['version', 'project.customer'])->findOrFail($request->quotation_id);

        Solution::create([
            'quotation_id'  => $quotation->id,
            'version_id'    => $quotation->version_id,
            'project_id'    => $quotation->project_id,
            'customer_id'   => $quotation->project->customer_id,
            'presale_id'    => $request->presale_id,
            'version_name'  => optional($quotation->version)->version_name,
            'project_name'  => optional($quotation->project)->name,
            'customer_name' => optional($quotation->project->customer)->name,
            'status'        => $status,
        ]);

        return redirect()->route('solutions.index')->with('success', 'Solution created successfully.');
    }
}
