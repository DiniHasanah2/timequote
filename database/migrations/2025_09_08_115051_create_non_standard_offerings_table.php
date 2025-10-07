<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('non_standard_offerings', function (Blueprint $table) {
            $table->uuid('id');

            // Scope
            $table->uuid('project_id');
            $table->uuid('version_id');
            $table->uuid('customer_id');
            $table->uuid('presale_id');

            // Category & Service (denormalize for snapshot)
            $table->uuid('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->string('category_code', 32)->nullable();

            $table->uuid('service_id')->nullable();
            $table->string('service_name')->nullable();
            $table->string('service_code', 64)->nullable();

            // Commercials
            $table->string('unit', 64)->nullable();                 
            $table->integer('quantity')->default(1);
            $table->integer('months')->default(1);

            $table->decimal('unit_price_per_month', 12, 4)->default(0); 
            $table->decimal('mark_up', 5, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);      

         
            $table->uuid('source_non_standard_item_id')->nullable();

            $table->timestamps();
        });

        Schema::table('non_standard_offerings', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('presale_id')->references('id')->on('users')->onDelete('cascade');

            // optional FKs:
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('source_non_standard_item_id')->references('id')->on('non_standard_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('non_standard_offerings');
    }
};
