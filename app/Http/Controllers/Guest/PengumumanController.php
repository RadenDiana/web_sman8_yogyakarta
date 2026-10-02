<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::where('status', 'publish')
            ->latest()
            ->paginate(8) 
            ->withQueryString();

        return view('guest.pengumuman', [
            'title' => 'Pengumuman Terbaru',
            'pengumuman' => $pengumuman,
            'newsPage' => true,
        ]);
    }
}