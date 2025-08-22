<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        // 1) Tambah kolum JIKA belum ada (tanpa UNIQUE dulu)
        Schema::table('price_catalogs', function (Blueprint $table) {
            if (!Schema::hasColumn('price_catalogs', 'version_code')) {
                $table->string('version_code')->nullable(); // nullable dulu, no unique
            }
            if (!Schema::hasColumn('price_catalogs', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('price_catalogs', 'effective_date')) {
                $table->date('effective_date')->nullable();
            }
            if (!Schema::hasColumn('price_catalogs', 'is_current')) {
                $table->boolean('is_current')->default(false)->index();
            }
            if (!Schema::hasColumn('price_catalogs', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('price_catalogs', 'created_by')) {
                $table->uuid('created_by')->nullable();
            }
            if (!Schema::hasColumn('price_catalogs', 'created_at')
                && !Schema::hasColumn('price_catalogs', 'updated_at')) {
                $table->timestamps();
            }
        });

        // 2) Backfill version_code yang kosong supaya nanti boleh letak UNIQUE
        $rows = DB::table('price_catalogs')->select('id', 'version_code')->get();
        $i = 1;
        foreach ($rows as $r) {
            if (empty($r->version_code)) {
                // contoh: v20250821-001-ABCD
                $code = 'v' . date('Ymd') . '-' . str_pad((string)$i, 3, '0', STR_PAD_LEFT) . '-' . Str::upper(Str::random(4));
                DB::table('price_catalogs')->where('id', $r->id)->update(['version_code' => $code]);
                $i++;
            }
        }

        // 3) Baru tambah UNIQUE index lepas semua row ada nilai unik
        Schema::table('price_catalogs', function (Blueprint $table) {
            // nama index ikut error yang keluar tadi
            $table->unique('version_code', 'price_catalogs_version_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('price_catalogs', function (Blueprint $table) {
            // buang unique index dulu kalau nak rollback
            try { $table->dropUnique('price_catalogs_version_code_unique'); } catch (\Throwable $e) {}
            // buang kolum tambahan (optional – ikut keperluan)
            if (Schema::hasColumn('price_catalogs', 'created_by')) $table->dropColumn('created_by');
            if (Schema::hasColumn('price_catalogs', 'notes')) $table->dropColumn('notes');
            if (Schema::hasColumn('price_catalogs', 'is_current')) $table->dropColumn('is_current');
            if (Schema::hasColumn('price_catalogs', 'effective_date')) $table->dropColumn('effective_date');
            if (Schema::hasColumn('price_catalogs', 'title')) $table->dropColumn('title');
            // biasanya kita kekalkan version_code; kalau nak buang:
            // if (Schema::hasColumn('price_catalogs', 'version_code')) $table->dropColumn('version_code');
        });
    }
};
