<?php

namespace App\Http\Controllers;

use App\Models\ECSFlavour;
use App\Models\Service;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class ECSFlavourController extends Controller
{
   

    public function index(Request $request)
{
    $query = ECSFlavour::query();

    // Filter by Type
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // Filter by Generation
    if ($request->filled('generation')) {
        $query->where('generation', $request->generation);
    }

    // Filter by vCPU
    if ($request->filled('vcpu')) {
        $query->where('vCPU', $request->vcpu);
    }

    // Filter by ECS Code (partial match)
    if ($request->filled('ecs_code')) {
        $query->where('ecs_code', 'like', '%' . $request->ecs_code . '%');
    }

    $ecs_flavours = $query->orderBy('id', 'asc')->get();

    // Dropdown options
    $allTypes = ECSFlavour::select('type')->distinct()->pluck('type');
    $allGenerations = ECSFlavour::select('generation')->distinct()->pluck('generation');
    $allVcpu = ECSFlavour::select('vCPU')->distinct()->pluck('vCPU')->sort();
    $allEcsCodes = ECSFlavour::select('ecs_code')->distinct()->pluck('ecs_code');

        $computeServices = Service::where('category_name', 'Compute')
        ->orderBy('name')
        ->get(['id', 'name', 'code', 'measurement_unit', 'charge_duration', 'description']);


    return view('products.ecsflavour.index', compact(
        'ecs_flavours',
        'allTypes',
        'allGenerations',
        'allVcpu',
        'allEcsCodes',
         'computeServices'
    ));
}

    
   


public function store(Request $request)
{
    $validated = $request->validate([
        'ecs_code' => [
            'required','string',
            Rule::exists('services','code')->where(fn($q) => $q->where('category_name','Compute')),
        ],
      
        'vCPU' => 'required|integer|min:0',
        'RAM' => 'required|integer|min:0',
        'type' => 'required|string',
        'generation' => 'required|string',
        'memory_label' => 'required|string',
        'windows_license_count' => 'required|integer|min:0',
        'red_hat_enterprise_license_count' => 'required|integer|min:0',
        'microsoft_sql_license_count' => 'required|integer|min:0',
    ]);

   
    $service = Service::where('code', trim($request->ecs_code))
        ->where('category_name','Compute')
        ->first();
    $validated['flavour_name'] = $service->name ?? '—';

  
    $validated['dr']             = $request->boolean('dr');
    $validated['pin']            = $request->boolean('pin');
    $validated['gpu']            = $request->boolean('gpu');
    $validated['dedicated_host'] = $request->boolean('dedicated_host');

    ECSFlavour::create($validated);

    return redirect()->route('ecs-flavours.index')->with('success', 'ECS Flavour added successfully.');
}


public function edit($id)
{
    $ecs_flavour = ECSFlavour::findOrFail($id);
    return view('products.ecsflavour.edit', compact('ecs_flavour'));
}
public function update(Request $request, $id)
{
    $validated = $request->validate([
        'ecs_code' => [
            'required','string',
            Rule::exists('services','code')->where(fn($q) => $q->where('category_name','Compute')),
        ],
    
        'vCPU' => 'required|integer|min:0',
        'RAM' => 'required|integer|min:0',
        'type' => 'required|string',
        'generation' => 'required|string',
        'memory_label' => 'required|string',
        'windows_license_count' => 'required|integer|min:0',
        'red_hat_enterprise_license_count' => 'required|integer|min:0',
        'microsoft_sql_license_count' => 'required|integer|min:0',
    ]);

   
    $service = Service::where('code', trim($request->ecs_code))
        ->where('category_name','Compute')
        ->first();
    $validated['flavour_name'] = $service->name ?? '—';

    $validated['dr']             = $request->boolean('dr');
    $validated['pin']            = $request->boolean('pin');
    $validated['gpu']            = $request->boolean('gpu');
    $validated['dedicated_host'] = $request->boolean('dedicated_host');

    ECSFlavour::findOrFail($id)->update($validated);

    return redirect()->route('ecs-flavours.index')->with('success', 'ECS Flavour updated successfully.');
}





public function destroy($id)
{
    $ecs_flavour = ECSFlavour::findOrFail($id);
    $ecs_flavour->delete();

    return redirect()->route('ecs-flavours.index')->with('success', 'ECS Flavour deleted successfully.');
}




   
    private function parseBoolean($value)
    {
        $value = strtolower(trim($value));
        return in_array($value, ['yes', '1']) ? 1 : 0;
    }

   


public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file'), 'r');
    if (!$file) {
        return back()->with('error', 'Failed to open the CSV file.');
    }

    $header = fgetcsv($file); 

    $created = 0;
    $skipped = [];
    $line    = 1; 

    while (($row = fgetcsv($file)) !== false) {
        $line++;

       
        $nonEmpty = array_filter($row, fn($v) => $v !== null && trim($v) !== '');
        if (count($nonEmpty) === 0) continue;

        $ecsCode = trim($row[0] ?? '');
        if ($ecsCode === '') {
            $skipped[] = "Line $line: missing ECS Service Code";
            continue;
        }

        
        $service = Service::where('code', $ecsCode)
            ->where('category_name', 'Compute')
            ->first();

        if (!$service) {
          
            $skipped[] = "Line $line ($ecsCode): service not found in Services (Compute)";
            continue;
        }

        ECSFlavour::create([
            'ecs_code'  => $ecsCode,
           
            'flavour_name' => $service->name,

          
            'vCPU'      => (int)($row[2]  ?? 0),
            'RAM'       => (int)($row[3]  ?? 0),
            'type'      => (string)($row[4] ?? ''),
            'generation'=> (string)($row[5] ?? ''),
            'memory_label' => (string)($row[6] ?? ''),

            'windows_license_count'            => (int)($row[7]  ?? 0),
            'red_hat_enterprise_license_count' => (int)($row[8]  ?? 0),

            'dr'             => $this->parseBoolean($row[9]  ?? ''),
            'pin'            => $this->parseBoolean($row[10] ?? ''),
            'gpu'            => $this->parseBoolean($row[11] ?? ''),
            'dedicated_host' => $this->parseBoolean($row[12] ?? ''),

            'microsoft_sql_license_count' => (int)($row[13] ?? 0),
        ]);

        $created++;
    }

    fclose($file);

    $msg = "Imported {$created} ECS Flavour(s).";
    if (!empty($skipped)) {
        $msg .= ' Skipped: '.count($skipped).'.';

        return back()->with('success', $msg)->with('import_skipped', $skipped);
    }

    return back()->with('success', $msg);
}






  public function export()
    {
        $ecs_flavours = ECSFlavour::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ecsflavour_export.csv"',
        ];

        $callback = function () use ($ecs_flavours) {
            $handle = fopen('php://output', 'w');

            // Header
            fputcsv($handle, [
                'ECS Service Code', 'Flavour Name', 'vCPU', 'vRAM', 'Type', 'Generation', 
                'Memory label', 'Windows License Count', 'Red Hat Enterprise License Count','DR', 
                'Pin', 'GPU', 'Dedicated Host', 'Microsoft SQL License Count'
            ]);

            // Data
            foreach ($ecs_flavours as $ecs_flavour) {
                fputcsv($handle, [
                    $ecs_flavour->ecs_code,
                    $ecs_flavour->flavour_name,
                    $ecs_flavour->vCPU,
                    $ecs_flavour->RAM,
                    $ecs_flavour->type,
                    $ecs_flavour->generation,
                    $ecs_flavour->memory_label,
                    $ecs_flavour->windows_license_count,
                    $ecs_flavour->red_hat_enterprise_license_count,
                    $ecs_flavour->dr ? 'Yes' : 'No',
                    $ecs_flavour->pin ? 'Yes' : 'No',
                    $ecs_flavour->gpu ? 'Yes' : 'No',
                    $ecs_flavour->dedicated_host ? 'Yes' : 'No',
                    $ecs_flavour->microsoft_sql_license_count
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}


