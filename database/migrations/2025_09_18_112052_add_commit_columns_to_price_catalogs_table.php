<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('price_catalogs', function (Blueprint $table) {
            $table->timestamp('committed_at')->nullable()->after('notes');
            $table->uuid('committed_by')->nullable()->after('committed_at');
        });
    }

    public function down(): void
    {
        Schema::table('price_catalogs', function (Blueprint $table) {
            $table->dropColumn(['committed_at', 'committed_by']);
        });
    }
};
