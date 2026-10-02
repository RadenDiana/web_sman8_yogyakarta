<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $fillable = [
        'judul', 'penyelenggara', 'tanggal', 'foto', 'status', 'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('assets/images/prestasi/' . $this->foto)
            : asset('assets/images/sekolah/logo.png');
    }
}