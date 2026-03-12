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
        Schema::create('berkas_pidanas', function (Blueprint $table) {
            $table->id();
            
            // Kolom data utama
            $table->enum('jenis', ['PID.B/KHUSUS', 'PIDANA CEPAT', 'ANAK', 'PRAPERADILAN']);
            $table->string('no_perkara')->unique();
            $table->string('pihak');
            $table->string('pasal');
            $table->date('tgl_putus');
            $table->date('tgl_penyerahan');
            
            // Kolom audit log
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Kolom soft delete & timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_pidanas');
    }
};
