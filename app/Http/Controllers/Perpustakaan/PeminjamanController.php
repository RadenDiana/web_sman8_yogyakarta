<?php

namespace App\Http\Controllers\Perpustakaan;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjam;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $peminjaman = Peminjaman::query()
            ->with(['siswa', 'peminjam', 'buku'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $cari = $request->search;
                $q->where(function ($w) use ($cari) {
                    $w->whereHas('buku',     fn ($b) => $b->where('judul', 'like', "%{$cari}%"))
                      ->orWhereHas('siswa',  fn ($s) => $s->where('nama', 'like', "%{$cari}%"))
                      ->orWhereHas('peminjam', fn ($p) => $p->where('nama', 'like', "%{$cari}%"));
                });
            })
            ->when($request->sort === 'terlama', fn ($q) => $q->orderBy('tgl_pinjam'))
            ->when($request->sort !== 'terlama', fn ($q) => $q->orderByDesc('tgl_pinjam'))
            ->paginate(5)
            ->withQueryString();

        return view('perpustakaan.peminjaman', [
            'peminjaman'     => $peminjaman,
            'siswaOption'    => Siswa::where('status', true)->orderBy('nama')->get(['id', 'nama', 'nis', 'kelas']),
            'peminjamOption' => Peminjam::where('status', true)->orderBy('nama')->get(['id', 'nama', 'kelas']),
            'bukuOption'     => Buku::orderBy('judul')->get(['id', 'judul', 'stok', 'kode']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        // Tentukan peminjam: siswa ATAU guru/umum (salah satu saja)
        if ($data['tipe'] === 'siswa') {
            if (empty($data['siswa_id'])) return back()->with('error', 'Pilih siswa peminjam terlebih dahulu.');
            $data['peminjam_id'] = null;
        } else {
            if (empty($data['peminjam_id'])) return back()->with('error', 'Pilih peminjam terlebih dahulu.');
            $data['siswa_id'] = null;
        }
        unset($data['tipe']);

        $buku = Buku::findOrFail($data['buku_id']);
        if ($buku->stok < 1) {
            return back()->with('error', 'Stok buku habis, tidak dapat dipinjam.');
        }

        $buku->decrement('stok');
        Peminjaman::create($data + ['tgl_dikembalikan' => null]);

        return back()->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $data = $this->validasi($request);

        if ($data['tipe'] === 'siswa') {
            if (empty($data['siswa_id'])) return back()->with('error', 'Pilih siswa peminjam terlebih dahulu.');
            $data['peminjam_id'] = null;
        } else {
            if (empty($data['peminjam_id'])) return back()->with('error', 'Pilih peminjam terlebih dahulu.');
            $data['siswa_id'] = null;
        }
        unset($data['tipe']);

        // Kalau buku diganti & belum dikembalikan: kembalikan stok lama, kurangi stok baru
        if ($data['buku_id'] != $peminjaman->buku_id && ! $peminjaman->tgl_dikembalikan) {
            $bukuBaru = Buku::findOrFail($data['buku_id']);
            if ($bukuBaru->stok < 1) {
                return back()->with('error', 'Stok buku baru habis.');
            }
            $peminjaman->buku->increment('stok');
            $bukuBaru->decrement('stok');
        }

        $peminjaman->update($data);

        return back()->with('success', 'Peminjaman berhasil diperbarui.');
    }

    public function kembalikan(Peminjaman $peminjaman)
    {
        if ($peminjaman->tgl_dikembalikan) {
            return back()->with('error', 'Buku ini sudah dikembalikan.');
        }

        $peminjaman->update(['tgl_dikembalikan' => now()]);
        $peminjaman->buku->increment('stok');

        return back()->with('success', 'Buku berhasil dikembalikan.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if (! $peminjaman->tgl_dikembalikan) {
            $peminjaman->buku->increment('stok');
        }

        $peminjaman->delete();

        return back()->with('success', 'Data peminjaman berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'tipe'        => 'required|in:siswa,luar',
            'siswa_id'    => 'nullable|exists:siswas,id',
            'peminjam_id' => 'nullable|exists:peminjams,id',
            'buku_id'     => 'required|exists:bukus,id',
            'tgl_pinjam'  => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_pinjam',
        ]);
    }
}