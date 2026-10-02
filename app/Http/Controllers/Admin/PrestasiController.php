<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
public function index(Request $request)
{
    $prestasis = Prestasi::query()
        ->with('user')
        ->when($request->filled('search'), function ($q) use ($request) {
            $cari = $request->search;
            $q->where(function ($w) use ($cari) {
                $w->where('judul', 'like', "%{$cari}%")
                  ->orWhere('penyelenggara', 'like', "%{$cari}%");
            });
        })
        ->when($request->filled('sort'), function ($q) use ($request) {
            match ($request->sort) {
                'terlama' => $q->orderBy('tanggal'),
                'a-z'     => $q->orderBy('judul'),
                'z-a'     => $q->orderByDesc('judul'),
                default   => $q->orderByDesc('tanggal'),
            };
        }, fn ($q) => $q->orderByDesc('tanggal'))
        ->paginate(12) // grid 4 kolom × 3 baris
        ->withQueryString();

    return view('admin.prestasi', compact('prestasis'));
}

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) $data['foto'] = $this->simpanFoto($request);
        $data['status']  = $request->boolean('status');
        $data['user_id'] = auth()->id();

        Prestasi::create($data);

        return back()->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function update(Request $request, Prestasi $prestasi)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($prestasi->foto);
            $data['foto'] = $this->simpanFoto($request);
        }
        $data['status'] = $request->boolean('status');

        $prestasi->update($data);

        return back()->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Prestasi $prestasi)
    {
        $this->hapusFoto($prestasi->foto);
        $prestasi->delete();

        return back()->with('success', 'Prestasi berhasil dihapus.');
    }

    /* ---------- helper ---------- */

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul'         => 'required|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'tanggal'       => 'nullable|date',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }

    private function simpanFoto(Request $request): string
    {
        $file = $request->file('foto');
        $nama = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('assets/images/prestasi'), $nama);

        return $nama;
    }

    private function hapusFoto(?string $nama): void
    {
        if ($nama && file_exists(public_path('assets/images/prestasi/' . $nama))) {
            unlink(public_path('assets/images/prestasi/' . $nama));
        }
    }
}