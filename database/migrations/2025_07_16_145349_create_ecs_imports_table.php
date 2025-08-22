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
        Schema::create('ecs_imports', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('version_id')->constrained()->onDelete('cascade');
    $table->json('import_data');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecs_imports');
    }
};
