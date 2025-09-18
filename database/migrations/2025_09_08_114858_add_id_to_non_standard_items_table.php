<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('non_standard_items', function (Blueprint $table) {
        $table->uuid('id')->primary()->first();
    });
}

public function down()
{
    Schema::table('non_standard_items', function (Blueprint $table) {
        $table->dropColumn('id');
    });
}

};
