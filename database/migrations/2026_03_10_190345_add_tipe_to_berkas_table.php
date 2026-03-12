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
        Schema::table('berkas', function (Blueprint $table) {
            // Menambahkan kolom tipe untuk membedakan kategori perkara
            // 'pidana' sebagai default agar data lama tidak error
            $table->string('tipe')->default('pidana')->after('id');

            // Opsional: Jika Anda ingin menambahkan metadata khusus perdata
            // agar bisa disimpan dalam format JSON di database
            $table->json('metadata')->nullable()->after('tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'metadata']);
        });
    }
};
