<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpdraas', function (Blueprint $table) {
            $table->json('dr_network')->nullable()->after('total_replication');
        });
    }

    public function down(): void
    {
        Schema::table('mpdraas', function (Blueprint $table) {
            $table->dropColumn('dr_network');
        });
    }
};
