<?php

namespace App\Http\Controllers;

use App\Models\PriceCatalog;
use App\Models\ServicePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Services\PricingConfigWriter;


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
        'adjust_percent_price'           => 'nullable|numeric',
        'adjust_percent_rate_card'       => 'nullable|numeric',
        'adjust_percent_transfer'        => 'nullable|numeric',
        'make_current'                   => 'nullable|boolean',
       
       
        
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
    $adjPrice    = isset($data['adjust_percent_price']) ? (float)$data['adjust_percent_price'] : 0.0;
    $adjRate     = isset($data['adjust_percent_rate_card']) ? (float)$data['adjust_percent_rate_card'] : 0.0;
    $adjTransfer = isset($data['adjust_percent_transfer']) ? (float)$data['adjust_percent_transfer'] : 0.0;

    ServicePrice::where('price_catalog_id', $data['source_catalog_id'])
        ->orderBy('id')
        ->chunk(500, function ($chunk) use ($new, $adjPrice, $adjRate, $adjTransfer) {
            $rows = [];
            foreach ($chunk as $sp) {
                $rows[] = [
                    'id'                       => (string) \Illuminate\Support\Str::uuid(),
                    'price_catalog_id'         => $new->id,
                    'service_id'               => $sp->service_id,
                    'price_per_unit'           => $this->applyAdjust($sp->price_per_unit, $adjPrice),
                    'rate_card_price_per_unit' => $this->applyAdjust($sp->rate_card_price_per_unit, $adjRate),
                    'transfer_price_per_unit'  => $this->applyAdjust($sp->transfer_price_per_unit, $adjTransfer),
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




    public function commit(Request $request, PriceCatalog $catalog)
{
    // Force a typed confirmation to avoid accidents
    $data = $request->validate([
        'confirm' => 'required|string|in:COMMIT',
    ]);

    DB::transaction(function () use ($catalog) {
        // 1) publish prices from this catalog → Service main
        \App\Models\ServicePrice::where('price_catalog_id', $catalog->id)
            ->orderBy('service_id')
            ->chunk(500, function ($chunk) {
                foreach ($chunk as $sp) {
                    /** @var Service $svc */
                    $svc = Service::find($sp->service_id);
                    if (!$svc) continue;

                    $changes = [];
                    $fields = [
                        'price_per_unit'             => 'price_per_unit',
                        'rate_card_price_per_unit'   => 'rate_card_price_per_unit',
                        'transfer_price_per_unit'    => 'transfer_price_per_unit',
                    ];

                    foreach ($fields as $svcField => $spField) {
                        $old = (float)($svc->$svcField ?? 0);
                        $new = (float)$sp->$spField;
                        if (round($old,4) !== round($new,4)) {
                            $changes[$svcField] = ['old' => $old, 'new' => $new];
                            $svc->$svcField = $new;
                        }
                    }

                    if (!empty($changes) && class_exists(\App\Services\ServiceAuditService::class)) {
                        \App\Services\ServiceAuditService::logChanges($svc->id, $changes);
                    }

                    $svc->save();
                }
            });

        // 2) set this catalog as current (ends old current)
        $catalog->makeCurrent();
      
$catalog->committed_at = now();
$catalog->committed_by = optional(auth()->user())->id;
$catalog->save();


        // 3) regenerate config/pricing.php using this catalog
        PricingConfigWriter::write($catalog->id);
    });

    return redirect()
        ->route('services.index', ['catalog' => $catalog->id])
        ->with('success', "Committed and published prices from {$catalog->version_name}. Now set as current.");
       
}

}
