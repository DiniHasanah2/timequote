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
        Schema::create('network_mappings', function (Blueprint $table) {
            $table->id('id');
            $table->string('network_code')->unique();
            $table->integer('min_bw'); // Minimum bandwidth
            $table->integer('max_bw'); // Maximum bandwidth
            $table->string('eip_foc'); // Elastic IP location
            $table->boolean('anti_ddos')->default(false); // Yes/No as boolean
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_mappings');
    }
};
