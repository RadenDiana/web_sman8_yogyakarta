<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KontakController extends Controller
{
    public function store(Request $request)
    {
        $request->validateWithBag('kontak', [
            'nama'   => 'required|string|max:100',
            'email'  => 'required|email|max:255',
            'subjek' => 'required|string|max:255',
            'pesan'  => 'required|string|max:2000',
        ]);

        // Pesan WAJIB tersimpan dulu ke dashboard admin, apa pun hasilnya
        Kontak::create($request->only('nama', 'email', 'subjek', 'pesan'));

        // Notifikasi email ke admin (email sekolah dari MAIL_FROM_ADDRESS)
        try {
            Mail::raw(
                "Pesan baru masuk dari website SMA Negeri 8 Yogyakarta.\n\n"
                . "Nama    : {$request->nama}\n"
                . "Email   : {$request->email}\n"
                . "Subjek  : {$request->subjek}\n\n"
                . "Pesan:\n{$request->pesan}\n\n"
                . "Balas pesan ini melalui dashboard admin: " . route('admin.kontak.index'),
            function ($message) {
                $message->subject('Pesan Baru dari Website | SMA Negeri 8 Yogyakarta');
                $message->to(config('mail.from.address'));
            });
        } catch (\Throwable $e) {
            // Notifikasi gagal (misal: offline) — pesan tetap aman, admin
            // tetap bisa melihatnya lewat dashboard. Catat ke log saja.
            Log::warning('Gagal kirim notifikasi pesan kontak: ' . $e->getMessage());
        }

        return redirect(route('home') . '#kontak')->with('pesanTerkirim', true);
    }
}