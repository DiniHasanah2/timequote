<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('security_service_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('version_id')->index();
            $table->uuid('project_id')->index()->nullable();
            $table->uuid('customer_id')->index()->nullable();

            $table->string('original_name');
            $table->string('stored_path');     // storage path on 'public' disk
            $table->string('mime_type')->nullable();
            $table->string('ext', 16)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_service_files');
    }
};
