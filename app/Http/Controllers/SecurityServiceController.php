<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Version;
use App\Models\SecurityService;
use App\Models\SecurityServiceFile;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\InternalSummary;
use Illuminate\Support\Facades\Storage;

class SecurityServiceController extends Controller
{
    public function create($versionId)
    {
         
        $version = Version::with(['project', 'security_service'])->findOrFail($versionId);

          // load pricing config
    $pricing = config('pricing');
    $summary  = InternalSummary::where('version_id', $versionId)->first();
$isLocked = (bool) optional($summary)->is_logged;
$lockedAt = optional($summary)->logged_at;

        
        return view('projects.security_service.create', [
            'project' => $version->project,
            'version' => $version,
            'security_service' => $version->security_service,
            'pricing' => $pricing,
              'isLocked'         => $isLocked,
    'lockedAt'         => $lockedAt,
        ]);
    }

   /*public function createTime($versionId)
{
    $version  = Version::with(['project', 'security_service'])->findOrFail($versionId);
    $pricing  = config('pricing');

    $summary  = InternalSummary::where('version_id', $versionId)->first();
$isLocked = (bool) optional($summary)->is_logged;
$lockedAt = optional($summary)->logged_at;

   

return view('projects.security_service.time_security_services.create', [
    'project'          => $version->project,
    'version'          => $version,
    'security_service' => $version->security_service,
    'pricing'          => $pricing,
    'isLocked'         => $isLocked,
    'lockedAt'         => $lockedAt,
]);

}*/

public function createTime($versionId)
{
    $version  = Version::with(['project', 'security_service'])->findOrFail($versionId);
    $pricing  = config('pricing');

    $summary  = InternalSummary::where('version_id', $versionId)->first();
    $isLocked = (bool) optional($summary)->is_logged;
    $lockedAt = optional($summary)->logged_at;

    // ⬇️ Fetch uploaded files for this version
    $files = SecurityServiceFile::where('version_id', $versionId)
        ->latest()
        ->get();

    return view('projects.security_service.time_security_services.create', [
        'project'          => $version->project,
        'version'          => $version,
        'security_service' => $version->security_service,
        'pricing'          => $pricing,
        'isLocked'         => $isLocked,
        'lockedAt'         => $lockedAt,
        'ref_files'        => $files, // ⬅️ pass to Blade
    ]);
}


public function store(Request $request, $versionId)
{
    $version = Version::with('project')->findOrFail($versionId);

    // ===== Resolve presale_id dulu =====
    $auth = auth()->user();
    $presaleId = $auth?->getKey();

    if (!$presaleId || !User::where('id', $presaleId)->exists()) {
        $presaleId = User::where('email', $auth->email ?? null)->value('id')
            ?? User::where('name', $auth->name ?? null)->value('id')
            ?? User::where('username', $auth->username ?? null)->value('id')
            ?? null;
    }
    if (!$presaleId && !empty($version->project->presale_id)) {
        $presaleId = $version->project->presale_id;
    }
    if (!$presaleId || !User::where('id', $presaleId)->exists()) {
        return back()->withErrors([
            'presale_id' => 'Could not resolve a valid presale user ID.'
        ])->withInput();
    }

    // ===== Section flag =====
    $section = $request->input('section', 'cloud');

    // ===== Lock guard =====
    $summary = InternalSummary::where('version_id', $versionId)->first();
    if (optional($summary)->is_logged) {
        return back()->with('error', '🔒 This version is locked in Internal Summary. Please unlock there to edit.');
    }

    // ===== Validation =====
    $rules = [
        // Time Security (monitoring)
        'kl_insight_vmonitoring'    => 'sometimes|in:Yes,No',
        'cyber_insight_vmonitoring' => 'sometimes|in:Yes,No',

        // Security Service (numbers)
        'kl_cloud_vulnerability'    => 'sometimes|nullable|integer|min:0',
        'cyber_cloud_vulnerability' => 'sometimes|nullable|integer|min:0',

        // Cloud Security (numbers)
        'kl_firewall_fortigate'     => 'sometimes|nullable|integer|min:0',
        'cyber_firewall_fortigate'  => 'sometimes|nullable|integer|min:0',
        'kl_firewall_opnsense'      => 'sometimes|nullable|integer|min:0',
        'cyber_firewall_opnsense'   => 'sometimes|nullable|integer|min:0',
        'kl_shared_waf'             => 'sometimes|nullable|integer|min:0',
        'cyber_shared_waf'          => 'sometimes|nullable|integer|min:0',
        'kl_antivirus'              => 'sometimes|nullable|integer|min:0',
        'cyber_antivirus'           => 'sometimes|nullable|integer|min:0',

        // Other Services
        'kl_gslb'                   => 'sometimes|nullable|integer|min:0',
        'cyber_gslb'                => 'sometimes|nullable|integer|min:0',
    ];

    // Managed Services options
    $managedOptions = 'None,Managed Operating System,Managed Backup and Restore,Managed Patching,Managed DR';
    for ($i = 1; $i <= 4; $i++) {
        $rules["kl_managed_services_$i"]    = "sometimes|nullable|string|in:$managedOptions";
        $rules["cyber_managed_services_$i"] = "sometimes|nullable|string|in:$managedOptions";
    }

    if ($section === 'time') {
        $rules['kl_insight_vmonitoring']    = 'required|in:Yes,No';
        $rules['cyber_insight_vmonitoring'] = 'required|in:Yes,No';
    }

    $validated = $request->validate($rules);

    // ===== Base payload common fields =====
    $base = [
        'version_id'  => $version->id,
        'project_id'  => $version->project_id,
        'customer_id' => $version->project->customer_id,
        'presale_id'  => $presaleId,
    ];

    $exists = \App\Models\SecurityService::where('version_id', $version->id)->exists();

    // ===== Build payload ikut section =====
    $payload = $base;

    if ($section === 'cloud') {
        // Ambil fields cloud yang dihantar
        $cloudKeys = [
            'kl_firewall_fortigate','cyber_firewall_fortigate',
            'kl_firewall_opnsense','cyber_firewall_opnsense',
            'kl_shared_waf','cyber_shared_waf',
            'kl_antivirus','cyber_antivirus',
            'kl_gslb','cyber_gslb',
            'kl_cloud_vulnerability','cyber_cloud_vulnerability',
        ];
        foreach ($cloudKeys as $k) {
            if (array_key_exists($k, $validated)) {
                $payload[$k] = $validated[$k];
            }
        }

        // Managed Services: pastikan tak null → default 'None'
        for ($i = 1; $i <= 4; $i++) {
            $payload["kl_managed_services_$i"]    = $request->input("kl_managed_services_$i", 'None') ?: 'None';
            $payload["cyber_managed_services_$i"] = $request->input("cyber_managed_services_$i", 'None') ?: 'None';
        }

    } else { // section === 'time'
        // HANYA update field time/monitoring + vulnerability
        $timeKeys = [
            'kl_insight_vmonitoring','cyber_insight_vmonitoring',
            'kl_cloud_vulnerability','cyber_cloud_vulnerability',
        ];
        foreach ($timeKeys as $k) {
            if (array_key_exists($k, $validated)) {
                $payload[$k] = $validated[$k];
            }
        }

        // Jika rekod belum wujud dan user save dari page time dulu,
        // isi default utk Managed Services supaya NOT NULL lepas create
        if (!$exists) {
            for ($i = 1; $i <= 4; $i++) {
                $payload["kl_managed_services_$i"]    = 'None';
                $payload["cyber_managed_services_$i"] = 'None';
            }
            // (Optional) elak null pada cloud numbers jika NOT NULL di DB
            $payload += [
                'kl_firewall_fortigate' => 0, 'cyber_firewall_fortigate' => 0,
                'kl_firewall_opnsense'  => 0, 'cyber_firewall_opnsense'  => 0,
                'kl_shared_waf'         => 0, 'cyber_shared_waf'         => 0,
                'kl_antivirus'          => 0, 'cyber_antivirus'          => 0,
                'kl_gslb'               => 0, 'cyber_gslb'               => 0,
            ];
        }
    }

    \App\Models\SecurityService::updateOrCreate(
        ['version_id' => $version->id],
        $payload
    );

    return back()->with('success', 'Security service data saved successfully!');
}

    public function uploadTimeFile(Request $request, $versionId)
    {
        $version = Version::with('project.customer')->findOrFail($versionId);

        $request->validate([
            'ref_file' => 'required|file|max:51200|mimes:pdf,csv,txt,xlsx,xls,doc,docx,ppt,pptx,png,jpg,jpeg,webp'
        ], [
            'ref_file.mimes' => 'Fail mesti jenis: PDF/CSV/TXT/Excel/Word/PPT/Images.',
            'ref_file.max'   => 'Saiz fail maksimum 50MB.'
        ]);

        $f         = $request->file('ref_file');
        $ext       = strtolower($f->getClientOriginalExtension());
        $mime      = $f->getMimeType();
        $original  = $f->getClientOriginalName();
        $dir       = "security_service/{$versionId}";
        $stored    = $f->store($dir, 'public');

        SecurityServiceFile::create([
            'version_id'    => $version->id,
            'project_id'    => $version->project_id,
            'customer_id'   => $version->project->customer_id ?? null,
            'original_name' => $original,
            'stored_path'   => $stored,
            'mime_type'     => $mime,
            'ext'           => $ext,
            'size_bytes'    => $f->getSize(),
        ]);

        return back()->with('success', 'File successfully uploaded.');
    }

    public function deleteTimeFile($versionId, SecurityServiceFile $file)
    {
        if ($file->version_id !== $versionId) abort(404);

        Storage::disk('public')->delete($file->stored_path);
        $file->delete();

        return back()->with('success', 'File successfully deleted.');
    }

    public function downloadTimeFile($versionId, SecurityServiceFile $file)
    {
        if ($file->version_id !== $versionId) abort(404);

        return Storage::disk('public')->download($file->stored_path, $file->original_name);
    }
}


