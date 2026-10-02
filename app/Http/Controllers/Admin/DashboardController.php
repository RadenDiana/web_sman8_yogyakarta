<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\Galeri;
use App\Models\Kontak;
use App\Models\Pengumuman;
use App\Models\Rating;
use App\Models\Siswa;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalEkstrakurikuler' => Ekstrakurikuler::count(),
            'totalSiswa'           => Siswa::count(),
            'totalAkun'            => User::count(),
            'totalGaleri'          => Galeri::count(),
            'pengumumanTerbaru'    => Pengumuman::latest()->take(8)->get(),

            // Rating
            'totalRating'          => Rating::count(),
            'rataRating'           => round(Rating::avg('rating') ?? 0, 1),
            'ratingPerBintang'     => Rating::selectRaw('rating, COUNT(*) AS jumlah')
                                       ->groupBy('rating')
                                       ->pluck('jumlah', 'rating'),
            'komentarTerbaru'      => Rating::whereNotNull('komentar')
                                       ->where('komentar', '!=', '')
                                       ->latest()->take(8)->get(),

            // Kontak
            'pesanBelumDibalas'    => Kontak::whereNull('dibalas_at')->count(),
        ]);
    }
}