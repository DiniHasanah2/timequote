<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with all customers and statistics
     */
    public function adminDashboard(): View
    {

        $customers = Customer::withCount([
                'projects',
                'quotations',
                'quotations as pending_quotations_count' => function($query) {
                    $query->where('status', 'pending');
                }
            ])
            ->with('presale')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalQuotations = Quotation::count();
        $pendingQuotations = Quotation::where('status', 'pending')->count();
        $totalUsers = User::count();//User::where('role', 'presale')->count();
        
        $customers = Customer::with(['presale', 'projects'])
        ->withCount('projects')
        ->latest()
        ->get();

        return view('admindashboard', compact(

        'totalQuotations',
        'pendingQuotations',
        'totalUsers',
        'customers',
        ));
    }

    //Display presale dashboard with all customers (all presales)
   public function presaleDashboard(): View
{
    $user = Auth::user();

    /*if (!$user) {
        abort(403, 'Unauthorized');
    }*/


    if (!$user || !in_array($user->role, ['presale', 'product'])) {
    abort(403, 'Unauthorized');
}


    // Semua project yang dibuat oleh user ini
    $projectIds = \App\Models\Project::where('presale_id', $user->id)->pluck('id');

    // Gabungkan customer:
    // - dimiliki oleh user (presale_id)
    // - ATAU customer ada project yang dimiliki user
    $customers = Customer::where(function ($q) use ($user) {
            $q->where('presale_id', $user->id)
             ->orWhere('created_by', $user->id)
              ->orWhereHas('projects', function ($sub) use ($user) {
                  $sub->where('presale_id', $user->id);
              });
        })
        ->withCount(['projects', 'quotations'])
        ->get();

    $totalQuotations = Quotation::whereIn('project_id', $projectIds)->count();

    $pendingQuotations = Quotation::whereIn('project_id', $projectIds)
        ->where('status', 'pending')
        ->count();

    return view('presaledashboard', compact(
        'customers',
        'totalQuotations',
        'pendingQuotations'
    ));
}



    /**
     * Display presale dashboard with only assigned customers
     */
    /*public function presaleDashboard(): View
    {
        // @var \App\Models\User $user 
        $user = Auth::user();
    
        if (!$user) {
             \Log::error('No authenticated user in presaleDashboard');
        abort(403, 'Unauthorized');
        }

          \Log::info('User attempting presale dashboard', ['user_id' => $user->id, 'username' => $user->username]);


        //Customer B (milik presale1) memang tak muncul untuk presale2, walaupun presale2 dah edit project/quotation milik customer tu.
        $customers = Customer::where('presale_id', $user->id)
            ->withCount(['projects', 'quotations'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalQuotations = DB::table('quotations')
            ->join('projects', 'quotations.project_id', '=', 'projects.id')
            ->join('customers', 'projects.customer_id', '=', 'customers.id')
            ->where('customers.presale_id', $user->id)
            ->count();

        $pendingQuotations = DB::table('quotations')
            ->join('projects', 'quotations.project_id', '=', 'projects.id')
            ->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                      ->from('customers')
                      ->whereColumn('customers.id', 'projects.customer_id')
                      ->where('customers.presale_id', $user->id);
            })
            ->where('quotations.status', 'pending')
            ->count();
    
        return view('presaledashboard', compact(
            'customers',
            'totalQuotations',
            'pendingQuotations'
        ));
    }*/
}