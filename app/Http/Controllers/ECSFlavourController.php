<?php

namespace App\Http\Controllers;

use App\Models\ECSFlavour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class ECSFlavourController extends Controller
{
    /*public function index()
    {
        
        $ecs_flavours = ECSFlavour::orderBy('id', 'asc')->get();

        return view('products.ecsflavour.index', compact('ecs_flavours'));
    }*/

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

    return view('products.ecsflavour.index', compact(
        'ecs_flavours',
        'allTypes',
        'allGenerations',
        'allVcpu',
        'allEcsCodes'
    ));
}

    
    public function store(Request $request)
{
    $validated = $request->validate([
        'ecs_code' => 'required|string',
        'flavour_name' => 'required|string',
        'vCPU' => 'required|integer|min:0',
        'RAM' => 'required|integer|min:0',
        'type' => 'required|string',
        'generation' => 'required|string',
        'memory_label' => 'required|string',
        'windows_license_count' => 'required|integer|min:0',
        'red_hat_enterprise_license_count' => 'required|integer|min:0',
        'microsoft_sql_license_count' => 'required|integer|min:0',
    ]);
     $validated['dr'] = $request->has('dr');
    $validated['pin'] = $request->has('pin');
    $validated['gpu'] = $request->has('gpu');
    $validated['dedicated_host'] = $request->has('dedicated_host');

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
        'ecs_code' => 'required|string',
        'flavour_name' => 'required|string',
        'vCPU' => 'required|integer|min:0',
        'RAM' => 'required|integer|min:0',
        'type' => 'required|string',
        'generation' => 'required|string',
        'memory_label' => 'required|string',
        'windows_license_count' => 'required|integer|min:0',
        'red_hat_enterprise_license_count' => 'required|integer|min:0',
        'microsoft_sql_license_count' => 'required|integer|min:0',
    ]);

    $validated['dr'] = $request->has('dr');
    $validated['pin'] = $request->has('pin');
    $validated['gpu'] = $request->has('gpu');
    $validated['dedicated_host'] = $request->has('dedicated_host');

    ECSFlavour::findOrFail($id)->update($validated);

    return redirect()->route('ecs-flavours.index')->with('success', 'ECS Flavour updated successfully.');
}




public function destroy($id)
{
    $ecs_flavour = ECSFlavour::findOrFail($id);
    $ecs_flavour->delete();

    return redirect()->route('ecs-flavours.index')->with('success', 'ECS Flavour deleted successfully.');
}

/*public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file'), 'r');
    $header = fgetcsv($file); // skip header

    while (($row = fgetcsv($file)) !== false) {
        ECSFlavour::create([
            'ecs_code' => $row[0],
            'flavour_name' => $row[1],
            'vCPU' => $row[2],
            'RAM' => $row[3],
            'type' => $row[4],
            'generation' => $row[5],
            'memory_label' => $row[6],
            'windows_license_count' => $row[7],
            'red_hat_enterprise_license_count' => $row[8],
            'pin' => $row[9] === 'Yes' ? 1 : 0,
            'gpu' => $row[10] === 'Yes' ? 1 : 0,
            'dedicated_host' => $row[11] === 'Yes' ? 1 : 0,
            'microsoft_sql_license_count' => $row[12],


        ]);
    }

    fclose($file);

    return redirect()->back()->with('success', 'ECS Flavours imported successfully.');
}*/




   
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
        $header = fgetcsv($file); // skip header

        while (($row = fgetcsv($file)) !== false) {
            ECSFlavour::create([
                'ecs_code' => $row[0],
                'flavour_name' => $row[1],
                'vCPU' => $row[2],
                'RAM' => $row[3],
                'type' => $row[4],
                'generation' => $row[5],
                'memory_label' => $row[6],
                'windows_license_count' => $row[7],
                'red_hat_enterprise_license_count' => $row[8],
                'dr' => $this->parseBoolean($row[9]),
                'pin' => $this->parseBoolean($row[10]),
                'gpu' => $this->parseBoolean($row[11]),
                'dedicated_host' => $this->parseBoolean($row[12]),
                'microsoft_sql_license_count' => $row[13],
            ]);
        }

        fclose($file);

        return redirect()->back()->with('success', 'ECS Flavours imported successfully.');
    }





/*public function export()
{
    $ecs_flavours = \App\Models\ECSFlavour::all();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="ecsflavour_export.csv"',
    ];

    $callback = function () use ($ecs_flavours) {
        $handle = fopen('php://output', 'w');

        // Header baris pertama
        fputcsv($handle, ['ECS Service Code', 'Flavour Name', 'vCPU', 'vRAM', 'Type', 'Generation', 'Memory label', 'Windows License Count', 'Red Hat Enterprise License Count', 'Pin', 'GPU', 'Dedicated Host', 'Microsoft SQL License Count']);

        // Data baris seterusnya
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
                $ecs_flavour->pin,
                $ecs_flavour->gpu,
                $ecs_flavour->dedicated_host,
                $ecs_flavour->microsoft_sql_license_count


            ]);
        }

        fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
}*/







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


