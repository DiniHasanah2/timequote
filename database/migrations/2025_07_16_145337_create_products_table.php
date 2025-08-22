
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

    Schema::create('products', function (Blueprint $table) {   
    $table->uuid('id')->primary();
    $table->uuid('presale_id');
    $table->uuid('quotation_id');
    $table->uuid('services_id');
    $table->foreign('presale_id')->references('id')->on('users')->cascadeOnDelete();
    $table->foreign('quotation_id')->references('id')->on('quotations')->cascadeOnDelete();
    $table->foreign('services_id')->references('id')->on('services')->onDelete('cascade');
    $table->integer('quantity')->nullable();
    $table->decimal('priceperunit', 10, 2);
    $table->decimal('totalprice', 10, 2);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};