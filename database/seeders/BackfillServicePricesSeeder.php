<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\PriceCatalog;
use App\Models\Service;
use App\Models\ServicePrice;

class BackfillServicePricesSeeder extends Seeder
{
    public function run(): void
    {
      
        $catalog = PriceCatalog::firstOrCreate(
            ['version_code' => 'v3.1.0'],
            [
                'id' => (string) Str::uuid(),
                'title' => 'August 2025 Release',
                'effective_date' => now()->toDateString(),
                'is_current' => true,
                'created_by' => auth()->id() ?? null,
            ]
        );

        // salin harga dari table services
        Service::query()->chunk(500, function ($services) use ($catalog) {
            foreach ($services as $svc) {
                ServicePrice::firstOrCreate(
                    [
                        'price_catalog_id' => $catalog->id,
                        'service_id' => $svc->id,
                    ],
                    [
                        'price_per_unit' => $svc->price_per_unit ?? 0,
                        'rate_card_price_per_unit' => $svc->rate_card_price_per_unit ?? 0,
                        'transfer_price_per_unit' => $svc->transfer_price_per_unit ?? 0,
                        'currency' => 'MYR',
                    ]
                );
            }
        });

        $this->call(\Database\Seeders\BackfillServicePricesSeeder::class);
    }
}
