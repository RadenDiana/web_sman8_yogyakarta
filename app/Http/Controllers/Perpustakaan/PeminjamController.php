<?php

namespace App\Http\Controllers\Perpustakaan;

use App\Http\Controllers\Controller;
use App\Models\Peminjam;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PeminjamController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->search;

        $statistik = [
            'peminjaman as sedang_dipinjam' => fn ($q) => $q->whereNull('tgl_dikembalikan'),
            'peminjaman as telah_meminjam',
            'peminjaman as terlambat_count' => fn ($q) =>
                $q->whereNull('tgl_dikembalikan')->whereDate('tgl_kembali', '<', now()),
        ];

        // 1) Guru / umum yang terdaftar manual — selalu tampil
        $luar = Peminjam::query()
            ->withCount($statistik)
            ->when($cari, fn ($q) => $q->where('nama', 'like', "%{$cari}%"))
            ->get()
            ->each(fn ($p) => $p->tipe = 'luar');

        // 2) Siswa yang PERNAH meminjam — muncul otomatis
        $siswa = Siswa::query()
            ->whereHas('peminjaman')
            ->withCount($statistik)
            ->when($cari, fn ($q) => $q->where(function ($w) use ($cari) {
                $w->where('nama', 'like', "%{$cari}%")
                  ->orWhere('nis', 'like', "%{$cari}%")
                  ->orWhere('kelas', 'like', "%{$cari}%");
            }))
            ->get()
            ->each(fn ($s) => $s->tipe = 'siswa');

        $gabungan = $luar->concat($siswa)->sortBy('nama')->values();

        $perPage = 8;
        $halaman = LengthAwarePaginator::resolveCurrentPage();
        $items   = $gabungan->slice(($halaman - 1) * $perPage, $perPage)->values();

        $siswas = new LengthAwarePaginator(
            $items,
            $gabungan->count(),
            $perPage,
            $halaman,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('perpustakaan.peminjam', compact('siswas'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) $data['foto'] = $this->simpanFoto($request);
        $data['status'] = $request->boolean('status');

        Peminjam::create($data);

        return back()->with('success', 'Peminjam (guru/umum) berhasil ditambahkan.');
    }

    public function update(Request $request, Peminjam $peminjam)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($peminjam->foto);
            $data['foto'] = $this->simpanFoto($request);
        }
        $data['status'] = $request->boolean('status');

        $peminjam->update($data);

        return back()->with('success', 'Data peminjam berhasil diperbarui.');
    }

    public function destroy(Peminjam $peminjam)
    {
        if ($peminjam->peminjaman()->whereNull('tgl_dikembalikan')->exists()) {
            return back()->with('error', 'Peminjam masih memiliki buku yang belum dikembalikan.');
        }

        $this->hapusFoto($peminjam->foto);
        $peminjam->delete();

        return back()->with('success', 'Data peminjam berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama'  => 'required|string|max:100',
            'nis'   => 'nullable|string|max:30',
            'kelas' => 'nullable|string|max:50',
            'foto'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }

    private function simpanFoto(Request $request): string
    {
        $file = $request->file('foto');
        $nama = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('assets/images/siswa'), $nama);

        return $nama;
    }

    private function hapusFoto(?string $nama): void
    {
        if ($nama && file_exists(public_path('assets/images/siswa/' . $nama))) {
            unlink(public_path('assets/images/siswa/' . $nama));
        }
    }
}