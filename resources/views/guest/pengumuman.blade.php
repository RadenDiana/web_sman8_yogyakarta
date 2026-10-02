@extends('layouts.guest')

@section('title', 'Pengumuman | SMA Negeri 8 Yogyakarta')
@section('hideNavbar', true)

@section('content')

{{-- HEADER --}}
<header class="page-header">
    <div class="container d-flex align-items-center justify-content-between py-4">
        <a href="{{ route('home') }}#pengumuman" class="page-back">
            <i class="fa-solid fa-chevron-left me-2"></i>Kembali
        </a>
        <h1 class="page-title mb-0">Pengumuman</h1>
    </div>
</header>

{{-- DAFTAR PENGUMUMAN --}}
<main class="container py-5">
    <div class="row g-4">
        @forelse ($pengumuman as $item)
            <div class="col-md-3">
                <article class="announce-card h-100"
                         style="cursor: pointer;"
                         data-item="{{ json_encode($item) }}"
                         onclick="showDetail(this)">
                    <img src="{{ $item->gambar
                            ? asset('assets/images/pengumuman/' . $item->gambar)
                            : asset('assets/images/sekolah/logo.png') }}"
                         class="announce-card-img {{ $item->gambar ? '' : 'obj-contain' }}"
                         alt="{{ $item->judul }}">

                    <div class="px-4 pb-4 pt-2">
                        <span class="announce-date d-block mb-2">
                            {{ ($item->tanggal ?? $item->created_at)->translatedFormat('d F Y') }}
                        </span>
                        <h2 class="announce-title mb-2">{{ $item->judul }}</h2>
                        <p class="announce-text mb-0">
                            &ldquo;{{ Str::limit($item->isi, 100) }}&rdquo;
                        </p>
                    </div>
                </article>
            </div>
        @empty
            <p class="text-center text-secondary mb-0">Belum ada pengumuman terbaru.</p>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $pengumuman->links() }}
    </div>
</main>

{{-- MODAL DETAIL PENGUMUMAN --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-announcement">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h2 class="h6 fw-bold text-uppercase mb-0" id="mdTitle">-</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="row g-3">
                    <div class="col-5 text-center">
                        <img id="mdImage" src="" class="rounded-3 border"
                             style="width: 100%; height: 130px; object-fit: cover;" alt="Gambar">
                        <div class="small text-secondary mt-2">Dibuat oleh Admin</div>
                    </div>

                    <div class="col-7">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="small text-secondary" id="mdDate">-</span>
                            <span class="badge rounded-pill badge-admin" id="mdStatus">-</span>
                        </div>
                        <p class="announce-text mb-0 text-break" id="mdText">-</p>
                    </div>
                </div>
            </div>
            <div class="bg-body-tertiary px-4 py-3 d-flex justify-content-center">
                <button class="btn btn-outline-secondary bg-white px-5" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(card) {
        const item = JSON.parse(card.dataset.item);

        document.getElementById('mdImage').src = item.gambar
            ? '{{ asset("assets/images/pengumuman") }}/' + item.gambar
            : '{{ asset("assets/images/sekolah/logo.png") }}';
        document.getElementById('mdTitle').innerText  = item.judul;
        document.getElementById('mdText').innerText   = item.isi;
        document.getElementById('mdDate').innerText   = new Date(item.tanggal || item.created_at)
            .toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

        const badge = document.getElementById('mdStatus');
        badge.textContent = item.status === 'publish' ? 'Publish' : 'Draft';
        badge.className = 'badge rounded-pill badge-admin ' +
            (item.status === 'publish' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis');

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetail')).show();
    }
</script>

@endsection