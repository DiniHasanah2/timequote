<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
       
        DB::statement('ALTER TABLE `network_mapping_logs` MODIFY `user_id` VARCHAR(191) NULL');
       
    }

    public function down(): void
    {
       
        DB::statement('ALTER TABLE `network_mapping_logs` MODIFY `user_id` BIGINT UNSIGNED NULL');
    }
};
