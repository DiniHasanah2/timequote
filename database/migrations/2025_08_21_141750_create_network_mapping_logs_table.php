<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('network_mapping_logs', function (Blueprint $table) {
            $table->id();

            // Boleh null supaya log umum (import/export) tak wajib ada mapping id
            $table->foreignId('network_mapping_id')
                  ->nullable()
                  ->constrained('network_mappings')
                  ->nullOnDelete();

            $table->string('action'); // created, updated, deleted, import, export
            $table->json('old_values')->nullable(); // nilai sebelum
            $table->json('new_values')->nullable(); // nilai selepas

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['network_mapping_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_mapping_logs');
    }
};
