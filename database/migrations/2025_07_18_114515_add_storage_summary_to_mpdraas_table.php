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
        Schema::table('mpdraas', function (Blueprint $table) {

            $table->integer('main')->nullable();
        	$table->integer('used')->nullable();
        	$table->integer('delta')->nullable();
        	$table->integer('total_replication')->nullable();

          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpdraas', function (Blueprint $table) {
            //
        });
    }
};