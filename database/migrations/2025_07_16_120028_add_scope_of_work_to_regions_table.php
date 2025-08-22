<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('regions', function (Blueprint $table) {
        $table->text('scope_of_work')->nullable(); // boleh buang after() kalau tak perlu
    });
}

public function down()
{
    Schema::table('regions', function (Blueprint $table) {
        $table->dropColumn('scope_of_work');
    });
}

};
