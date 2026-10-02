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
    Schema::create('peminjaman', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
        $table->foreignId('buku_id')->constrained('bukus')->cascadeOnDelete();
        $table->date('tgl_pinjam');
        $table->date('tgl_kembali');
        $table->date('tgl_dikembalikan')->nullable(); // null = masih dipinjam
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
