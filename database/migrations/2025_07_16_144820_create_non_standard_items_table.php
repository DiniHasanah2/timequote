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
        Schema::create('non_standard_items', function (Blueprint $table) {
            $table->uuid('id');
            // Foreign keys
            $table->uuid('project_id');
            $table->uuid('version_id');
            $table->uuid('customer_id');
            $table->uuid('presale_id');
            $table->string('item_name')->nullable();
            $table->string('unit')->nullable();
            $table->integer('quantity')->nullable();
           $table->decimal('cost', 10, 2)->nullable();
$table->decimal('mark_up', 5, 2)->nullable();
$table->decimal('selling_price', 10, 2)->nullable();

            

            $table->timestamps();
        });

    Schema::table('non_standard_items', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('presale_id')->references('id')->on('users')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_standard_items');
    }
};



     