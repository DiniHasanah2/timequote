<?php

namespace App\Services;

use App\Models\PriceCatalog;
use App\Models\Service;

class PricingConfigWriter
{
    public static function write(?string $catalogId = null): void
    {
        $catalog = $catalogId
            ? PriceCatalog::find($catalogId)
            : PriceCatalog::where('is_current', true)->first();

        if (!$catalog) {
            // fallback: legacy (from Service master)
            $services = Service::all();
            $pricingData = [];
            foreach ($services as $s) {
                $pricingData[$s->code] = [
                    'category_name'              => $s->category_name,
                    'category_code'              => $s->category_code,
                    'name'                       => $s->name,
                    'measurement_unit'           => $s->measurement_unit,
                    'description'                => $s->description,
                    'price_per_unit'             => (float) $s->price_per_unit,
                    'rate_card_price_per_unit'   => (float) $s->rate_card_price_per_unit,
                    'transfer_price_per_unit'    => (float) $s->transfer_price_per_unit,
                ];
            }
        } else {
            // join Service + ServicePrice for this catalog
            $services = Service::query()
                ->select([
                    'services.*',
                    'sp.price_per_unit',
                    'sp.rate_card_price_per_unit',
                    'sp.transfer_price_per_unit',
                ])
                ->leftJoin('service_prices as sp', function ($join) use ($catalog) {
                    $join->on('sp.service_id', '=', 'services.id')
                        ->where('sp.price_catalog_id', '=', $catalog->id);
                })
                ->get();

            $pricingData = [];
            foreach ($services as $s) {
                $pricingData[$s->code] = [
                    'category_name'              => $s->category_name,
                    'category_code'              => $s->category_code,
                    'name'                       => $s->name,
                    'measurement_unit'           => $s->measurement_unit,
                    'description'                => $s->description,
                    'price_per_unit'             => (float) ($s->price_per_unit ?? 0),
                    'rate_card_price_per_unit'   => (float) ($s->rate_card_price_per_unit ?? 0),
                    'transfer_price_per_unit'    => (float) ($s->transfer_price_per_unit ?? 0),
                ];
            }
        }

        // Meta
        $pricingData['_catalog'] = [
            'id'             => optional($catalog)->id,
            'version_code'   => optional($catalog)->version_code ?? 'legacy',
            'version_name'   => optional($catalog)->version_name ?? 'legacy',
            'effective_from' => optional($catalog)->effective_from,
            'effective_to'   => optional($catalog)->effective_to,
            'is_current'     => (bool) (optional($catalog)->is_current ?? true),
            'generated_at'   => now()->toDateTimeString(),
        ];

        $content = "<?php\n\nreturn " . var_export($pricingData, true) . ";\n";
        file_put_contents(config_path('pricing.php'), $content);
    }
}
