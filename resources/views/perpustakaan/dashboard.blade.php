@extends('layouts.perpustakaan')

@section('title', 'Dashboard Perpustakaan')

@section('content')

<div class="admin-header-line pb-3 mb-4">
    <h1 class="admin-title">Dashboard</h1>
    <div class="admin-breadcrumb">Dashboard</div>
</div>

{{-- STATISTIK --}}
<div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
    <div class="col">
        <div class="card admin-outline admin-card shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fw-semibold" style="font-size: 13px;">Data Buku</div>
                <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                    <i class="fa-solid fa-users"></i>
                    <span class="fs-4 fw-semibold">{{ $totalBuku }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card admin-outline admin-card shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fw-semibold" style="font-size: 13px;">Peminjaman Buku</div>
                <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                    <i class="fa-solid fa-image"></i>
                    <span class="fs-4 fw-semibold">{{ $totalDipinjam }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card admin-outline admin-card shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fw-semibold" style="font-size: 13px;">Data Peminjam</div>
                <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                    <i class="fa-solid fa-users"></i>
                    <span class="fs-4 fw-semibold">{{ $totalPeminjam }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TOP 10 + TERLAMBAT --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-outline admin-card shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold mb-1">Top 10 Peminjam</h2>
                <div class="text-secondary mb-4">(Bulan ini)</div>

                <div class="admin-scroll pe-2">
                    <div class="d-flex flex-column gap-3">
                        @forelse ($topPeminjam as $item)
                            <div class="admin-item d-flex align-items-center gap-3 p-3">
                                <span class="fw-semibold" style="width: 36px;">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <<div class="fw-semibold small">{{ $item->nama }}</div>
                                    <div class="small text-secondary">({{ $item->kelas }})</div>
                                </div>
                                <span class="chip-buku ms-auto">{{ $item->total }} Buku</span>
                            </div>
                        @empty
                            <p class="text-secondary mb-0">Belum ada peminjaman bulan ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-outline admin-card shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold text-danger mb-1">Terlambat Dikembalikan</h2>
                <div class="text-secondary mb-4">({{ $totalTerlambat }} Buku)</div>

                <div class="admin-scroll pe-2">
                    <div class="d-flex flex-column gap-3">
                        @forelse ($terlambat as $item)
                            <div class="admin-item p-3">
                                <div class="fw-semibold small">{{ $item->buku?->judul ?? '-' }}</div>
                                <div class="small text-danger">Kode : {{ $item->buku?->kode ?? '-' }}</div>
                            </div>
                        @empty
                            <p class="text-secondary mb-0">Tidak ada buku yang terlambat. 🎉</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection