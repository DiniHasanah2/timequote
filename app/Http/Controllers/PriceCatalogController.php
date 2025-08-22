<?php

namespace App\Http\Controllers;

use App\Models\PriceCatalog;
use App\Models\ServicePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PriceCatalogController extends Controller
{
    public function store(Request $request)
{
    $data = $request->validate([
        'version_name'      => 'required|string|max:100|unique:price_catalogs,version_name',
        'effective_from'    => 'nullable|date',
        'effective_to'      => 'nullable|date',
        'notes'             => 'nullable|string',
        'source_catalog_id' => 'nullable|uuid',
        'adjust_percent'    => 'nullable|numeric',
        'make_current'      => 'nullable|boolean',
    ]);

    $catalogId = (string) Str::uuid();

    DB::transaction(function () use ($data, $catalogId) {
        /** @var \App\Models\PriceCatalog $new */
        $new = PriceCatalog::create([
            'id'             => $catalogId,
             'version_code'   => $data['version_name'], 
            'version_name'   => $data['version_name'],
            'effective_from' => $data['effective_from'] ?? null,
            'effective_to'   => $data['effective_to'] ?? null,
            'is_current'     => false,
            'notes'          => $data['notes'] ?? null,
        ]);

        if (!empty($data['source_catalog_id'])) {
            $adjust = isset($data['adjust_percent']) ? (float)$data['adjust_percent'] : 0.0;

            ServicePrice::where('price_catalog_id', $data['source_catalog_id'])
                ->orderBy('id')
                ->chunk(500, function ($chunk) use ($new, $adjust) {
                    $rows = [];
                    foreach ($chunk as $sp) {
                        $rows[] = [
                            'id'                       => (string) Str::uuid(),
                            'price_catalog_id'         => $new->id,
                            'service_id'               => $sp->service_id,
                            'price_per_unit'           => $this->applyAdjust($sp->price_per_unit, $adjust),
                            'rate_card_price_per_unit' => $this->applyAdjust($sp->rate_card_price_per_unit, $adjust),
                            'transfer_price_per_unit'  => $this->applyAdjust($sp->transfer_price_per_unit, $adjust),
                            'currency'                 => $sp->currency ?? 'MYR',
                            'created_at'               => now(),
                            'updated_at'               => now(),
                        ];
                    }
                    if (!empty($rows)) {
                        DB::table('service_prices')->insert($rows);
                    }
                });
        }

        if (!empty($data['make_current'])) {
            $new->makeCurrent();
        }
    });

    return redirect()
        ->route('services.index', ['catalog' => $catalogId])
        ->with('success', 'New price version created.');
}

    public function makeCurrent(PriceCatalog $catalog)
    {
        $catalog->makeCurrent();
        return back()->with('success', 'Set as current version.');
    }

    private function applyAdjust($value, float $percent)
    {
        $value = (float) $value;
        if ($percent === 0.0) return round($value, 4);
        $mult = 1 + ($percent / 100.0);
        return round($value * $mult, 4);
    }
}
