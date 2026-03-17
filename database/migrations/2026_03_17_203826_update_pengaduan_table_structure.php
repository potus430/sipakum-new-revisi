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
        Schema::table('pengaduan', function (Blueprint $table) {
            // Menambah kolom baru sesuai revisi
            $table->string('pelapor')->after('judul');
            $table->string('terlapor')->after('pelapor');
            $table->string('jenis_pengaduan')->after('terlapor'); // Contoh: Disiplin, Pelayanan, dll
            $table->string('sarana_pengaduan')->after('jenis_pengaduan'); // Contoh: Meja Pengaduan, Website, WA
            $table->text('tindak_lanjut')->nullable()->after('status');
            $table->text('keterangan')->nullable()->after('tindak_lanjut');

            // Menghapus kolom lama yang sudah tidak relevan (opsional)
            // $table->dropColumn('isi_pengaduan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
