<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $pengumuman = Pengumuman::query()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('judul', 'like', '%' . $request->search . '%'))
            ->when($request->filter === 'terlama', fn ($q) => $q->oldest())
            ->when($request->filter !== 'terlama', fn ($q) => $q->latest())
            ->paginate(8)
            ->withQueryString();

        return view('admin.pengumuman', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'   => 'required|max:255',
            'isi'     => 'required',
            'tanggal' => 'nullable|date',
            'gambar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'  => 'required|in:publish,draft',
        ]);

        $data = $request->only('judul', 'isi', 'tanggal', 'status');

        if ($request->hasFile('gambar')) {
            $folder = public_path('assets/images/pengumuman');
            if (! is_dir($folder)) mkdir($folder, 0777, true);
            $nama = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move($folder, $nama);
            $data['gambar'] = $nama;
        }

        Pengumuman::create($data);
        return back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $request->validate([
            'judul'   => 'required|max:255',
            'isi'     => 'required',
            'tanggal' => 'nullable|date',
            'gambar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'  => 'required|in:publish,draft',
        ]);

        $data = $request->only('judul', 'isi', 'tanggal', 'status');

        if ($request->hasFile('gambar')) {
            $lama = public_path('assets/images/pengumuman/' . $pengumuman->gambar);
            if ($pengumuman->gambar && file_exists($lama)) unlink($lama);

            $nama = time() . '_' . $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move(public_path('assets/images/pengumuman'), $nama);
            $data['gambar'] = $nama;
        }

        $pengumuman->update($data);
        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $path = public_path('assets/images/pengumuman/' . $pengumuman->gambar);
        if ($pengumuman->gambar && file_exists($path)) unlink($path);

        $pengumuman->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}