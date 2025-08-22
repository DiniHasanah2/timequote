<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRateCardPricePrecisionInServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('rate_card_price_per_unit', 10, 4)->change();
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('rate_card_price_per_unit', 10, 2)->change();
        });
    }
}

