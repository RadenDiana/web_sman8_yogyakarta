@extends('layouts.guest')

@section('title', 'Galeri | SMA Negeri 8 Yogyakarta')
@section('hideNavbar', true)

@section('content')

{{-- HEADER --}}
<header class="page-header">
    <div class="container d-flex align-items-center justify-content-between py-4">
        <a href="{{ route('home') }}#galeri" class="page-back">
            <i class="fa-solid fa-chevron-left me-2"></i>Kembali
        </a>
        <h1 class="page-title mb-0">Galeri</h1>
    </div>
</header>

{{-- DAFTAR GALERI --}}
<main class="container py-5" style="max-width: 1150px;">
    <div class="d-flex flex-column gap-4">
        @forelse ($galeri as $item)
            <article class="gallery-card d-flex flex-column flex-md-row align-items-center {{ $loop->odd ? 'flex-md-row-reverse' : '' }}">
                <div class="gallery-img flex-shrink-0">
                    <img src="{{ $item->foto_url }}" alt="{{ $item->judul }}">
                         alt="{{ $item->judul }}">
                </div>

                <div class="flex-grow-1 {{ $loop->odd ? 'text-md-end' : '' }}">
                    <div class="gallery-date">
                        <span class="gallery-day">{{ $item->created_at->format('d') }}</span>
                        <span class="gallery-month">{{ $item->created_at->translatedFormat('M Y') }}</span>
                    </div>

                    <h2 class="gallery-title mb-0">{{ $item->judul }}</h2>
                </div>
            </article>
        @empty
            <p class="text-center text-secondary mb-0">Belum ada galeri yang ditambahkan.</p>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $galeri->links() }}
    </div>
</main>

@endsection