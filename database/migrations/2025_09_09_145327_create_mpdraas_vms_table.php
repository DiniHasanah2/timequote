<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up() {
    Schema::create('mpdraas_vms', function (Blueprint $t) {
      $t->uuid('id')->primary();
      $t->uuid('mpdraas_id');
      $t->uuid('version_id');
      $t->integer('row_no')->nullable();

      $t->string('vm_name')->nullable();
      $t->enum('always_on', ['Yes','No'])->nullable();
      $t->enum('pin', ['Yes','No'])->nullable();
      $t->integer('vcpu')->nullable();
      $t->integer('vram')->nullable();
      $t->string('flavour_mapping')->nullable();
      $t->integer('system_disk')->nullable();
      $t->integer('data_disk')->nullable();
      $t->string('operating_system')->nullable();
      $t->integer('rds_count')->nullable();
      $t->enum('m_sql', ['None','Web','Standard','Enterprise'])->nullable();

      // auto fields
      $t->integer('used_system_disk')->nullable();
      $t->integer('used_data_disk')->nullable();
      $t->enum('solution_type', ['None','EVS','OBS'])->nullable();
      $t->integer('rto_expected')->nullable();
      $t->decimal('dd_change',8,2)->nullable();
      $t->decimal('data_change',10,2)->nullable();
      $t->decimal('data_change_size',12,2)->nullable();
      $t->integer('replication_frequency')->nullable();
      $t->integer('num_replication')->nullable();
      $t->decimal('amount_data_change',12,2)->nullable();
      $t->decimal('replication_bandwidth',8,2)->nullable();
      $t->decimal('rpo_achieved',8,2)->nullable();

      $t->timestamps();
    });
  }
  public function down() { Schema::dropIfExists('mpdraas_vms'); }
};
