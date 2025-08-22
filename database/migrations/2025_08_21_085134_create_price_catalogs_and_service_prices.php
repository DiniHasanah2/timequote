<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Versi harga (Price Catalogs)
        Schema::create('price_catalogs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('version_name');            // cth: v3.1.0
            $table->date('effective_from')->nullable();// cth: 2025-08-21
            $table->date('effective_to')->nullable();  // optional
            $table->boolean('is_current')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Harga per service untuk sesuatu versi
        Schema::create('service_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('price_catalog_id');
            $table->uuid('service_id');

            $table->decimal('price_per_unit', 10, 4)->nullable();
            $table->decimal('rate_card_price_per_unit', 10, 4)->nullable();
            $table->decimal('transfer_price_per_unit', 10, 4)->nullable();

            $table->timestamps();

            $table->foreign('price_catalog_id')->references('id')->on('price_catalogs')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();

            $table->unique(['price_catalog_id', 'service_id']); // 1 service 1 harga per versi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_prices');
        Schema::dropIfExists('price_catalogs');
    }
};
