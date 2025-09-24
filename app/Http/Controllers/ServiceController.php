<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\PriceCatalog;
use App\Models\ServicePrice;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Services\PricingConfigWriter;



class ServiceController extends Controller
{
    protected function getActiveCatalog(Request $request): PriceCatalog
{
    if ($request->filled('catalog')) {
        $cat = PriceCatalog::find($request->catalog);
        if ($cat) return $cat;
    }
    // cari guna ?version=... tapi ikut version_name
    if ($request->filled('version')) {
        $cat = PriceCatalog::where('version_name', $request->version)->first();
        if ($cat) return $cat;
    }
    $current = PriceCatalog::where('is_current', true)->first();
    if ($current) return $current;

    // auto-buat default
    return PriceCatalog::create([
        'id'             => (string) Str::uuid(),
        'version_code'   => 'v(testing)',
        'version_name'   => 'v(testing)',
        'effective_from' => now()->toDateString(),
        'is_current'     => true,
        'notes'          => 'Initial Catalog',
    ]);
}

public function index(Request $request)
{
    
    if ($request->boolean('reset')) {
        session()->forget('services.filters');
    }

    $keys = ['category','code','sort','catalog'];

    
    if (!$request->hasAny($keys) && session()->has('services.filters')) {
        foreach (session('services.filters') as $k => $v) {
            if ($v !== null && $v !== '') {
                $request->merge([$k => $v]);
            }
        }
    }

   
    session(['services.filters' => $request->only($keys)]);
    // ===== TAMAT BLOK TAMBAHAN =====

    // ========== Which catalog are we "viewing" ==========
    $viewCatalog = $this->getActiveCatalog($request); // you already have this helper

    // ========== Current / Next / Last ==========
    $currentCatalog = PriceCatalog::where('is_current', true)->first();

    $nextCatalog = PriceCatalog::where('is_current', false)
        ->whereNull('effective_to')
        ->orderByDesc('effective_from')
        ->orderByDesc('created_at')
        ->first();

    $lastCatalog = PriceCatalog::where('is_current', false)
        ->whereNotNull('effective_to')
        ->where('id', '!=', optional($currentCatalog)->id)
        ->where('id', '!=', optional($nextCatalog)->id)
        ->orderByDesc('effective_to')
        ->first();

    // ===== dropdowns =====
    $categories = Category::all();
    $allCategories = Service::select('category_name')->distinct()->pluck('category_name');
    $allServiceCode = Service::select('code')->distinct()->pluck('code');

    // ===== base query join to service_prices using the VIEWING catalog =====
    $query = Service::query()
        ->select([
            'services.*',
            'sp.price_per_unit as v_price_per_unit',
            'sp.rate_card_price_per_unit as v_rate_card_price_per_unit',
            'sp.transfer_price_per_unit as v_transfer_price_per_unit',
        ])
        ->leftJoin('service_prices as sp', function ($join) use ($viewCatalog) {
            $join->on('sp.service_id', '=', 'services.id')
                 ->where('sp.price_catalog_id', '=', $viewCatalog->id);
        });

    // ===== filters =====
    if ($request->filled('category')) {
        $query->where('services.category_name', $request->category);
    }
    if ($request->filled('code')) {
        $query->where('services.code', $request->code);
    }

    // ===== sort =====
    switch ($request->sort) {
        case 'name_asc':
            $query->orderBy('services.name', 'asc'); break;
        case 'name_desc':
            $query->orderBy('services.name', 'desc'); break;
        case 'price_low_high':
            $query->orderByRaw('COALESCE(sp.price_per_unit, services.price_per_unit) ASC'); break;
        case 'price_high_low':
            $query->orderByRaw('COALESCE(sp.price_per_unit, services.price_per_unit) DESC'); break;
        default:
            $query->orderBy('services.category_name', 'asc');
    }

    $services = $query->get();

    // for dropdown list
    $catalogs = PriceCatalog::orderByDesc('effective_from')->orderByDesc('created_at')->get();

    // send to view
    return view('products.service.index', [
        'services'       => $services,
        'allCategories'  => $allCategories,
        'allServiceCode' => $allServiceCode,
        'categories'     => $categories,
        'catalogs'       => $catalogs,
        'catalog'        => $viewCatalog,
        'currentCatalog' => $currentCatalog,
        'nextCatalog'    => $nextCatalog,
        'lastCatalog'    => $lastCatalog,
    ]);
}


    

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|uuid',
            'category_name' => 'required|string',
            'category_code' => 'required|string',
            'code' => 'required|string',
            'name' => 'required|string',
            'measurement_unit' => 'required|string',
            'description' => 'nullable|string',
            'price_per_unit' => 'required|numeric',
            'rate_card_price_per_unit' => 'required|numeric',
            'transfer_price_per_unit' => 'required|numeric',
        ]);

        $catalog = $this->getActiveCatalog($request);

        DB::transaction(function () use ($request, $catalog) {
            // 1) create service master (masih simpan harga lama utk compat)
            $service = Service::create([
                'id' => (string) Str::uuid(),
                'category_id' => $request->category_id,
                'category_name' => $request->category_name,
                'category_code' => $request->category_code,
                'code' => $request->code,
                'name' => $request->name,
                'measurement_unit' => $request->measurement_unit,
                'description' => $request->description,
                'price_per_unit' => $request->price_per_unit,
                'rate_card_price_per_unit' => $request->rate_card_price_per_unit,
                'transfer_price_per_unit' => $request->transfer_price_per_unit,
            ]);

            // 2) create harga untuk catalog aktif
            ServicePrice::create([
                'id' => (string) Str::uuid(),
                'price_catalog_id' => $catalog->id,
                'service_id' => $service->id,
                'price_per_unit' => $request->price_per_unit,
                'rate_card_price_per_unit' => $request->rate_card_price_per_unit,
                'transfer_price_per_unit' => $request->transfer_price_per_unit,
                'currency' => 'MYR',
            ]);
        });

        //$this->updatePricingConfig($this->getActiveCatalog($request)->id);



        $this->updatePricingConfig($catalog->id);


        return redirect()->route('services.index', ['catalog' => $catalog->id])->with('success', 'Service added successfully.');
    }

    /*public function edit($id)
    {
        $service = Service::findOrFail($id);
        $categories = Category::all();

        return view('products.service.edit', compact('service', 'categories'));
    }*/
    public function edit(Request $request, $id)
{
    $catalog = $this->getActiveCatalog($request);

    $service = Service::query()
        ->select([
            'services.*',
            'sp.price_per_unit as v_price_per_unit',
            'sp.rate_card_price_per_unit as v_rate_card_price_per_unit',
            'sp.transfer_price_per_unit as v_transfer_price_per_unit',
        ])
        ->leftJoin('service_prices as sp', function ($join) use ($catalog) {
            $join->on('sp.service_id', '=', 'services.id')
                 ->where('sp.price_catalog_id', '=', $catalog->id);
        })
        ->where('services.id', $id)
        ->firstOrFail();

    $categories = Category::all();

    return view('products.service.edit', compact('service', 'categories', 'catalog'));
}

public function update(Request $request, $id)
{
    \Log::info('UPDATE REQUEST:', $request->all());

    $request->validate([
        'category_id' => 'required|uuid',
        'category_name' => 'required|string',
        'category_code' => 'required|string',
        'code' => 'required|string',
        'name' => 'required|string',
        'measurement_unit' => 'required|string',
        'description' => 'nullable|string',
        'price_per_unit' => 'required|numeric',
        'rate_card_price_per_unit' => 'required|numeric',
        'transfer_price_per_unit' => 'required|numeric',
    ]);

    $catalog = $this->getActiveCatalog($request);
    $locked  = $this->isCatalogLocked($catalog);

    DB::transaction(function () use ($request, $id, $catalog, $locked) {
        $service = Service::findOrFail($id);

        // track perubahan non-price
        $originalValues = $service->toArray();
        $changes = [];

        $fieldsToTrack = ['measurement_unit'];
        foreach ($fieldsToTrack as $field) {
            if ($request->has($field) && ($originalValues[$field] ?? null) != $request->$field) {
                $changes[$field] = [
                    'old' => $originalValues[$field] ?? null,
                    'new' => $request->$field
                ];
            }
        }

        // Sentiasa benarkan update field bukan harga
        $payload = [
            'category_id'      => $request->category_id,
            'category_name'    => $request->category_name,
            'category_code'    => $request->category_code,
            'code'             => $request->code,
            'name'             => $request->name,
            'measurement_unit' => $request->measurement_unit,
            'description'      => $request->description,
        ];

        // Kalau TAK locked, barulah boleh update harga master (jika memang nak kekalkan behavior ni)
        if (!$locked) {
            $payload += [
                'price_per_unit'             => $request->price_per_unit,
                'rate_card_price_per_unit'   => $request->rate_card_price_per_unit,
                'transfer_price_per_unit'    => $request->transfer_price_per_unit,
            ];
        }

        $service->update($payload);

        // Kalau TAK locked, update harga dalam jadual version (service_prices)
        if (!$locked) {
            $sp = ServicePrice::firstOrNew([
                'price_catalog_id' => $catalog->id,
                'service_id'       => $service->id,
            ]);

            foreach (['price_per_unit','rate_card_price_per_unit','transfer_price_per_unit'] as $pf) {
                $old = $sp->exists ? $sp->$pf : null;
                $new = $request->$pf;
                if ($old !== null && (float)$old != (float)$new) {
                    $changes[$pf] = ['old' => $old, 'new' => $new];
                }
                $sp->$pf = $new;
            }
            $sp->currency = 'MYR';
            $sp->save();
        }

        if (!empty($changes)) {
            \App\Services\ServiceAuditService::logChanges($service->id, $changes);
        }
    });

    // Masih ok untuk regenerate config walaupun locked (no harm).
    $this->updatePricingConfig($catalog->id);

    $msg = $locked
        ? 'Saved non-price fields. Prices are locked for this version.'
        : 'Service updated successfully.';

    return redirect()->route('services.index', ['catalog' => $catalog->id])
        ->with('success', $msg);
}








    private function isCatalogLocked(PriceCatalog $catalog): bool
{
    return ($catalog->is_current ?? false) || !is_null($catalog->effective_to);
}


    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $catalog = $this->getActiveCatalog($request);

        $file = fopen($request->file('csv_file'), 'r');
        $header = fgetcsv($file); // skip header

        DB::transaction(function () use ($file, $catalog) {
            while (($row = fgetcsv($file)) !== false) {
                // CSV layout rujuk template sedia ada:
                // [Category Name, Category Code, Name, Code, Measurement Unit, Description, Price, RateCard, Transfer]
                $category = Category::where('name', $row[0])->first();
                if (!$category) {
                    // skip kalau category tak wujud
                    continue;
                }

                // cari/insert service master
                $service = Service::firstOrCreate(
                    [
                        'code' => $row[3],
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'category_id' => $category->id,
                        'category_name' => $row[0],
                        'category_code' => $row[1],
                        'name' => $row[2],
                        'measurement_unit' => $row[4],
                        'description' => $row[5] ?? null,
                        // simpan juga ke table lama utk compat
                        'price_per_unit' => (float)($row[6] ?? 0),
                        'rate_card_price_per_unit' => (float)($row[7] ?? 0),
                        'transfer_price_per_unit' => (float)($row[8] ?? 0),
                    ]
                );

                // update field master jika berubah
                $service->update([
                    'category_id' => $category->id,
                    'category_name' => $row[0],
                    'category_code' => $row[1],
                    'name' => $row[2],
                    'measurement_unit' => $row[4],
                    'description' => $row[5] ?? null,
                    'price_per_unit' => (float)($row[6] ?? 0),
                    'rate_card_price_per_unit' => (float)($row[7] ?? 0),
                    'transfer_price_per_unit' => (float)($row[8] ?? 0),
                ]);

                // insert/update harga untuk catalog aktif
                ServicePrice::updateOrCreate(
                    [
                        'price_catalog_id' => $catalog->id,
                        'service_id' => $service->id,
                    ],
                    [
                        'price_per_unit' => (float)($row[6] ?? 0),
                        'rate_card_price_per_unit' => (float)($row[7] ?? 0),
                        'transfer_price_per_unit' => (float)($row[8] ?? 0),
                        'currency' => 'MYR',
                    ]
                );
            }
        });

        fclose($file);

        $this->updatePricingConfig($catalog->id);

        return redirect()->back()->with('success', 'Services imported successfully.');
    }

    public function export(Request $request)
    {
        $catalog = $this->getActiveCatalog($request);

        // join ikut catalog
        $rows = Service::query()
            ->select([
                'services.id',
                'services.category_name',
                'services.category_code',
                'services.name',
                'services.code',
                'services.measurement_unit',
                'services.description',
                'sp.price_per_unit',
                'sp.rate_card_price_per_unit',
                'sp.transfer_price_per_unit',
            ])
            ->leftJoin('service_prices as sp', function ($join) use ($catalog) {
                $join->on('sp.service_id', '=', 'services.id')
                     ->where('sp.price_catalog_id', '=', $catalog->id);
            })
            ->orderBy('services.category_name')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',

            'Content-Disposition' => 'attachment; filename="services_export_'.$catalog->version_name.'.csv"',

        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // header
            fputcsv($handle, [
                'ID',
                'Category Name',
                'Category Code',
                'Name',
                'Code',
                'Measurement Unit',
                'Description',
                'Price Per Unit (RM)',
                'Rate Card Price Per Unit (RM)',
                'Transfer Price Per Unit (RM)'
            ]);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->category_name,
                    $r->category_code,
                    $r->name,
                    $r->code,
                    $r->measurement_unit,
                    $r->description,
                    number_format((float)$r->price_per_unit, 4, '.', ''),
                    number_format((float)$r->rate_card_price_per_unit, 4, '.', ''),
                    number_format((float)$r->transfer_price_per_unit, 4, '.', ''),
                ]);
            }

            fclose($handle); // (fix: sebelum ni typo fcloe)
        };

        return Response::stream($callback, 200, $headers);
    }

private function updatePricingConfig(?string $catalogId = null)
{
    PricingConfigWriter::write($catalogId);
}



public function bulkPreview(Request $request)
{
    $data = $request->validate([
        'catalog_id'   => 'required|uuid',
        'selected'     => 'required|array|min:1',
        'selected.*'   => 'uuid',
        'pct_price'    => 'nullable|numeric',
        'pct_rate'     => 'nullable|numeric',
        'pct_transfer' => 'nullable|numeric',
    ]);

    $catalog     = \App\Models\PriceCatalog::findOrFail($data['catalog_id']);
    $services    = \App\Models\Service::whereIn('id', $data['selected'])->get();
    $pctPrice    = isset($data['pct_price']) ? (float)$data['pct_price'] : null;
    $pctRate     = isset($data['pct_rate']) ? (float)$data['pct_rate'] : null;
    $pctTransfer = isset($data['pct_transfer']) ? (float)$data['pct_transfer'] : null;

    $applyAdjust = function($value, $pct) {
        $value = (float)$value;
        if ($pct === null) return round($value, 4);
        $mult = 1.0 + ($pct / 100.0);
        return round($value * $mult, 4);
    };

    $rows = [];

    foreach ($services as $service) {
        $sp = \App\Models\ServicePrice::where([
            'price_catalog_id' => $catalog->id,
            'service_id'       => $service->id,
        ])->first();

      
        $old_ppu  = (float)($sp->price_per_unit           ?? $service->price_per_unit           ?? 0);
        $old_rcpu = (float)($sp->rate_card_price_per_unit ?? $service->rate_card_price_per_unit ?? 0);
        $old_tpu  = (float)($sp->transfer_price_per_unit  ?? $service->transfer_price_per_unit  ?? 0);

        $new_ppu  = $applyAdjust($old_ppu,  $pctPrice);
        $new_rcpu = $applyAdjust($old_rcpu, $pctRate);
        $new_tpu  = $applyAdjust($old_tpu,  $pctTransfer);

        $rows[] = [
            'id'    => $service->id,
            'code'  => $service->code,
            'name'  => $service->name,
            'old'   => ['ppu'=>$old_ppu, 'rcpu'=>$old_rcpu, 'tpu'=>$old_tpu],
            'new'   => ['ppu'=>$new_ppu, 'rcpu'=>$new_rcpu, 'tpu'=>$new_tpu],
            'delta' => [
                'ppu'  => round($new_ppu  - $old_ppu,  4),
                'rcpu' => round($new_rcpu - $old_rcpu, 4),
                'tpu'  => round($new_tpu  - $old_tpu,  4),
            ],
        ];
    }

    return response()->json([
        'catalog'   => ['id'=>$catalog->id, 'version_name'=>$catalog->version_name],
        'count'     => count($rows),
        'items'     => $rows,
        'pct'       => ['price'=>$pctPrice, 'rate'=>$pctRate, 'transfer'=>$pctTransfer],
    ]);
}












    public function destroy($id)
{
    $service = Service::findOrFail($id);

    DB::transaction(function () use ($service) {
        // padam harga versi-versa yang berkait
        ServicePrice::where('service_id', $service->id)->delete();
        // padam master
        $service->delete();
    });

    // regenerate pricing.php (ikut catalog current)
    $current = PriceCatalog::where('is_current', true)->first();
    $this->updatePricingConfig(optional($current)->id);

    return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
}


  

    public function auditLogs($id)
    {
        $service = Service::findOrFail($id);
        $logs = \App\Services\ServiceAuditService::getServiceLogs($service->id);

        return view('products.service.audit-logs', compact('service', 'logs'));
    }

    public function bulkLog(Request $request)
{
    $data = $request->validate([
        'catalog_id' => 'required|uuid',
        'selected'   => 'required|array|min:1',
        'selected.*' => 'uuid',
    ]);

    $catalog  = \App\Models\PriceCatalog::findOrFail($data['catalog_id']);
    if ($this->isCatalogLocked($catalog)) {
    return redirect()->back()->with('error', 'This version is locked (current/ended). Create a new version to log prices.');
}

    $services = \App\Models\Service::whereIn('id', $data['selected'])->get();

    $updated = 0;

    DB::transaction(function () use ($services, $catalog, &$updated) {
        foreach ($services as $service) {
            // log harga dari master(service) → version yang dipilih
            $values = [
                'price_per_unit'           => (float)$service->price_per_unit,
                'rate_card_price_per_unit' => (float)$service->rate_card_price_per_unit,
                'transfer_price_per_unit'  => (float)$service->transfer_price_per_unit,
                'currency'                 => 'MYR',
            ];

            $sp = \App\Models\ServicePrice::firstOrNew([
                'price_catalog_id' => $catalog->id,
                'service_id'       => $service->id,
            ]);

            $changes = [];
            foreach (['price_per_unit','rate_card_price_per_unit','transfer_price_per_unit'] as $f) {
                $old = $sp->exists ? (float)$sp->$f : null;
                $new = $values[$f];
                if ($old === null || round($old,4) !== round($new,4)) {
                    $changes[$f] = ['old' => $old, 'new' => $new];
                }
                $sp->$f = $new;
            }
            $sp->currency = 'MYR';
            $sp->save();

            if (!empty($changes) && class_exists(\App\Services\ServiceAuditService::class)) {
                \App\Services\ServiceAuditService::logChanges($service->id, $changes);
            }

            $updated++;
        }
    });

    $this->updatePricingConfig($catalog->id);

    return redirect()->route('services.index', ['catalog' => $catalog->id])
        ->with('success', "Logged $updated item(s) to version {$catalog->version_name}.");
}

public function bulkAdjust(Request $request)
{
    $data = $request->validate([
        'catalog_id'   => 'required|uuid',
        'selected'     => 'required|array|min:1',
        'selected.*'   => 'uuid',
        'pct_price'    => 'nullable|numeric',
        'pct_rate'     => 'nullable|numeric',
        'pct_transfer' => 'nullable|numeric',
    ]);

    $catalog     = \App\Models\PriceCatalog::findOrFail($data['catalog_id']);
    if ($this->isCatalogLocked($catalog)) {
    return redirect()->back()->with('error', 'This version is locked (current/ended). Create a new version to adjust prices.');
}

    $services    = \App\Models\Service::whereIn('id', $data['selected'])->get();
    $pctPrice    = isset($data['pct_price']) ? (float)$data['pct_price'] : null;
    $pctRate     = isset($data['pct_rate']) ? (float)$data['pct_rate'] : null;
    $pctTransfer = isset($data['pct_transfer']) ? (float)$data['pct_transfer'] : null;

    $applyAdjust = function($value, $pct) {
        $value = (float)$value;
        if ($pct === null) return round($value, 4);
        $mult = 1.0 + ($pct / 100.0);
        return round($value * $mult, 4);
    };

    $count = 0;

    DB::transaction(function () use ($services, $catalog, $pctPrice, $pctRate, $pctTransfer, $applyAdjust, &$count) {
        foreach ($services as $service) {
            $sp = \App\Models\ServicePrice::firstOrNew([
                'price_catalog_id' => $catalog->id,
                'service_id'       => $service->id,
            ]);

            // seed dari master kalau row baru
            if (!$sp->exists) {
                $sp->price_per_unit           = (float)$service->price_per_unit;
                $sp->rate_card_price_per_unit = (float)$service->rate_card_price_per_unit;
                $sp->transfer_price_per_unit  = (float)$service->transfer_price_per_unit;
                $sp->currency                 = 'MYR';
            }

            $changes = [];

            $old = (float)$sp->price_per_unit;
            $new = $applyAdjust($old, $pctPrice);
            if (round($old,4) !== round($new,4)) {
                $changes['price_per_unit'] = ['old' => $old, 'new' => $new];
                $sp->price_per_unit = $new;
            }

            $old = (float)$sp->rate_card_price_per_unit;
            $new = $applyAdjust($old, $pctRate);
            if (round($old,4) !== round($new,4)) {
                $changes['rate_card_price_per_unit'] = ['old' => $old, 'new' => $new];
                $sp->rate_card_price_per_unit = $new;
            }

            $old = (float)$sp->transfer_price_per_unit;
            $new = $applyAdjust($old, $pctTransfer);
            if (round($old,4) !== round($new,4)) {
                $changes['transfer_price_per_unit'] = ['old' => $old, 'new' => $new];
                $sp->transfer_price_per_unit = $new;
            }

            $sp->currency = 'MYR';
            $sp->save();

            if (!empty($changes) && class_exists(\App\Services\ServiceAuditService::class)) {
                \App\Services\ServiceAuditService::logChanges($service->id, $changes);
            }

            $count++;
        }
    });

    $this->updatePricingConfig($catalog->id);

    return redirect()->route('services.index', ['catalog' => $catalog->id])
        ->with('success', "Adjusted $count item(s) in version {$catalog->version_name}.");
}

public function priceHistory(Service $service)
{
    // Join all versions this service appears in
    $rows = ServicePrice::query()
        ->select([
            'price_catalogs.version_name',
            'price_catalogs.effective_from',
            'service_prices.price_per_unit',
            'service_prices.rate_card_price_per_unit',
            'service_prices.transfer_price_per_unit',
            'service_prices.updated_at',
        ])
        ->join('price_catalogs', 'price_catalogs.id', '=', 'service_prices.price_catalog_id')
        ->where('service_prices.service_id', $service->id)
        ->orderByDesc('price_catalogs.effective_from')
        ->orderByDesc('service_prices.updated_at')
        ->get();

    // compute deltas vs previous row (just for display)
    $withDelta = [];
    $prev = null;
    foreach ($rows as $r) {
        $delta = [
            'ppu'   => $prev ? round((float)$r->price_per_unit - (float)$prev->price_per_unit, 4) : null,
            'rcpu'  => $prev ? round((float)$r->rate_card_price_per_unit - (float)$prev->rate_card_price_per_unit, 4) : null,
            'tpu'   => $prev ? round((float)$r->transfer_price_per_unit - (float)$prev->transfer_price_per_unit, 4) : null,
        ];
        $withDelta[] = ['row' => $r, 'delta' => $delta];
        $prev = $r;
    }

    return view('products.service.price-history', [
        'service'   => $service,
        'versions'  => $withDelta,
    ]);
}


}

