<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('ecs_configurations', function (Blueprint $table) {
            // jadikan nullable, buang default 'No'
            $table->string('ecs_dr')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ecs_configurations', function (Blueprint $table) {
         
            $table->string('ecs_dr')->default('No')->change();
        });
    }
};


