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


public function index(Request $request): View
{
    $user = Auth::user();

    $query = Customer::query();

   
    if ($user->role === 'presale') {
        if ($user->division) {
            $query->ofDivision($user->division);
        } else {
            $query->whereRaw('1=0');
        }
    }

    
    $keyword    = trim($request->query('q', ''));
    $department = $request->query('department');

    if ($keyword !== '') {
        $query->where('name', 'LIKE', "%{$keyword}%");
    }

    if (!empty($department)) {
        $query->where('department', $department);
    }

    $customers = $query->orderBy('created_at', 'asc')->get();


    

    $presales = in_array($user->role, ['admin','product'])
        ? \App\Models\User::where('role', 'presale')->orderBy('name')->get()
        : null;

    $clientManagers = \App\Models\ClientManager::orderBy('name')->get();

    $deptMap = $this->deptMap;
    $deptOptions = ($user->role === 'presale' && $user->division)
        ? ($deptMap[$user->division] ?? [])
        : array_values(array_unique(array_merge($deptMap['Enterprise'], $deptMap['Wholesale'])));

    return view('customers.index', compact('customers', 'presales', 'clientManagers', 'user', 'deptOptions'));
}

    

   
    public function create(): View
{
    $clientManagers = ClientManager::orderBy('name')->get(); 
    $user = Auth::user();

   
    $presales = ($user->role === 'admin') 
        ? User::where('role', 'presale')->orderBy('name')->get()
        : null;

    return view('customers.create', compact('clientManagers', 'presales'));
}

private array $deptMap = [
    'Enterprise' => [
        "Financial Services",
        "Manufacturing & Automotive",
        "State Government",
        "Region-Southern & Eastern",
        "Hospitality & Healthcare",
        "GLC",
        "Education",
        "Banks",
        "Enterprise Technology",
        "Oil & Gas",
        "Public Sector",
        "Region-Northern",
        "Federal Government",
        "Enterprise & Public Sector Business",
        "Retail & Media",
    ],
    'Wholesale' => [
        "Wholesale",
        "OTT",
        "ASP",
        "Global",
        "Domestic",
    ],
];

public function store(Request $request)
{
    \Log::info('Store Request Data:', $request->all());

    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login first!');
    }

    $user = Auth::user();

    $rules = [
        'name'            => 'required|string|max:255',
        'business_number' => 'nullable|string|max:255',
        'division'        => 'required|in:Enterprise,Wholesale',
        'department'      => 'required|string|max:255',
        'client_manager_id' => 'required|string|exists:client_manager,name',
    ];

    if ($user->role === 'admin' || $user->role === 'product') {
        $rules['presale_id'] = 'required|exists:users,id';
    }

    $validated = $request->validate($rules);

    
    $validDept = $this->deptMap[$validated['division']] ?? [];
    if (!in_array($validated['department'], $validDept, true)) {
        return back()->withInput()->with('error', 'Department is not valid for the chosen Division.');
    }

   
    if ($user->role === 'admin' || $user->role === 'product') {
        $presaleId   = $request->presale_id;
        $presaleUser = User::findOrFail($presaleId);

       
        if (is_null($presaleUser->division)) {
        
            $presaleUser->division = $validated['division'];
            $presaleUser->save();
        } elseif ($presaleUser->division !== $validated['division']) {
            return back()->withInput()->with('error',
                "Selected Presale is locked to {$presaleUser->division} division.");
        }
    } else {
        
        $presaleId   = $user->id;
        $presaleUser = $user;

        if (is_null($user->division)) {
         
            $user->division = $validated['division'];
            $user->save();
        } elseif ($validated['division'] !== $user->division) {
            return back()->withInput()->with('error',
                "You are locked to {$user->division} division.");
        }
    }

   
    $manager = ClientManager::where('name', $validated['client_manager_id'])->first();
    if (!$manager) {
        return back()->withErrors(['client_manager_id' => 'Client Manager not found.']);
    }

    $normalizedName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validated['name']));
    if (Customer::where('normalized_name', $normalizedName)->exists()) {
        return redirect()->back()->withInput()->with('error', 'Customer with a similar name already exists!');
    }

    Customer::create([
        'id'               => \Str::uuid(),
        'name'             => $validated['name'],
        'normalized_name'  => $normalizedName,
        'business_number'  => $validated['business_number'] ?? null,
        'division'         => $validated['division'],   
        'department'       => $validated['department'], 
        'presale_id'       => $presaleId,
        'presale_name'     => $presaleUser->name,
        'client_manager_id'=> $manager->id,
        'client_manager'   => $manager->name,
        'created_by'       => $user->id,
    ]);

    return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
}

public function update(Request $request, Customer $customer)
{
    $user = Auth::user();

    if (!in_array($user->role, ['admin','product']) && $customer->presale_id != $user->id) {
        abort(403, 'Unauthorized action.');
    }

    $rules = [
        'client_manager_id' => 'required|string',
        'division'   => 'required|in:Enterprise,Wholesale',
        'department' => 'required|string|max:100',
    ];

    if ($user->role === 'admin' || $user->role === 'product') {
        $rules['name']      = 'required|string|max:255';
        $rules['presale_id']= 'required|exists:users,id';
    }

    $validated = $request->validate($rules);

    
    $validDept = $this->deptMap[$validated['division']] ?? [];
    if (!in_array($validated['department'], $validDept, true)) {
        return back()->withInput()->with('error', 'Department is not valid for the chosen Division.');
    }

    $manager = ClientManager::where('name', $validated['client_manager_id'])->first();
    if (!$manager) {
        return back()->withErrors(['client_manager_id' => 'Client Manager not found.']);
    }
    $validated['client_manager_id'] = $manager->id;
    $validated['client_manager']    = $manager->name;

    if ($user->role === 'admin' || $user->role === 'product') {
        
        $presaleUser = User::findOrFail($validated['presale_id']);
        if (is_null($presaleUser->division)) {
            $presaleUser->division = $validated['division'];
            $presaleUser->save();
        } elseif ($presaleUser->division !== $validated['division']) {
            return back()->withInput()->with('error',
                "Selected Presale is locked to {$presaleUser->division} division.");
        }

      
        $validated['presale_name'] = $presaleUser->name;
    } else {
      
        if ($user->division !== $validated['division']) {
            return back()->withInput()->with('error',
                "You are locked to {$user->division} division.");
        }
        
        $validated['presale_id']   = $user->id;
        $validated['presale_name'] = $user->name;
    }

    $customer->update($validated);

    return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
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




 
    public function show(Customer $customer)
    {
        $customer->formatted_created_at = Carbon::parse($customer->created_at)
            ->format('d/m/Y');
        return view('customers.show', compact('customer'));
    }

    
}