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
       Schema::create('p_flavour_maps', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('flavour');
    $table->integer('vcpu');
    $table->integer('vram');
    $table->string('type');
    $table->string('generation');
    $table->string('memory_label');
    $table->integer('windows_license_count');
    $table->integer('rhel')->nullable();
    $table->string('dr')->nullable();
    $table->string('pin')->nullable();
    $table->string('gpu')->nullable();
    $table->string('ddh')->nullable();
    $table->integer('mssql')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_flavour_maps');
    }
};
