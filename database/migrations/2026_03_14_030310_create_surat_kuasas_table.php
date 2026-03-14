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
        Schema::create('surat_kuasa', function (Blueprint $table) {
            $table->id();
        $table->foreignId('berkas_id')->constrained('berkas')->onDelete('cascade');
        $table->string('nomor_surat_kuasa')->unique();
        $table->date('tanggal_surat');
        $table->string('penerima_kuasa'); // Nama Pengacara/Advokat
        $table->string('pemberi_kuasa');  // Nama Terdakwa/Penggugat
        $table->enum('jenis_kuasa', ['Khusus', 'Substitusi', 'Umum']);
        $table->string('file_path')->nullable(); // Scan PDF Surat Kuasa
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_kuasas');
    }
};
