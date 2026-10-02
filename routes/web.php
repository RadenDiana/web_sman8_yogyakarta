<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Guest\LoginController;
use App\Http\Controllers\Guest\GaleriController as GuestGaleriController;
use App\Http\Controllers\Guest\PengumumanController as GuestPengumumanController;
use App\Http\Controllers\Guest\PrestasiController as GuestPrestasiController;
use App\Http\Controllers\Guest\TopSiswaController as GuestTopSiswaController;
use App\Http\Controllers\Guest\KontakController as GuestKontakController;
use App\Http\Controllers\Guest\RatingController as GuestRatingController;

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EkstrakurikulerController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\PrestasiController as AdminPrestasiController;
use App\Http\Controllers\Admin\KontakController as AdminKontakController;
use App\Http\Controllers\Admin\KomentarController as AdminKomentarController;

use App\Http\Controllers\Perpustakaan\DashboardController as PerpusDashboardController;
use App\Http\Controllers\Perpustakaan\BukuController;
use App\Http\Controllers\Perpustakaan\PeminjamanController;
use App\Http\Controllers\Perpustakaan\PeminjamController;

/* ================================
   Guest / Public
================================ */

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pengumuman', [GuestPengumumanController::class, 'index'])->name('guest.pengumuman');
Route::get('/galeri', [GuestGaleriController::class, 'index'])->name('guest.galeri');
Route::get('/prestasi', [GuestPrestasiController::class, 'index'])->name('guest.prestasi');
Route::get('/top-siswa', [GuestTopSiswaController::class, 'index'])->name('guest.top-siswa');
Route::post('/kontak', [GuestKontakController::class, 'store'])->name('guest.kontak.store');
Route::post('/rating', [GuestRatingController::class, 'store'])->name('guest.rating.store');

/* ================================
   Authentication
================================ */

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::post('/lupa-password', [LoginController::class, 'resetPassword'])->name('password.reset');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/* ================================
   Admin (wajib login)
================================ */

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('ekstrakurikuler', EkstrakurikulerController::class)
            ->except(['create', 'edit', 'show']);
        
        Route::resource('siswa', AdminSiswaController::class)
            ->except(['create', 'edit', 'show']);

        Route::resource('prestasi', AdminPrestasiController::class)
            ->except(['create', 'edit', 'show']);

        Route::resource('pengumuman', AdminPengumumanController::class)
            ->except(['create', 'edit', 'show']);

        Route::resource('galeri', AdminGaleriController::class)
            ->except(['create', 'edit', 'show']);

        // Akun — route manual agar binding cocok dengan `User $user` di controller
        Route::get('akun', [AkunController::class, 'index'])->name('akun.index');
        Route::post('akun', [AkunController::class, 'store'])->name('akun.store');
        Route::put('akun/{user}', [AkunController::class, 'update'])->name('akun.update');
        Route::delete('akun/{user}', [AkunController::class, 'destroy'])->name('akun.destroy');

        // Kontak — pesan masuk dari home
        Route::get('kontak', [AdminKontakController::class, 'index'])->name('kontak.index');
        Route::post('kontak/{kontak}/balas', [AdminKontakController::class, 'balas'])->name('kontak.balas');
        Route::delete('kontak/{kontak}', [AdminKontakController::class, 'destroy'])->name('kontak.destroy');

        Route::get('komentar', [AdminKomentarController::class, 'index'])->name('komentar.index');
        Route::delete('komentar/{rating}', [AdminKomentarController::class, 'destroy'])->name('komentar.destroy');
        Route::post('komentar/bulk', [AdminKomentarController::class, 'bulkDestroy'])->name('komentar.bulk');
    });

/* ================================
   Perpustakaan (role: perpustakaan)
================================ */

Route::prefix('perpustakaan')
    ->name('perpustakaan.')
    ->middleware(['auth', 'role:perpustakaan'])
    ->group(function () {

        Route::get('/dashboard', [PerpusDashboardController::class, 'index'])->name('dashboard');

        Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
        Route::post('buku', [BukuController::class, 'store'])->name('buku.store');
        Route::put('buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');

        Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::put('peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
        Route::post('peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
        Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

        Route::get('peminjam', [PeminjamController::class, 'index'])->name('peminjam.index');
        Route::post('peminjam', [PeminjamController::class, 'store'])->name('peminjam.store');
        Route::put('peminjam/{peminjam}', [PeminjamController::class, 'update'])->name('peminjam.update');
        Route::delete('peminjam/{peminjam}', [PeminjamController::class, 'destroy'])->name('peminjam.destroy');
    });