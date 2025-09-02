<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('internal_summaries', function (Blueprint $table) {
        // Letak selepas image storage (ikut susunan DB kau)
        $table->unsignedBigInteger('kl_evs_dr')->default(0)->after('cyber_image_storage');
        $table->unsignedBigInteger('cyber_evs_dr')->default(0)->after('kl_evs_dr');
    });
}

public function down(): void
{
    Schema::table('internal_summaries', function (Blueprint $table) {
        $table->dropColumn(['kl_evs_dr', 'cyber_evs_dr']);
    });
}

};
