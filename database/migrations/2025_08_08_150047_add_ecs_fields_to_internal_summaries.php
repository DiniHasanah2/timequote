<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('internal_summaries', function (Blueprint $table) {
    $table->string('ecs_flavour_mapping')->nullable();
    $table->integer('ecs_vcpu')->nullable();
    $table->integer('ecs_vram')->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_summaries', function (Blueprint $table) {
            //
        });
    }
};
