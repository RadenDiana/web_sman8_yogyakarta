<?php

namespace App\Http\Controllers\Perpustakaan;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $bukus = Buku::query()
            ->withCount(['peminjaman as dipinjam_count' => fn ($q) => $q->whereNull('tgl_dikembalikan')])
            ->when($request->filled('search'), function ($q) use ($request) {
                $cari = $request->search;
                $q->where(fn ($w) => $w->where('judul', 'like', "%{$cari}%")
                    ->orWhere('kode', 'like', "%{$cari}%")
                    ->orWhere('pengarang', 'like', "%{$cari}%"));
            })
            ->when($request->sort === 'terlama', fn ($q) => $q->oldest())
            ->when($request->sort === 'a-z',     fn ($q) => $q->orderBy('judul'))
            ->when($request->sort === 'z-a',     fn ($q) => $q->orderByDesc('judul'))
            ->when(! in_array($request->sort, ['terlama', 'a-z', 'z-a']), fn ($q) => $q->latest())
            ->paginate(5)
            ->withQueryString();

        return view('perpustakaan.buku', compact('bukus'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) $data['foto'] = $this->simpanFoto($request);

        Buku::create($data);

        return back()->with('success', 'Buku berhasil ditambahkan.');
    }

    public function update(Request $request, Buku $buku)
    {
        $data = $this->validasi($request, $buku->id);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($buku->foto);
            $data['foto'] = $this->simpanFoto($request);
        }

        $buku->update($data);

        return back()->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku)
    {
        if ($buku->peminjaman()->whereNull('tgl_dikembalikan')->exists()) {
            return back()->with('error', 'Buku sedang dipinjam, tidak dapat dihapus.');
        }

        $this->hapusFoto($buku->foto);
        $buku->delete();

        return back()->with('success', 'Buku berhasil dihapus.');
    }

    private function validasi(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'kode'       => 'required|max:20|unique:bukus,kode' . ($id ? ",{$id}" : ''),
            'judul'      => 'required|max:255',
            'pengarang'  => 'nullable|max:255',
            'penerbit'   => 'nullable|max:255',
            'stok'       => 'required|integer|min:0',
            'foto'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }

    private function simpanFoto(Request $request): string
    {
        $file = $request->file('foto');
        $nama = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('assets/images/buku'), $nama);

        return $nama;
    }

    private function hapusFoto(?string $nama): void
    {
        if ($nama && file_exists(public_path('assets/images/buku/' . $nama))) {
            unlink(public_path('assets/images/buku/' . $nama));
        }
    }
}