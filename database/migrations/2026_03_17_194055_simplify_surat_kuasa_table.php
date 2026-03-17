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
        Schema::table('surat_kuasa', function (Blueprint $table) {
            // Hapus foreign key dan kolom lama
            $table->dropForeign(['berkas_id']);
            $table->dropColumn('berkas_id');

            // Tambah kolom kategori baru (Pidana/Perdata)
            $table->string('kategori_perkara')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_kuasa', function (Blueprint $table) {
            $table->foreignId('berkas_id')->nullable()->constrained('berkas');
            $table->dropColumn('kategori_perkara');
        });
    }
};
