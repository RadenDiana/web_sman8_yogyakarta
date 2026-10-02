<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    protected $fillable = ['nama', 'email', 'subjek', 'pesan', 'dibalas_at'];

    protected $casts = ['dibalas_at' => 'datetime'];
}