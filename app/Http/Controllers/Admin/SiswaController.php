<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $siswas = Siswa::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $cari = $request->search;
                $q->where(function ($w) use ($cari) {
                    $w->where('nama', 'like', "%{$cari}%")
                      ->orWhere('nis', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('kelas'), fn ($q) => $q->where('kelas', $request->kelas))
            ->orderBy('nama')
            ->paginate(8) // sesuai desain: 5 baris per halaman
            ->withQueryString();

        $daftarKelas = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('admin.siswa', compact('siswas', 'daftarKelas'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) $data['foto'] = $this->simpanFoto($request);
        $data['status'] = $request->boolean('status');

        Siswa::create($data);

        return back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $data = $this->validasi($request, $siswa->id);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($siswa->foto);
            $data['foto'] = $this->simpanFoto($request);
        }
        $data['status'] = $request->boolean('status');

        $siswa->update($data);

        return back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $this->hapusFoto($siswa->foto);
        $siswa->delete();

        return back()->with('success', 'Data siswa berhasil dihapus.');
    }

    /* ---------- helper ---------- */

    private function validasi(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nama'          => 'required|string|max:100',
            'nis'           => 'required|string|max:20|unique:siswas,nis' . ($id ? ",{$id}" : ''),
            'kelas'         => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:P,L',
            'agama'         => 'required|string|max:20',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string|max:255',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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