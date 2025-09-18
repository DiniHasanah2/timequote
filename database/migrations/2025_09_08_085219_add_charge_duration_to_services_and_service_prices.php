<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('charge_duration')->nullable()->default('Monthly')->after('measurement_unit');
        });

        Schema::table('service_prices', function (Blueprint $table) {
            $table->string('charge_duration')->nullable()->default('Monthly')->after('price_per_unit');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('charge_duration');
        });

        Schema::table('service_prices', function (Blueprint $table) {
            $table->dropColumn('charge_duration');
        });
    }
};
