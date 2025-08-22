<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('ecs_flavours', function (Blueprint $table) {
        $table->boolean('dr')->default(false)->after('red_hat_enterprise_license_count'); 
    });
}

public function down()
{
    Schema::table('ecs_flavours', function (Blueprint $table) {
        $table->dropColumn('dr');
    });
}

};
