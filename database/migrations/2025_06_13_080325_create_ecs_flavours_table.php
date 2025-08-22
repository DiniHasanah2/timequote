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
        Schema::create('ecs_flavours', function (Blueprint $table) {
            $table->id('id'); // Not UUID as per requirement
            $table->string('ecs_code');
            $table->string('flavour_name');
            $table->integer('vCPU');
            $table->string('RAM');
            $table->string('type');
            $table->string('generation');
            $table->string('memory_label');
            $table->integer('windows_license_count')->default(0);
            $table->integer('red_hat_enterprise_license_count')->default(0);
            $table->boolean('pin')->default(false);
            $table->boolean('gpu')->default(false);
            $table->boolean('dedicated_host')->default(false);
            $table->integer('microsoft_sql_license_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecs_flavours');
    }
};
