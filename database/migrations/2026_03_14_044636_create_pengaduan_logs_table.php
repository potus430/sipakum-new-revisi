<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaduan_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pengaduan_id')->constrained('pengaduan')->onDelete('cascade');
    $table->foreignId('user_id')->constrained(); // Admin yang merubah
    $table->string('status_lama');
    $table->string('status_baru');
   $table->timestamps(); // Menggunakan created_at untuk mencatat waktu perubahan

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan_logs');
    }
};
