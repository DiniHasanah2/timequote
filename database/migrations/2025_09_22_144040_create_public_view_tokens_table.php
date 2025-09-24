<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('public_view_tokens', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('quote_id')->index();
      $t->unsignedBigInteger('version_id')->nullable()->index(); // null jika 'latest'
      $t->boolean('is_latest')->default(false);

     
      $t->text('token');

      // Unique hash (HEX 64 char)
      $t->char('token_hash', 64)->unique();

      $t->timestamp('expires_at')->nullable();
      $t->timestamp('revoked_at')->nullable();
      $t->unsignedBigInteger('created_by')->nullable();
      $t->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('public_view_tokens');
  }
};
