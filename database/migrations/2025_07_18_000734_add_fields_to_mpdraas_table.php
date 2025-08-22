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

            $table->integer('num_proxy')->nullable();
            $table->string('vm_name')->nullable();
            $table->string('always_on')->default('No');
            $table->string('pin')->default('No');
            $table->integer('vcpu')->nullable();
            $table->integer('vram')->nullable();
            $table->text('flavour_mapping')->nullable(); // JSON atau strin

            
            $table->integer('system_disk')->nullable();
            $table->integer('data_disk')->nullable();
            $table->string('operating_system')->default('Microsoft Windows Std');
            $table->integer('rds_count')->nullable();
            $table->string('m_sql')->default('Standard');
            $table->integer('used_system_disk')->nullable();
            $table->integer('used_data_disk')->nullable();
            $table->string('solution_type')->default('');
            $table->integer('rto_expected')->nullable();
            $table->integer('dd_change')->nullable();
            $table->integer('data_change')->nullable();
            $table->integer('data_change_size')->nullable();
            $table->integer('replication_frequency')->nullable();
            $table->integer('num_replication')->nullable();
            $table->integer('amount_data_change')->nullable();
            $table->integer('replication_bandwidth')->nullable();
            $table->integer('rpo_achieved')->nullable();
            $table->string('ddos_requirement')->default('No');
             $table->integer('bandwidth_requirement')->nullable();
          
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