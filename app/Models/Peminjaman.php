<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'siswa_id',
        'peminjam_id',  
        'buku_id',
        'tgl_pinjam',
        'tgl_kembali',
        'tgl_dikembalikan',
    ];

    protected $casts = [
        'tgl_pinjam'       => 'date',
        'tgl_kembali'      => 'date',
        'tgl_dikembalikan' => 'date',
    ];

    public function siswa()    { return $this->belongsTo(Siswa::class); }
    public function peminjam() { return $this->belongsTo(Peminjam::class); }
    public function buku()     { return $this->belongsTo(Buku::class); }

    public function getStatusLabelAttribute(): string
    {
        if ($this->tgl_dikembalikan) return 'Dikembalikan';
        return now()->gt($this->tgl_kembali) ? 'Terlambat' : 'Dipinjam';
    }

    public function getNamaPeminjamAttribute(): string
    {
        return $this->siswa?->nama ?? $this->peminjam?->nama ?? '-';
    }
}