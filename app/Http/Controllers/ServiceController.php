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
        'version_code'   => 'v1.0.0',
        'version_name'   => 'v1.0.0',
        'effective_from' => now()->toDateString(),
        'is_current'     => true,
        'notes'          => 'Initial Catalog',
    ]);
}


    public function index(Request $request)
    {
        $catalog = $this->getActiveCatalog($request);

        // dropdowns
        $categories = Category::all();
        $allCategories = Service::select('category_name')->distinct()->pluck('category_name');
        $allServiceCode = Service::select('code')->distinct()->pluck('code');

        // base query join ke service_prices ikut catalog aktif
        $query = Service::query()
            ->select([
                'services.*',
                'sp.price_per_unit as v_price_per_unit',
                'sp.rate_card_price_per_unit as v_rate_card_price_per_unit',
                'sp.transfer_price_per_unit as v_transfer_price_per_unit',
            ])
            ->leftJoin('service_prices as sp', function ($join) use ($catalog) {
                $join->on('sp.service_id', '=', 'services.id')
                     ->where('sp.price_catalog_id', '=', $catalog->id);
            });

        // FILTERS
        if ($request->filled('category')) {
            $query->where('services.category_name', $request->category);
        }
        if ($request->filled('code')) {
            $query->where('services.code', $request->code);
        }

        // SORT
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('services.name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('services.name', 'desc');
                break;
            case 'price_low_high':
                $query->orderBy('sp.price_per_unit', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('sp.price_per_unit', 'desc');
                break;
            default:
                $query->orderBy('services.category_name', 'asc');
        }

        $services = $query->get();

     $catalogs = PriceCatalog::orderByDesc('effective_from')->orderByDesc('created_at')->get();
$currentCatalog = PriceCatalog::where('is_current', true)->first();
$previousCatalog = PriceCatalog::where('id', '!=', optional($currentCatalog)->id)
                        ->orderByDesc('effective_from')->orderByDesc('created_at')->first();


        return view('products.service.index', compact(
            'services',
            'allCategories',
            'allServiceCode',
            'categories',
            'catalogs',
            'catalog',
            'currentCatalog',
            'previousCatalog'
        ));
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

        $this->updatePricingConfig($this->getActiveCatalog($request)->id);

        return redirect()->route('services.index', ['catalog' => $catalog->id])->with('success', 'Service added successfully.');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $categories = Category::all();

        return view('products.service.edit', compact('service', 'categories'));
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

        DB::transaction(function () use ($request, $id, $catalog) {
            $service = Service::findOrFail($id);

            // track perubahan (gabung service + harga versi)
            $originalValues = $service->toArray();
            $changes = [];

            $fieldsToTrack = [
                'measurement_unit',
            ];

            foreach ($fieldsToTrack as $field) {
                if ($request->has($field) && ($originalValues[$field] ?? null) != $request->$field) {
                    $changes[$field] = [
                        'old' => $originalValues[$field] ?? null,
                        'new' => $request->$field
                    ];
                }
            }

            // update service master (termasuk harga lama utk compat)
            $service->update([
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

            // update/insert harga dalam catalog aktif
            $sp = ServicePrice::firstOrNew([
                'price_catalog_id' => $catalog->id,
                'service_id' => $service->id,
            ]);

            // log perubahan harga versi
            $priceFields = ['price_per_unit','rate_card_price_per_unit','transfer_price_per_unit'];
            foreach ($priceFields as $pf) {
                $old = $sp->exists ? $sp->$pf : null;
                $new = $request->$pf;
                if ($old !== null && (float)$old != (float)$new) {
                    $changes[$pf] = ['old' => $old, 'new' => $new];
                }
                $sp->$pf = $new;
            }
            $sp->currency = 'MYR';
            $sp->save();

            if (!empty($changes)) {
                \App\Services\ServiceAuditService::logChanges($service->id, $changes);
            }
        });

        $this->updatePricingConfig($catalog->id);

        return redirect()->route('services.index', ['catalog' => $catalog->id])->with('success', 'Service updated successfully.');
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

    // regenerate pricing.php ikut catalog aktif
    private function updatePricingConfig(?string $catalogId = null)
    {
        $catalog = $catalogId
            ? PriceCatalog::find($catalogId)
            : PriceCatalog::where('is_current', true)->first();

        // fallback: kalau tiada catalog, guna table lama (behavior asal)
        if (!$catalog) {
            $services = Service::all();
            $pricingData = [];
            foreach ($services as $service) {
                $pricingData[$service->code] = [
                    'category_name' => $service->category_name,
                    'category_code' => $service->category_code,
                    'name' => $service->name,
                    'measurement_unit' => $service->measurement_unit,
                    'description' => $service->description,
                    'price_per_unit' => $service->price_per_unit,
                    'rate_card_price_per_unit' => $service->rate_card_price_per_unit,
                    'transfer_price_per_unit' => $service->transfer_price_per_unit,
                ];
            }
        } else {
            // join ikut catalog
            $services = Service::query()
                ->select([
                    'services.*',
                    'sp.price_per_unit',
                    'sp.rate_card_price_per_unit',
                    'sp.transfer_price_per_unit'
                ])
                ->leftJoin('service_prices as sp', function ($join) use ($catalog) {
                    $join->on('sp.service_id', '=', 'services.id')
                         ->where('sp.price_catalog_id', '=', $catalog->id);
                })
                ->get();

            $pricingData = [];
            foreach ($services as $service) {
                $pricingData[$service->code] = [
                    'category_name' => $service->category_name,
                    'category_code' => $service->category_code,
                    'name' => $service->name,
                    'measurement_unit' => $service->measurement_unit,
                    'description' => $service->description,
                    'price_per_unit' => (float)($service->price_per_unit ?? 0),
                    'rate_card_price_per_unit' => (float)($service->rate_card_price_per_unit ?? 0),
                    'transfer_price_per_unit' => (float)($service->transfer_price_per_unit ?? 0),
                ];
            }
        }

        $configPath = config_path('pricing.php');
        $content = "<?php\n\nreturn " . var_export($pricingData, true) . ";\n";
        file_put_contents($configPath, $content);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

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
}


/*namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service; 
use App\Models\Category;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;


class ServiceController extends Controller
{

public function index(Request $request)
{
    $query = Service::query();
    $categories = Category::all();


    // ✅ FILTER
    if ($request->filled('category')) {
        $query->where('category_name', $request->category);
    }

    // ✅ FILTER by service code
    if ($request->filled('code')) {
        $query->where('code', $request->code);
    }

    // ✅ SORT
    switch ($request->sort) {
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'price_low_high':
            $query->orderBy('price_per_unit', 'asc');
            break;
        case 'price_high_low':
            $query->orderBy('price_per_unit', 'desc');
            break;
        default:
            $query->orderBy('category_name', 'asc'); // default sort
    }

    $services = $query->get();

    // 🔁 Senarai kategori untuk filter dropdown
    $allCategories = Service::select('category_name')->distinct()->pluck('category_name');
      $allServiceCode = Service::select('code')->distinct()->pluck('code');

    return view('products.service.index', compact('services', 'allCategories', 'allServiceCode','categories'));

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
            'description' => 'required|string',
            'price_per_unit' => 'required|numeric',
            'rate_card_price_per_unit' => 'required|numeric',
        ]);

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
        ]);

        $this->updatePricingConfig();

        return redirect()->route('services.index')->with('success', 'Service added successfully.');
    }

public function edit($id)
{
    $service = Service::findOrFail($id);
    $categories = Category::all();

    return view('products.service.edit', compact('service', 'categories'));
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

        $service = Service::findOrFail($id);
        
        // Get original values before update
        $originalValues = $service->toArray();
        
        // Track changes for specific fields
        $changes = [];
        
        $fieldsToTrack = [
            'measurement_unit',
            'price_per_unit',
            'rate_card_price_per_unit',
            'transfer_price_per_unit'
        ];
        
        foreach ($fieldsToTrack as $field) {
            if ($request->has($field) && $originalValues[$field] != $request->$field) {
                $changes[$field] = [
                    'old' => $originalValues[$field],
                    'new' => $request->$field
                ];
            }
        }

        // Update the service
        $service->update([
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

        // Log the changes if any
        if (!empty($changes)) {
            \App\Services\ServiceAuditService::logChanges($service->id, $changes);
        }

        $this->updatePricingConfig();

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file'), 'r');
    $header = fgetcsv($file); // skip header


         while (($row = fgetcsv($file)) !== false) {
        // Cari category ID berdasarkan nama (atau boleh guna code)
        $category = Category::where('name', $row[0])->first();

        // Skip kalau tak jumpa category
        if (!$category) {
            continue;
        }


        Service::create([
             'id' => (string) Str::uuid(), 
    'category_id' => $category->id, 
            'category_name' => $row[0],
            'category_code' => $row[1],
            'code' => $row[2],
            'name' => $row[3],
            'measurement_unit' => $row[4],
            'description' => $row[5],
            'price_per_unit' => $row[6],
            'rate_card_price_per_unit' => $row[7],
            'transfer_price_per_unit' => $row[8],

        ]);
    }

    fclose($file);

    return redirect()->back()->with('success', 'Services imported successfully.');
}


public function export()
{
    $services = \App\Models\Service::all();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="services_export.csv"',
    ];

    $callback = function () use ($services) {
        $handle = fopen('php://output', 'w');

        // Header baris pertama
        fputcsv($handle, ['ID', 'Category Name', 'Category Code', 'Name', 'Code', 'Measurement Unit', 'Description', 'Price Per Unit (RM)', 'Rate Card Price Per Unit (RM)', 'Transfer Price Per Unit (RM)']);

        // Data baris seterusnya
        foreach ($services as $service) {
            fputcsv($handle, [
                $service->id,
                $service->category_name,
                $service->category_code,
                $service->name,
                $service->code,
                $service->measurement_unit,
                $service->description,
                $service->price_per_unit,
                $service->rate_card_price_per_unit,
                $service->transfer_price_per_unit,
            ]);
        }

        fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
}

    private function updatePricingConfig()
    {
        $services = Service::all();

        $pricingData = [];

        foreach ($services as $service) {
            $pricingData[$service->code] = [
                'category_name' => $service->category_name,
                'category_code' => $service->category_code,
                'name' => $service->name,
                'measurement_unit' => $service->measurement_unit,
                'description' => $service->description,
                'price_per_unit' => $service->price_per_unit,
                'rate_card_price_per_unit' => $service->rate_card_price_per_unit,
                'transfer_price_per_unit' => $service->transfer_price_per_unit,
            ];
        }

        $configPath = config_path('pricing.php');

        $content = "<?php\n\nreturn " . var_export($pricingData, true) . ";\n";

        file_put_contents($configPath, $content);
    }


    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        // regenerate pricing.php selepas delete
        $this->updatePricingConfig();

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }

    public function auditLogs($id)
    {
        $service = Service::findOrFail($id);
        $logs = \App\Services\ServiceAuditService::getServiceLogs($service->id);
        
        return view('products.service.audit-logs', compact('service', 'logs'));
    }

}*/

