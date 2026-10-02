@extends('layouts.guest')

@section('title', 'Top Siswa Perpustakaan | SMA Negeri 8 Yogyakarta')
@section('hideNavbar', true)

@section('content')

{{-- HEADER --}}
<header class="page-header">
    <div class="container d-flex align-items-center justify-content-between py-4">
        <a href="{{ route('home') }}#top-siswa" class="page-back">
            <i class="fa-solid fa-chevron-left me-2"></i>Kembali
        </a>
        <h1 class="page-title mb-0">Top Siswa Perpustakaan</h1>
    </div>
</header>

{{-- DAFTAR TOP SISWA --}}
<main class="container py-5 top-siswa-page" style="max-width: 1150px;">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        @forelse ($topSiswa as $item)
            <div class="col">
                <div class="top-siswa-card h-100">
                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                    <div class="py-3 px-2 text-center">
                        <div class="small text-secondary">{{ $item->kelas }}</div>
                        <div class="fw-bold text-uppercase">{{ $item->nama }}</div>
                        <div class="small text-secondary mt-1">{{ $item->total_pinjam }} Buku Telah Dipinjam</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-center text-secondary mb-0">Belum ada data peminjaman.</p></div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $topSiswa->links() }}
    </div>
</main>

@endsection