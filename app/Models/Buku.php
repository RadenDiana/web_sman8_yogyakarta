<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $fillable = ['kode', 'judul', 'pengarang', 'penerbit', 'stok', 'foto'];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('assets/images/buku/' . $this->foto)
            : asset('assets/images/sekolah/logo.png');
    }
}