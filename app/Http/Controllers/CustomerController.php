<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Customer;
use App\Models\User;
use App\Models\ClientManager;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerController extends Controller
{
    // Display list of customers for the logged-in user
    public function index(): View
    {
        $user = Auth::user();
    
        $query = Customer::query();
        
        //Filter for presale users (presale only view owns Customer)
        /*if ($user->role !== 'admin') {
            $query->where('presale_id', $user->id); 
        }*/

        if ($user->role === 'presale') {
    // show all presale customers (optional)
    // $query->whereHas('presale'); // or just remove the filter
}

        
        $customers = $query->orderBy('created_at', 'asc')->get();
        
        // Only fetch presales if user is admin (to avoid unnecessary query)
        $presales = ($user->role === 'admin') 
            ? User::where('role', 'presale')->get() 
            : null;

            $clientManagers = ClientManager::orderBy('name')->get();

        return view('customers.index', compact('customers', 'presales', 'clientManagers'));
    }

    // Display form to add new customer

   
    public function create(): View
{
    $clientManagers = ClientManager::orderBy('name')->get(); // fetch semua client manager
    $user = Auth::user();

    // Only admin can choose presale manually
    $presales = ($user->role === 'admin') 
        ? User::where('role', 'presale')->orderBy('name')->get()
        : null;

    return view('customers.create', compact('clientManagers', 'presales'));
}

    // Store new customer data
    public function store(Request $request)
    {
          \Log::info('Store Request Data:', $request->all()); 
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }

        $user = Auth::user();

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'business_number' => 'nullable|string|max:255',
            'division' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'client_manager_id' => 'required|string|exists:client_manager,name',

        ];

        if ($user->role === 'admin') {
            $rules['presale_id'] = 'required|exists:users,id';
        }

        $validatedData = $request->validate($rules);

         \Log::info('Validated Data:', $validatedData);
        // Determine presale_id based on role
        $presaleId = $user->role === 'admin' ? $request->presale_id : $user->id;
        
        // Get presale user's name
        $presaleUser = User::findOrFail($presaleId);

        $manager = ClientManager::where('name', $request->client_manager_id)->first();

if (!$manager) {
    return back()->withErrors(['client_manager_id' => 'Client Manager not found.']);
}
$normalizedName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validatedData['name']));

if (Customer::where('normalized_name', $normalizedName)->exists()) {
    return redirect()->back()
        ->withInput()
        ->with('error', 'Customer with a similar name already exists!');
}








        Customer::create([
            'id' => \Str::uuid(), //just added
            'name' => $validatedData['name'],
             'normalized_name' => $normalizedName,
            'business_number' => $validatedData['business_number'] ?? null,
            'division' => $validatedData['division'] ?? null,
            'department' => $validatedData['department'] ?? null,
            'presale_id' => $presaleId,
            'presale_name' => $presaleUser->name,
            //'client_manager_id' => $validatedData['client_manager_id'], 
            'client_manager_id' => $manager->id,
            'client_manager' => $manager->name,
            'created_by' => $user->id,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

   

    public function edit(Customer $customer)
{
    $user = Auth::user();

    $canEdit = false;

    if ($user->role === 'admin') {
        $canEdit = true;
    } elseif ($user->role === 'presale' && $customer->presale_id == $user->id) {
        $canEdit = true;
    }

    $presales = ($user->role === 'admin') 
        ? User::where('role', 'presale')->get() 
        : null;

    $clientManagers = ClientManager::orderBy('name')->get();

    return view('customers.edit', compact('customer', 'presales', 'clientManagers', 'canEdit'));
}




    // Display single customer
    public function show(Customer $customer)
    {
        $customer->formatted_created_at = Carbon::parse($customer->created_at)
            ->format('d/m/Y');
        return view('customers.show', compact('customer'));
    }

    // Process update (using route model binding)
    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();
    
        // Non-admin users can only edit their own customers
        if ($user->role !== 'admin' && $customer->presale_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Validation rules
        $rules['client_manager_id'] = 'required|string';
        

        
        if ($user->role === 'admin') {
            $rules['name'] = 'required|string|max:255';
            $rules['presale_id'] = 'required|exists:users,id';
        }

          $rules['division'] = 'required|string|max:100';
            $rules['department'] = 'required|string|max:100';
        
        $validated = $request->validate($rules);

        // Get the client manager's name
    

     $manager = ClientManager::where('name', $validated['client_manager_id'])->first();

if (!$manager) {
    return back()->withErrors(['client_manager_id' => 'Client Manager not found.']);
}
    $validated['client_manager_id'] = $manager->id;
    $validated['client_manager'] = $manager->name;

    
        // Update presale_name if admin changes presale_id
        if ($user->role === 'admin' && isset($validated['presale_id'])) {
            $presaleUser = User::findOrFail($validated['presale_id']);
            $validated['presale_name'] = $presaleUser->name;
        }
        
        $customer->update($validated);
        
        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }
}