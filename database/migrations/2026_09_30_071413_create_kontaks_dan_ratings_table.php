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
    Schema::create('kontaks', function (Blueprint $table) {
        $table->id();
        $table->string('nama', 100);
        $table->string('email');
        $table->string('subjek');
        $table->text('pesan');
        $table->timestamp('dibalas_at')->nullable(); // null = belum dibalas
        $table->timestamps();
    });

    Schema::create('ratings', function (Blueprint $table) {
        $table->id();
        $table->unsignedTinyInteger('rating'); // 1–5
        $table->text('komentar')->nullable();  // anonim, tanpa nama
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('kontaks');
    Schema::dropIfExists('ratings');
}
};
