<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
public function up()
{
    Schema::table('quotations', function (Blueprint $table) {
        $table->integer('contract_duration')->default(12);
    });
}

public function down()
{
    Schema::table('quotations', function (Blueprint $table) {
        $table->dropColumn('contract_duration');
    });
}

};
