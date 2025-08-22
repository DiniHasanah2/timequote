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
        Schema::create('projects', function (Blueprint $table) {
            $table->engine = 'InnoDB'; 
            
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('presale_id');
            $table->string('name');
            $table->decimal('quotation_value', 12, 2)->default(0.00);
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('presale_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // versions table migration
Schema::create('versions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('project_id');
    $table->string('version_name');
    $table->string('version_number');
    $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
    $table->timestamps();


    
    // Composite unique (prevent same project having duplicate versions)
    $table->unique(['project_id', 'version_number']);
});
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
         Schema::dropIfExists('versions');
    }
};
