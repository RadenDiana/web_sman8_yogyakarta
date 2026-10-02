<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $fillable = [
        'nama', 'nis', 'kelas', 'jenis_kelamin', 'agama',
        'tempat_lahir', 'tanggal_lahir', 'alamat', 'foto', 'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status'        => 'boolean',
    ];

    // Foto dengan fallback ke logo sekolah
    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('assets/images/siswa/' . $this->foto)
            : asset('assets/images/sekolah/logo.png');
    }

    public function peminjaman()
{
    return $this->hasMany(Peminjaman::class);
}
}