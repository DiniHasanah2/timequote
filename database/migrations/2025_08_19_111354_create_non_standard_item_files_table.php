<?php

// database/migrations/2025_08_19_000000_create_non_standard_item_files_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('non_standard_item_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id')->index();
            $table->uuid('version_id')->index();
            $table->uuid('customer_id')->nullable()->index();
            $table->string('original_name');
            $table->string('stored_path');     // storage path
            $table->string('mime_type', 190);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('ext', 40)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('non_standard_item_files');
    }
};
