<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Kalau ada foreign key ke users, drop dulu (kalau tak ada, abaikan)
        // DB::statement('ALTER TABLE `network_mapping_logs` DROP FOREIGN KEY `network_mapping_logs_user_id_foreign`;');

        DB::statement('ALTER TABLE `network_mapping_logs` MODIFY `user_id` VARCHAR(191) NULL');
        // Optional: tambah index
        // DB::statement('CREATE INDEX `network_mapping_logs_user_id_index` ON `network_mapping_logs` (`user_id`)');
    }

    public function down(): void
    {
        // WARNING: revert ke BIGINT akan fail kalau ada nilai bukan nombor dalam user_id
        DB::statement('ALTER TABLE `network_mapping_logs` MODIFY `user_id` BIGINT UNSIGNED NULL');
    }
};
