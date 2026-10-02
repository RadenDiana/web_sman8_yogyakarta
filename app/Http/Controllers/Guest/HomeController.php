<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\Galeri;
use App\Models\Pengumuman;
use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $tingkat  = $request->query('tingkat');
        $kategori = $request->query('kategori');

        $rekapSiswa = Siswa::selectRaw("
                kelas,
                COUNT(*) AS jumlah,
                SUM(jenis_kelamin = 'P') AS p,
                SUM(jenis_kelamin = 'L') AS l,
                SUM(agama = 'Islam')    AS islam,
                SUM(agama = 'Kristen')  AS kristen,
                SUM(agama = 'Katholik') AS katholik,
                SUM(agama = 'Hindu')    AS hindu,
                SUM(agama = 'Budha')    AS budha
            ")
            ->when($tingkat,  fn ($q) => $q->where('kelas', 'like', "$tingkat %"))
            ->when($kategori, fn ($q) => $q->where('kelas', 'like', "% $kategori %"))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        $semuaKelas     = Siswa::select('kelas')->distinct()->pluck('kelas');
        $daftarTingkat  = $semuaKelas->map(fn ($k) => explode(' ', $k)[0])->unique()->sort()->values();
        $daftarKategori = $semuaKelas->map(fn ($k) => explode(' ', $k)[1] ?? null)->filter()->unique()->sort()->values();

        return view('guest.home', [
            'pengumumanTerbaru' => Pengumuman::where('status', 'publish')->latest()->take(6)->get(),
            'ekstrakurikuler'   => Ekstrakurikuler::orderBy('nama')->get(),
            'galeriHome'        => Galeri::latest()->take(8)->get(),
            'prestasiHome'      => Prestasi::where('status', true)->orderByDesc('tanggal')->take(6)->get(),
            'topSiswa'          => Siswa::query()
                                     ->select('siswas.id', 'siswas.nama', 'siswas.kelas', 'siswas.foto')
                                     ->selectRaw('COUNT(peminjaman.id) AS total_pinjam')
                                     ->leftJoin('peminjaman', 'peminjaman.siswa_id', '=', 'siswas.id')
                                     ->groupBy('siswas.id', 'siswas.nama', 'siswas.kelas', 'siswas.foto')
                                     ->having('total_pinjam', '>', 0) // hanya yang pernah meminjam
                                     ->orderByDesc('total_pinjam')
                                     ->orderBy('siswas.nama')
                                     ->take(5)
                                     ->get(),
            'rekapSiswa'        => $rekapSiswa,
            'daftarTingkat'     => $daftarTingkat,
            'daftarKategori'    => $daftarKategori,
            'tingkat'           => $tingkat,
            'kategori'          => $kategori,
        ]);
    }
}