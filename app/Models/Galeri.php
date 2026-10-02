<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $table = 'galeris';
    protected $fillable = ['judul', 'foto', 'tanggal', 'status'];

    protected $casts = ['tanggal' => 'date'];

    // Foto dengan fallback ke logo sekolah (sama seperti Siswa & Prestasi)
    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('assets/images/galeri/' . $this->foto)
            : asset('assets/images/sekolah/logo.png');
    }
}