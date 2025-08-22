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
        Schema::create('solutions', function (Blueprint $table) {
                $table->id();
            $table->uuid('project_id');
             $table->uuid('version_id')->unique();
            $table->uuid('customer_id');
            $table->uuid('presale_id');
        
            $table->uuid('project_name');
            $table->uuid('version_name');
            $table->uuid('customer_name');


        $table->string('status');
        $table->string('quotation_id');
        $table->timestamps();
        });


         Schema::table('solutions', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('presale_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solutions');
    }
};
