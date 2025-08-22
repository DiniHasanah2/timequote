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
        Schema::create('project_presale', function (Blueprint $table) {
    $table->uuid('project_id');
    $table->uuid('presale_id');

    $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
    $table->foreign('presale_id')->references('id')->on('users')->cascadeOnDelete();

    $table->primary(['project_id', 'presale_id']); // Composite primary
});

    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_presale');
    }
};
