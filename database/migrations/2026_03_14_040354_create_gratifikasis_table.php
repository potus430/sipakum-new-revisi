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
        Schema::create('gratifikasi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('pelapor');
    $table->string('pemberi');
    $table->string('bentuk_gratifikasi'); // Uang, Barang, Fasilitas
    $table->decimal('estimasi_nilai', 15, 2)->nullable();
    $table->date('tanggal_penerimaan');
    $table->text('kronologi');
    $table->string('status')->default('Pending'); // Pending, Dilaporkan, Ditolak
    $table->string('file_bukti')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gratifikasi');
    }
};
