<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjam extends Model
{
    protected $fillable = ['nama', 'nis', 'kelas', 'foto', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('assets/images/siswa/' . $this->foto)
            : asset('assets/images/sekolah/logo.png');
    }
}