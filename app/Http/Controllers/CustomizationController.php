<?php

namespace App\Http\Controllers;

use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CustomizationController extends Controller
{
    public function show($versionId)
    {
        
        $resp = app(InternalSummaryController::class)->index($versionId);

        if ($resp instanceof RedirectResponse) {
            return $resp;
        }

        if ($resp instanceof View) {
            $data = $resp->getData();

            /** @var \App\Models\Version $version */
            $version = $data['version'] ?? Version::with(['project.customer','region','security_service'])->findOrFail($versionId);

          
            $servicesList = ['Managed Operating System','Managed Backup and Restore','Managed Patching','Managed DR'];

            $data += [
                'project'           => $data['project']           ?? $version->project,
                'region'            => $data['region']            ?? ($version->region ?? null),
                'security_service'  => $data['security_service']  ?? ($version->security_service ?? null),
                'licenseSummary'    => $data['licenseSummary']    ?? [],
                'usedFlavours'      => $data['usedFlavours']      ?? collect(),
                'flavourDetails'    => $data['flavourDetails']    ?? collect(),
                'drCountsKL'        => $data['drCountsKL']        ?? [],
                'drCountsCJ'        => $data['drCountsCJ']        ?? [],
                'klEvsDR'           => $data['klEvsDR']           ?? 0,
                'cyberEvsDR'        => $data['cyberEvsDR']        ?? 0,
                'nonStandardItems'  => $data['nonStandardItems']  ?? collect(),
                'notes'             => $data['notes']             ?? [],
                'summary'           => $data['summary']           ?? null,
            ];

          
            $data['klManagedServices']    = $data['klManagedServices']    ?? array_fill_keys($servicesList, 0);
            $data['cyberManagedServices'] = $data['cyberManagedServices'] ?? array_fill_keys($servicesList, 0);

          
            $custom = is_array($version->customization ?? null)
                ? $version->customization
                : (json_decode($version->customization ?? '[]', true) ?: []);

            $data['custom'] = $custom;
            $data['customization_mode'] = true;

            return view('projects.security_service.customization', $data);
        }

        return $resp;
    }

    public function save($versionId, Request $request)
    {
        $validated = $request->validate([
            'custom'   => ['nullable','array'],
            'custom.*' => ['nullable','string','max:255'],
        ]);

        $version  = Version::findOrFail($versionId);

       
        $incoming = array_filter($validated['custom'] ?? [], function ($v) {
            return !(is_null($v) || (is_string($v) && trim($v) === ''));
        });

        $existing = is_array($version->customization ?? null)
            ? $version->customization
            : (json_decode($version->customization ?? '[]', true) ?: []);

        $version->customization = array_replace($existing, $incoming);
        $version->save();

        return back()->with('status', 'Customization saved.');
    }
}
