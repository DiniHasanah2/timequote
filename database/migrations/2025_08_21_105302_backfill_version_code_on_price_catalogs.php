<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('price_catalogs')
          ->whereNull('version_code')
          ->orWhere('version_code', '')
          ->update([
              // MySQL boleh set kolum = kolum lain direct
              'version_code' => DB::raw('version_name')
          ]);
    }
    public function down(): void {}
};
