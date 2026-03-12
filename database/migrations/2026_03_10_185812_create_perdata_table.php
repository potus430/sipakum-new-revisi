<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('perdata', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi')->unique(); // Contoh: 12/Pdt.G/2026/PN.XX
            $table->string('penggugat');
            $table->string('tergugat');
            $table->date('tanggal_register');
            $table->string('jenis_perkara'); // Contoh: Gugatan, Permohonan
            $table->json('metadata')->nullable(); // Untuk hakim, panitera, atau info tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perdata');
    }
};
