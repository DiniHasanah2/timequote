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
   

     public function adminDashboard(): View
{
    $customers = Customer::with(['presale'])
        ->withCount([
            'projects',
            'quotations',
            'quotations as pending_quotations_count' => function ($q) {
                $q->where('quotations.status', 'pending');
            },
        ])
        ->orderByDesc('customers.created_at') 
        ->get();

    $totalQuotations   = Quotation::count();
    $pendingQuotations = Quotation::where('status', 'pending')->count();
    $totalUsers        = User::count();

    return view('admindashboard', compact(
        'totalQuotations',
        'pendingQuotations',
        'totalUsers',
        'customers',
    ));
}

  
   public function presaleDashboard(): View
{
    $user = Auth::user();

   


    if (!$user || !in_array($user->role, ['presale', 'product'])) {
    abort(403, 'Unauthorized');
}


  
    $projectIds = \App\Models\Project::where('presale_id', $user->id)->pluck('id');

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



   
}