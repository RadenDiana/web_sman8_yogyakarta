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
    // Tabel peminjam eksternal (guru/umum) — TERPISAH dari siswas
    Schema::create('peminjams', function (Blueprint $table) {
        $table->id();
        $table->string('nama', 100);
        $table->string('nis', 30)->nullable();    // bisa diisi NIP/identitas
        $table->string('kelas', 50)->nullable();  // kategori: "Guru", "Umum", dll
        $table->string('foto')->nullable();
        $table->boolean('status')->default(true);
        $table->timestamps();
    });

    Schema::table('peminjaman', function (Blueprint $table) {
        $table->dropForeign(['siswa_id']);
    });

    Schema::table('peminjaman', function (Blueprint $table) {
        $table->foreignId('siswa_id')->nullable()->change(); // sekarang bisa null
        $table->foreignId('peminjam_id')->nullable()->constrained('peminjams')->nullOnDelete();
    });

    Schema::table('peminjaman', function (Blueprint $table) {
        $table->foreign('siswa_id')->references('id')->on('siswas')->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('peminjaman', function (Blueprint $table) {
        $table->dropForeign(['peminjam_id']);
        $table->dropColumn('peminjam_id');
    });
    Schema::dropIfExists('peminjams');
}
};