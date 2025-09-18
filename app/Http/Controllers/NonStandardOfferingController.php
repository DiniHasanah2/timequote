<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Models\Category;
use App\Models\Service;
use App\Models\NonStandardOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NonStandardOfferingController extends Controller
{
    public function store(Request $request, $versionId)
    {
        $version = Version::with('project.customer')->findOrFail($versionId);

        $data = $request->validate([
            'category_id'                  => 'nullable|uuid|exists:categories,id',
            'service_id'                   => 'nullable|uuid|exists:services,id',
            'unit'                         => 'nullable|string|max:64',
            'quantity'                     => 'required|integer|min:1',
            'months'                       => 'required|integer|min:1',
            'unit_price_per_month'         => 'required|numeric|min:0',
            'mark_up'                      => 'nullable|numeric|min:0',
            'source_non_standard_item_id'  => 'nullable|uuid|exists:non_standard_items,id',
        ]);

        $cat = $data['category_id'] ? Category::find($data['category_id']) : null;
        $svc = $data['service_id']  ? Service::find($data['service_id'])   : null;

        $base = ($data['quantity'] ?? 1) * ($data['months'] ?? 1) * ($data['unit_price_per_month'] ?? 0);
        $selling = $base * (1 + (($data['mark_up'] ?? 0)/100));

        NonStandardOffering::create([
            'id'                     => (string) Str::uuid(),
            'project_id'             => $version->project_id,
            'version_id'             => $version->id,
            'customer_id'            => $version->project->customer_id,
            'presale_id'             => $version->project->presale_id,

            'category_id'            => $cat->id   ?? null,
            'category_name'          => $cat->name ?? null,
            'category_code'          => $cat->category_code ?? null,

            'service_id'             => $svc->id   ?? null,
            'service_name'           => $svc->name ?? null,
            'service_code'           => $svc->code ?? null,

            'unit'                   => $data['unit'] ?? ($svc->measurement_unit ?? 'Unit'),
            'quantity'               => $data['quantity'],
            'months'                 => $data['months'],
            'unit_price_per_month'   => $data['unit_price_per_month'],
            'mark_up'                => $data['mark_up'] ?? 0,
            'selling_price'          => round($selling, 2),

            'source_non_standard_item_id' => $data['source_non_standard_item_id'] ?? null,
        ]);

        return back()->with('success', 'Non-Standard offering saved.');
    }

    public function edit($versionId, NonStandardOffering $offering)
    {
        abort_unless($offering->version_id === $versionId, 404);
        $categories = Category::orderBy('name')->get();
        $services   = Service::orderBy('name')->get();

        return view('projects.non_standard_items.offering_edit', compact('offering','categories','services','versionId'));
    }

    public function update(Request $request, $versionId, NonStandardOffering $offering)
    {
        abort_unless($offering->version_id === $versionId, 404);

        $data = $request->validate([
            'category_id'           => 'nullable|uuid|exists:categories,id',
            'service_id'            => 'nullable|uuid|exists:services,id',
            'unit'                  => 'nullable|string|max:64',
            'quantity'              => 'required|integer|min:1',
            'months'                => 'required|integer|min:1',
            'unit_price_per_month'  => 'required|numeric|min:0',
            'mark_up'               => 'nullable|numeric|min:0',
        ]);

        $cat = $data['category_id'] ? Category::find($data['category_id']) : null;
        $svc = $data['service_id']  ? Service::find($data['service_id'])   : null;

        $base = ($data['quantity'] ?? 1) * ($data['months'] ?? 1) * ($data['unit_price_per_month'] ?? 0);
        $selling = $base * (1 + (($data['mark_up'] ?? 0)/100));

        $offering->update([
            'category_id'            => $cat->id   ?? null,
            'category_name'          => $cat->name ?? null,
            'category_code'          => $cat->category_code ?? null,
            'service_id'             => $svc->id   ?? null,
            'service_name'           => $svc->name ?? null,
            'service_code'           => $svc->code ?? null,
            'unit'                   => $data['unit'] ?? ($svc->measurement_unit ?? 'Unit'),
            'quantity'               => $data['quantity'],
            'months'                 => $data['months'],
            'unit_price_per_month'   => $data['unit_price_per_month'],
            'mark_up'                => $data['mark_up'] ?? 0,
            'selling_price'          => round($selling, 2),
        ]);

        return redirect()->route('versions.non_standard_items.create', $versionId)->with('success','Offering updated.');
    }

    public function destroy($versionId, NonStandardOffering $offering)
    {
        abort_unless($offering->version_id === $versionId, 404);
        $offering->delete();
        return back()->with('success', 'Offering deleted.');
    }
}
