<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Version;
use App\Models\SecurityService;
use App\Models\ECSConfiguration;
use App\Models\Region;
use App\Imports\NonStandardItemImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\NonStandardItem;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\NonStandardItemFile; 
use Illuminate\Support\Facades\Storage;




class NonStandardItemController extends Controller
{
    public function index($versionId)
    {
        $version = Version::findOrFail($versionId);
        $items = NonStandardItem::where('version_id', $versionId)->get();
       return view('projects.non_standard_items.index', compact('version', 'items'));


        
    }



public function create($versionId)
{
    $version = Version::with('project')->findOrFail($versionId);

     $files = NonStandardItemFile::where('version_id', $versionId)
            ->latest()->get();

    return view('projects.non_standard_items.create', [
        'project' => $version->project,
        'version' => $version,
        'security_service' => $version->security_service ?? null,
        'non_standard_items' => NonStandardItem::where('version_id', $versionId)->get(),
        'ref_files'          => $files,
    ]);
}




   


public function store(Request $request, $versionId)
{
    $version = Version::with('project')->findOrFail($versionId);

    $validated = $request->validate([
        'item_name' => 'required|string|max:100',
        'unit' => 'required|string|max:100',
        'quantity' => 'required|integer|min:1|max:128',
        'cost' => 'required|numeric|min:0',
        'mark_up' => 'required|numeric|min:0',
    ]);

    $cost = $validated['cost'];
    $markup = $validated['mark_up'];
    $validated['selling_price'] = $cost + ($cost * ($markup / 100));

    $validated['id'] = (string) Str::uuid();
    $validated['project_id'] = $version->project_id;
    $validated['customer_id'] = $version->project->customer_id;
    $validated['presale_id'] = $version->project->presale_id;
    $validated['version_id'] = $version->id;

    NonStandardItem::create($validated);

    return redirect()->back()->with('success', 'Non standard item saved successfully!');
}




public function edit($versionId, $itemId)
{
    $version = Version::findOrFail($versionId);
    $item = $version->non_standard_items()->findOrFail($itemId);

  
    return view('projects.non_standard_items.edit', compact('version', 'item'));

    
}
public function update(Request $request, $versionId, $itemId)
{
    $item = NonStandardItem::where('version_id', $versionId)->findOrFail($itemId);

    $validated = $request->validate([
        'item_name' => 'required|string|max:255',
        'unit' => 'required|string',
        'quantity' => 'required|numeric',
        'cost' => 'required|numeric',
        'mark_up' => 'required|numeric',
        'selling_price' => 'required|numeric',
    ]);

    $item->update($validated);

   

    return redirect()->route('versions.non_standard_items.create', $versionId)
                 ->with('success', 'Item updated successfully!');

}

public function destroy($versionId, $itemId)
{
    $item = NonStandardItem::where('version_id', $versionId)->findOrFail($itemId);
    $item->delete();

    
    return redirect()->route('versions.non_standard_items.create', $versionId)
                 ->with('success', 'Item updated successfully!');

}
public function import(Request $request, $versionId)
{
    $version = Version::with('project')->findOrFail($versionId);

    $request->validate([
        'import_file' => 'required|file|mimes:xlsx,xls',
    ]);

    Excel::import(new NonStandardItemImport($version), $request->file('import_file'));

    return redirect()->route('versions.non_standard_items.create', $versionId)
        ->with('success', 'Items imported successfully!');
}


 // ===== NEW: Upload any reference file =====
    public function uploadAnyFile(Request $request, $versionId)
    {
        $version = Version::with('project.customer')->findOrFail($versionId);

        // Benarkan PDF, CSV, Excel, Images, Word, PowerPoint, Text — adjust ikut keperluan
        $request->validate([
            'ref_file' => 'required|file|max:51200|mimes:pdf,csv,txt,xlsx,xls,doc,docx,ppt,pptx,png,jpg,jpeg,webp'
        ], [
            'ref_file.mimes' => 'Fail mesti jenis: PDF/CSV/TXT/Excel/Word/PPT/Images.',
            'ref_file.max' => 'Saiz fail maksimum 50MB.'
        ]);

        $f = $request->file('ref_file');
        $ext = strtolower($f->getClientOriginalExtension());
        $mime = $f->getMimeType();
        $original = $f->getClientOriginalName();

        // simpan bawah public disk supaya boleh preview via Storage::url
        $dir = "ns_items/{$versionId}";
        $storedPath = $f->store($dir, 'public'); // storage/app/public/ns_items/{version}/xxxx

        NonStandardItemFile::create([
            'project_id'   => $version->project_id,
            'version_id'   => $version->id,
            'customer_id'  => $version->project->customer_id ?? null,
            'original_name'=> $original,
            'stored_path'  => $storedPath,
            'mime_type'    => $mime,
            'size_bytes'   => $f->getSize(),
            'ext'          => $ext,
        ]);

        return back()->with('success', 'File successfully uploaded.');
    }

    // ===== NEW: Delete reference file =====
    public function deleteAnyFile($versionId, NonStandardItemFile $file)
    {
        // pastikan belong to this version
        if ($file->version_id !== $versionId) {
            abort(404);
        }

        // padam fizikal + rekod
        Storage::disk('public')->delete($file->stored_path);
        $file->delete();

        return back()->with('success', 'File successfully deleted.');
    }

    // ===== NEW: Download/or open original file =====
    public function downloadAnyFile($versionId, NonStandardItemFile $file)
    {
        if ($file->version_id !== $versionId) abort(404);
        return Storage::disk('public')->download($file->stored_path, $file->original_name);
    }
}



 


