<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Galeri;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Galeri::where('status', 'publish')
            ->latest()
            ->paginate(8);

        return view('guest.galeri', compact('galeri'));
    }
}