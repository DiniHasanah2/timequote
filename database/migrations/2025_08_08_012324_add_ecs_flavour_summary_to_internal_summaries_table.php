<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::table('internal_summaries', function (Blueprint $table) {
        $table->json('ecs_flavour_summary')->nullable();
    });
}

public function down()
{
    Schema::table('internal_summaries', function (Blueprint $table) {
        $table->dropColumn('ecs_flavour_summary');
    });
}

};
