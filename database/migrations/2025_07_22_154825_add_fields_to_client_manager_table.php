<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_manager', function (Blueprint $table) {
            $table->string('staff_no')->nullable()->after('name');
            $table->string('division')->nullable()->after('staff_no');
            $table->string('personal_contact')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('client_manager', function (Blueprint $table) {
            $table->dropColumn(['staff_no', 'division', 'personal_contact']);
        });
    }
};

