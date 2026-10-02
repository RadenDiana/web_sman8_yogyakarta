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
    Schema::create('siswas', function (Blueprint $table) {
        $table->id();
        $table->string('nama', 100);
        $table->string('nis', 20)->unique();
        $table->string('kelas', 20);              // format: "XII F 1"
        $table->enum('jenis_kelamin', ['P', 'L']);
        $table->string('agama', 20);
        $table->string('tempat_lahir', 100)->nullable();
        $table->date('tanggal_lahir')->nullable();
        $table->string('alamat', 255)->nullable();
        $table->string('foto')->nullable();
        $table->boolean('status')->default(true); // true = Aktif
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
