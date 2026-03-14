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
       Schema::create('waarmerking', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('nomor_register')->unique(); // Nomor urut Waarmerking
        $table->string('nama_pemohon');
        $table->string('jenis_dokumen'); // Contoh: Perjanjian, Surat Pernyataan
        $table->date('tanggal_legalisasi');
        $table->string('file_path')->nullable();
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waarmerking');
    }
};
