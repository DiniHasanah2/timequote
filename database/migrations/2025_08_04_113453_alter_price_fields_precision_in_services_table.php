<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPriceFieldsPrecisionInServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_per_unit', 10, 4)->change();
            $table->decimal('rate_card_price_per_unit', 10, 4)->change();
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_per_unit', 10, 2)->change();
            $table->decimal('rate_card_price_per_unit', 10, 2)->change();
        });
    }
}
