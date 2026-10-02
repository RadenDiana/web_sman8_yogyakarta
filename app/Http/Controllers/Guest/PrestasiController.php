<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;

class PrestasiController extends Controller
{
    public function index()
    {
        return view('guest.prestasi', [
            'prestasis' => Prestasi::where('status', true)
                ->orderByDesc('tanggal')
                ->paginate(9) // 3 kolom x 3 baris
                ->withQueryString(),
        ]);
    }
}