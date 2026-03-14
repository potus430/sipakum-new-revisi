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
        Schema::create('pengaduan', function (Blueprint $table) {
    $table->id();
    $table->string('judul');
    $table->text('isi_pengaduan');
    $table->string('anonim')->default('Tidak'); // Opsi pelapor anonim
    $table->string('status')->default('Terima'); // Terima, Verifikasi, Investigasi, Selesai
    $table->string('file_pendukung')->nullable();
    $table->foreignId('user_id')->nullable()->constrained('users'); // Null jika anonim
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
