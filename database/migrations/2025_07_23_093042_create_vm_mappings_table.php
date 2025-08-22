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
        Schema::create('vm_mappings', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->string('vm_name')->nullable(); // from ecs_configurations
    $table->string('customer_name')->nullable(); // from customer
    $table->uuid('project_id')->nullable();
    $table->uuid('quotation_id')->nullable();
    $table->string('ecs_flavour_mapping')->nullable(); // from ecs_configurations
    $table->string('ecs_code')->nullable(); // from ecs_flavours

    $table->timestamps();

    // Recommended foreign keys:
    $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
    $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('set null');
});
    }
    
    public function down(): void
    {
        Schema::dropIfExists('vm_mappings');
    }
};
