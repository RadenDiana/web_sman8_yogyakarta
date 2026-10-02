<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Siswa;

class TopSiswaController extends Controller
{
    public function index()
    {
        return view('guest.top-siswa', [
            'topSiswa' => Siswa::query()
                ->select('siswas.id', 'siswas.nama', 'siswas.kelas', 'siswas.foto')
                ->selectRaw('COUNT(peminjaman.id) AS total_pinjam')
                ->leftJoin('peminjaman', 'peminjaman.siswa_id', '=', 'siswas.id')
                ->groupBy('siswas.id', 'siswas.nama', 'siswas.kelas', 'siswas.foto')
                ->having('total_pinjam', '>', 0) // hanya yang pernah meminjam
                ->orderByDesc('total_pinjam')
                ->orderBy('siswas.nama')
                ->paginate(12),
        ]);
    }
}