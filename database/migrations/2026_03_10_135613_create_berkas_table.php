<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('berkas', function (Blueprint $table) {
            $table->id();
            // Identitas Modul
            $table->string('modul'); // Contoh: 'pidana', 'perdata', 'waarmerking', 'arsip_umum'

            // Data Utama
            $table->string('nomor_registrasi'); // Nomor unik (perkara/surat)
            $table->string('subjek'); // Nama pihak atau judul surat
            $table->date('tanggal_kejadian'); // Tanggal perkara/surat
            $table->text('deskripsi')->nullable();

            // Data Tambahan (JSON agar fleksibel untuk kolom khusus setiap modul)
            $table->json('metadata')->nullable();

            // Audit & Relasi
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas');
    }
};
