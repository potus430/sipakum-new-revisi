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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa pelakunya
            $table->string('module');     // Nama Modul (Pidana, Perdata, dsb)
            $table->string('action');     // CREATE, UPDATE, DELETE
            $table->string('target_id');  // ID data yang dimanipulasi
            $table->json('old_data')->nullable(); // Data sebelum berubah
            $table->json('new_data')->nullable(); // Data sesudah berubah
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
