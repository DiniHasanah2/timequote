<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            if (!Schema::hasColumn('service_prices', 'currency')) {
                // letak tanpa ->after() supaya selamat walaupun order kolum berbeza
                $table->char('currency', 3)->default('MYR');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            if (Schema::hasColumn('service_prices', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
