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
        Schema::create('services', function (Blueprint $table) {
            //$table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->string('category_name');
            $table->string('category_code');
            $table->string('code');
            $table->string('name');
            $table->string('measurement_unit');
            $table->text('description')->nullable();
            $table->decimal('price_per_unit', 10, 2);
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
