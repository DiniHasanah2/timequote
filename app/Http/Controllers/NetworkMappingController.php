<?php
namespace App\Http\Controllers;

use App\Models\NetworkMapping;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\NetworkMappingLog;


class NetworkMappingController extends Controller
{
   

    public function index()
{
    $network_mappings = NetworkMapping::orderBy('created_at', 'asc')->get();

  
    $networkServices = \App\Models\Service::where('category_name', 'Cloud Network')
        ->orderBy('name')
        ->get(['id','name','code','description','measurement_unit','charge_duration']);

    return view('products.networkmapping.index', compact('network_mappings','networkServices'));
}



public function store(Request $request)
{
    $validated = $request->validate([
        'network_code' => [
            'required','string',
            'unique:network_mappings,network_code',
            Rule::exists('services','code')->where(fn($q)=>$q->where('category_name','Cloud Network')),
        ],
        'min_bw'  => 'required|numeric|min:0',
        'max_bw'  => 'required|numeric|min:0|gte:min_bw',
        'eip_foc' => 'required|integer|min:0',
        'anti_ddos' => 'nullable|in:on',
    ]);

    try {
        \DB::beginTransaction();

        $created = NetworkMapping::create([
            'network_code' => $validated['network_code'],
            'min_bw'       => $validated['min_bw'],
            'max_bw'       => $validated['max_bw'],
            'eip_foc'      => $validated['eip_foc'],
            'anti_ddos'    => $request->boolean('anti_ddos'),
        ]);

        NetworkMappingLog::create([
            'network_mapping_id' => $created->id,
            'action'     => 'created',
            'old_values' => null,
            'new_values' => $created->only(['network_code','min_bw','max_bw','eip_foc','anti_ddos']),
            'user_id'    => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        \DB::commit();
        return redirect()->route('network-mappings.index')->with('success','Network mapping added successfully.');
    } catch (\Throwable $e) {
        \DB::rollBack();
        return back()->withErrors(['error'=>$e->getMessage()])->withInput();
    }
}

/*public function store(Request $request)
{
    $validated = $request->validate([
        'network_code' => 'required|string|unique:network_mappings,network_code',
        'min_bw'  => 'required|numeric|min:0',
        'max_bw'  => 'required|numeric|min:0|gte:min_bw',
        'eip_foc' => 'required|integer|min:0',
        'anti_ddos' => 'nullable|in:on',
    ]);

    try {
        DB::beginTransaction();

        // 1) Create record
        $created = NetworkMapping::create([
            'network_code' => $validated['network_code'],
            'min_bw'       => $validated['min_bw'],
            'max_bw'       => $validated['max_bw'],
            'eip_foc'      => $validated['eip_foc'],
            'anti_ddos'    => $request->has('anti_ddos'),
        ]);

        // 2) Log creation (di SINI tempatnya)
        NetworkMappingLog::create([
            'network_mapping_id' => $created->id,
            'action'     => 'created',
            'old_values' => null,
            'new_values' => $created->only(['network_code','min_bw','max_bw','eip_foc','anti_ddos']),
            'user_id'    => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        DB::commit();

        return redirect()
            ->route('network-mappings.index')
            ->with('success', 'Network mapping added successfully.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}*/



public function edit($id)
{
    $network_mapping = NetworkMapping::findOrFail($id);

    $logs = \App\Models\NetworkMappingLog::with('user')
        ->where('network_mapping_id', $id)
        ->latest()
        ->limit(50)
        ->get();

    return view('products.networkmapping.edit', compact('network_mapping','logs'));
}


public function update(Request $request, $id)
{
    $network_mapping = NetworkMapping::findOrFail($id);

    $validated = $request->validate([
        'min_bw'  => 'required|numeric|min:0',
        'max_bw'  => 'required|numeric|min:0|gte:min_bw',
        'eip_foc' => 'required|integer|min:0',
        'anti_ddos' => 'nullable|in:on',
    ]);

    // Simpan nilai lama dulu
    $before = $network_mapping->only(['min_bw','max_bw','eip_foc','anti_ddos']);

    // Apply perubahan
    $network_mapping->min_bw   = $validated['min_bw'];
    $network_mapping->max_bw   = $validated['max_bw'];
    $network_mapping->eip_foc  = $validated['eip_foc'];
    $network_mapping->anti_ddos = $request->has('anti_ddos');
    $network_mapping->save();

    // Nilai baru
    $after  = $network_mapping->only(['min_bw','max_bw','eip_foc','anti_ddos']);

    // Bezakan yang berubah sahaja
    $changes = $this->diffFields($before, $after);

    if (!empty($changes)) {
        NetworkMappingLog::create([
            'network_mapping_id' => $network_mapping->id,
            'action'   => 'updated',
            'old_values' => $before,
            'new_values' => $after,
            'user_id'  => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
    }

    return redirect()
        ->route('network-mappings.index')
        ->with('success', 'Network mapping updated successfully.');
}


public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file'), 'r');
    $header = fgetcsv($file); // skip header

    $count = 0;
    while (($row = fgetcsv($file)) !== false) {
        NetworkMapping::create([
            'network_code' => $row[0],
            'min_bw' => $row[1],
            'max_bw' => $row[2],
            'eip_foc' => $row[3],
            'anti_ddos' => $row[4] === 'Yes' ? 1 : 0,
        ]);
        $count++;
    }
    fclose($file);

    // log umum (network_mapping_id null)
    \App\Models\NetworkMappingLog::create([
        'network_mapping_id' => null,
        'action' => 'import',
        'old_values' => null,
        'new_values' => ['rows_imported' => $count],
        'user_id' => auth()->id(),
        'ip_address' => $request->ip(),
        'user_agent' => $request->header('User-Agent'),
    ]);

    return redirect()->back()->with('success', 'Services imported successfully.');
}


public function export(Request $request)
{
    $network_mappings = \App\Models\NetworkMapping::all();

    // log umum (tiada id spesifik)
    \App\Models\NetworkMappingLog::create([
        'network_mapping_id' => null,
        'action' => 'export',
        'old_values' => null,
        'new_values' => ['rows_exported' => $network_mappings->count()],
        'user_id' => auth()->id(),
        'ip_address' => $request->ip(),
        'user_agent' => $request->header('User-Agent'),
    ]);

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="networkmappings_export.csv"',
    ];

    $callback = function () use ($network_mappings) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['id','network_code', 'min_bw', 'max_bw', 'eip_foc', 'anti_ddos']);
        foreach ($network_mappings as $nm) {
            fputcsv($handle, [
                $nm->id, $nm->network_code, $nm->min_bw, $nm->max_bw, $nm->eip_foc, $nm->anti_ddos,
            ]);
        }
        fclose($handle);
    };

    return \Illuminate\Support\Facades\Response::stream($callback, 200, $headers);
}


private function diffFields(array $old, array $new): array
{
    $changed = [];
    foreach ($new as $key => $newVal) {
        $oldVal = $old[$key] ?? null;
        // normalize boolean supaya "0/1" dan true/false tak keliru
        if (in_array($key, ['anti_ddos'], true)) {
            $oldVal = (bool) $oldVal;
            $newVal = (bool) $newVal;
        }
        if ($oldVal !== $newVal) {
            $changed[$key] = ['old' => $oldVal, 'new' => $newVal];
        }
    }
    return $changed;
}


}