<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BalasKontakMail;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class KontakController extends Controller
{
    public function index(Request $request)
    {
        $kontaks = Kontak::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $cari = $request->search;
                $q->where(fn ($w) => $w
                    ->where('nama', 'like', "%{$cari}%")
                    ->orWhere('email', 'like', "%{$cari}%")
                    ->orWhere('subjek', 'like', "%{$cari}%"));
            })
            ->when($request->sort === 'terlama', fn ($q) => $q->oldest())
            ->when($request->sort !== 'terlama', fn ($q) => $q->latest())
            ->paginate(8)
            ->withQueryString();

        return view('admin.kontak', compact('kontaks'));
    }

    public function balas(Request $request, Kontak $kontak)
    {
        $data = $request->validate(['balasan' => 'required|string|max:3000']);

        try {
            Mail::to($kontak->email)->send(new BalasKontakMail($kontak, $data['balasan']));
        } catch (\Throwable $e) {
            // Email gagal terkirim (koneksi/SMTP bermasalah).
            // Status TIDAK diubah agar admin bisa mencoba mengirim ulang.
            return back()
                ->withInput()
                ->with('error', 'Gagal mengirim email balasan. Periksa koneksi internet, lalu coba lagi.');
        }

        $kontak->update(['dibalas_at' => now()]);

        return redirect()->route('admin.kontak.index')
            ->with('success', 'Balasan berhasil dikirim ke ' . $kontak->email . '.');
    }

    public function destroy(Kontak $kontak)
    {
        $kontak->delete();

        return back()->with('success', 'Pesan berhasil dihapus.');
    }
}