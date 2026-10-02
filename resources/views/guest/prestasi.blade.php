@extends('layouts.guest')

@section('title', 'Prestasi | SMA Negeri 8 Yogyakarta')
@section('hideNavbar', true)

@section('content')

{{-- HEADER --}}
<header class="page-header">
    <div class="container d-flex align-items-center justify-content-between py-4">
        <a href="{{ route('home') }}#prestasi" class="page-back">
            <i class="fa-solid fa-chevron-left me-2"></i>Kembali
        </a>
        <h1 class="page-title mb-0">Prestasi</h1>
    </div>
</header>

{{-- DAFTAR PRESTASI --}}
<main class="container py-5" style="max-width: 1150px;">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        @forelse ($prestasis as $item)
            <div class="col">
                <div class="prestasi-card h-100">
                    <img src="{{ $item->foto_url }}" alt="{{ $item->judul }}" class="prestasi-card-img">
                    <div class="pt-3 px-1">
                        <div class="small text-secondary mb-1">
                            {{ $item->tanggal?->translatedFormat('d M Y') ?? $item->created_at->translatedFormat('d M Y') }}
                        </div>
                        <div class="prestasi-card-title mb-2">{{ $item->judul }}</div>
                        <p class="prestasi-card-text mb-0">{{ $item->penyelenggara }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col"><p class="text-center text-secondary mb-0">Belum ada data prestasi.</p></div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $prestasis->links() }}
    </div>
</main>

@endsection