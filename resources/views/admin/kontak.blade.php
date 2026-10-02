@extends('layouts.admin')

@section('title', 'Kontak')

@section('content')

{{-- HEADER --}}
<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Kontak</h1>
        <div class="admin-breadcrumb">Dashboard / Kontak</div>
    </div>
</div>

{{-- SEARCH & SORT --}}
<form action="{{ route('admin.kontak.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari pesan..."
                   onchange="this.form.submit()">
        </div>
    </div>
    <div class="col-md-5">
        <select name="sort" class="form-select admin-filter-select" onchange="this.form.submit()">
            <option value="">Urutan berdasarkan</option>
            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
        </select>
    </div>
</form>

{{-- TABLE + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive admin-scroll">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="admin-thead">
                <tr>
                    <th>Pesan Masuk</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kontaks as $item)
                    <tr>
                        <td>{{ $item->email }}</td>
                        <td>
                            <span class="dot-status {{ $item->dibalas_at ? 'hijau' : 'kuning' }}"
                                  title="{{ $item->dibalas_at ? 'Sudah dibalas' : 'Belum dibalas' }}"></span>
                        </td>
                        <td>{{ $item->created_at->translatedFormat('d M Y') }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        onclick="showDetail(this)" title="Lihat">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        onclick="openReply(this)" title="Balas">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                                <form action="{{ route('admin.kontak.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-secondary">Belum ada pesan masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $kontaks->firstItem() ?? 0 }} sampai {{ $kontaks->lastItem() ?? 0 }} dari {{ $kontaks->total() }} data
        </span>
        {{ $kontaks->links() }}
    </div>
</div>

{{-- DETAIL PESAN --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-4 position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>

        <h2 class="h5 fw-bold mb-4">Pesan Masuk</h2>

        <table class="table table-borderless table-sm mb-0">
            <tbody>
                <tr><td class="text-secondary" style="width: 25%;">Nama</td><td id="detailNama" class="fw-semibold">-</td></tr>
                <tr><td class="text-secondary">Email</td><td id="detailEmail">-</td></tr>
                <tr><td class="text-secondary">Subjek</td><td id="detailSubjek">-</td></tr>
                <tr><td class="text-secondary">Pesan</td><td id="detailPesan" style="white-space: pre-wrap;">-</td></tr>
                <tr><td class="text-secondary">Status</td><td id="detailStatus">-</td></tr>
            </tbody>
        </table>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-center gap-3 px-4 py-3">
        <form id="formDelete" action="" method="POST"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger px-4">Hapus</button>
        </form>
        <button class="btn btn-primary px-4" onclick="openReplyFromDetail()">Balas Pesan</button>
        <button class="btn btn-outline-secondary bg-white px-4" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- MODAL BALAS PESAN --}}
<div class="modal fade" id="modalBalas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formBalas" method="POST" class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h2 class="h5 fw-bold text-uppercase mb-0">Balas Pesan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <p class="small text-secondary mb-3">
                    Dikirim ke: <strong id="balasEmail">-</strong>
                </p>

                <textarea name="balasan" rows="5" required
                          class="form-control bg-body-tertiary border-0 mb-2 py-2"
                          placeholder="Tulis balasan..."></textarea>

                <small class="text-secondary">Balasan akan dikirim sebagai email ke pengirim.</small>
            </div>
            <div class="admin-footer px-4 py-3 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary px-5" style="min-width: 280px;">Kirim Balasan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let current = null;

    function showDetail(btn) {
        current = JSON.parse(btn.dataset.item);

        document.getElementById('detailNama').innerText   = current.nama;
        document.getElementById('detailEmail').innerText  = current.email;
        document.getElementById('detailSubjek').innerText = current.subjek;
        document.getElementById('detailPesan').innerText  = current.pesan;
        document.getElementById('detailStatus').innerText =
            current.dibalas_at ? 'Sudah dibalas' : 'Belum dibalas';

        document.getElementById('formDelete').action = `/admin/kontak/${current.id}`;

        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function openReply(btn) {
        current = JSON.parse(btn.dataset.item);
        bukaModalBalas();
    }

    function openReplyFromDetail() {
        if (current) bukaModalBalas();
    }

    function bukaModalBalas() {
        document.getElementById('balasEmail').innerText = current.email;
        document.getElementById('formBalas').action = `/admin/kontak/${current.id}/balas`;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalBalas')).show();
    }
</script>

@endsection