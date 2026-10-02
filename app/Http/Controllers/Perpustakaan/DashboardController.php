<?php

namespace App\Http\Controllers\Perpustakaan;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $terlambatQuery = Peminjaman::with('buku')
            ->whereNull('tgl_dikembalikan')
            ->whereDate('tgl_kembali', '<', now());

        return view('perpustakaan.dashboard', [
            'totalBuku'       => Buku::count(),
            'totalDipinjam'   => Peminjaman::whereNull('tgl_dikembalikan')->count(),
            'totalPeminjam'   => Peminjaman::distinct('siswa_id')->count('siswa_id'),
            'topPeminjam'     => Peminjaman::query()
                 ->join('siswas', 'siswas.id', '=', 'peminjaman.siswa_id')
                 ->selectRaw('siswas.id, siswas.nama, siswas.kelas, COUNT(*) AS total')
                 ->whereMonth('peminjaman.tgl_pinjam', now()->month)
                 ->whereYear('peminjaman.tgl_pinjam', now()->year)
                 ->groupBy('siswas.id', 'siswas.nama', 'siswas.kelas')
                 ->orderByDesc('total')
                 ->take(10)
                 ->get(),
            'terlambat'       => (clone $terlambatQuery)->latest('tgl_kembali')->take(10)->get(),
            'totalTerlambat'  => $terlambatQuery->count(),
        ]);
    }
}