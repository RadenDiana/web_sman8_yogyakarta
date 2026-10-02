<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $galeri = Galeri::query()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('judul', 'like', '%' . $request->search . '%'))
            ->when($request->sort === 'a-z',     fn ($q) => $q->orderBy('judul'))
            ->when($request->sort === 'z-a',     fn ($q) => $q->orderByDesc('judul'))
            ->when($request->sort === 'terlama', fn ($q) => $q->oldest())
            ->when(! in_array($request->sort, ['a-z', 'z-a', 'terlama']), fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        return view('admin.galeri', compact('galeri'));
    }

public function store(Request $request)
{
    $request->validate([
        'judul'  => 'required|max:255',
        'foto'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|in:publish,draft',
    ]);

    $data = $request->only('judul', 'tanggal', 'status');

    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $nama = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('assets/images/galeri'), $nama);
        $data['foto'] = $nama;
    }

    Galeri::create($data);

    return back()->with('success', 'Foto galeri berhasil ditambahkan.');
}

    public function update(Request $request, Galeri $galeri)
    {
        $request->validate([
            'judul'  => 'required|max:255',
            'foto'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:publish,draft',
        ]);

        $data = $request->only('judul', 'tanggal', 'status');

        if ($request->hasFile('foto')) {
            $lama = public_path('assets/images/galeri/' . $galeri->foto);
            if ($galeri->foto && file_exists($lama)) unlink($lama);

            $file = $request->file('foto');
            $nama = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images/galeri'), $nama);
            $data['foto'] = $nama;
        }

        $galeri->update($data);

        return back()->with('success', 'Foto galeri berhasil diperbarui.');
    }

    public function destroy(Galeri $galeri)
    {
        $path = public_path('assets/images/galeri/' . $galeri->foto);
        if ($galeri->foto && file_exists($path)) unlink($path);

        $galeri->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}