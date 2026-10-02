@extends('layouts.admin')

@section('title', 'Komentar')

@section('content')

{{-- HEADER --}}
<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Komentar</h1>
        <div class="admin-breadcrumb">Dashboard / Komentar</div>
    </div>
    <form id="formBulk" action="{{ route('admin.komentar.bulk') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="ids[]" id="bulkIds">
    </form>
    <button class="btn btn-primary d-none" id="btnBulk" onclick="hapusTerpilih()">
        <i class="fa-solid fa-trash me-1"></i> Hapus Terpilih (<span id="jumlahPilih">0</span>)
    </button>
</div>

{{-- SEARCH & SORT --}}
<form action="{{ route('admin.komentar.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari komentar..."
                   onchange="this.form.submit()">
        </div>
    </div>
    <div class="col-md-5">
        <select name="sort" class="form-select admin-filter-select" onchange="this.form.submit()">
            <option value="">Urutan berdasarkan</option>
            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
            <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A - Z</option>
        </select>
    </div>
</form>

{{-- TABLE + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive admin-scroll">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="admin-thead">
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" class="form-check-input" id="checkSemua"
                               onchange="toggleSemua(this)">
                    </th>
                    <th>Komentar</th>
                    <th>Rating</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ratings as $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input check-item" value="{{ $item->id }}"
                                   onchange="updateBulkBar()">
                        </td>
                        <td class="text-start" style="max-width: 480px;">
                            <span class="small">&ldquo;{{ \Illuminate\Support\Str::limit($item->komentar, 100) }}&rdquo;</span>
                        </td>
                        <td style="white-space: nowrap;">
                            @for ($j = 0; $j < $item->rating; $j++)
                                <i class="fa-solid fa-star text-warning" style="font-size: 11px;"></i>
                            @endfor
                        </td>
                        <td>{{ $item->created_at->translatedFormat('d M Y') }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        onclick="showKomentar(this)" title="Lihat">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <form action="{{ route('admin.komentar.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
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
                    <tr><td colspan="5" class="py-4 text-secondary">Belum ada komentar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $ratings->firstItem() ?? 0 }} sampai {{ $ratings->lastItem() ?? 0 }} dari {{ $ratings->total() }} data
        </span>
        {{ $ratings->links() }}
    </div>
</div>

{{-- MODAL DETAIL KOMENTAR --}}
<div class="modal fade" id="modalKomentar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h2 class="h6 fw-bold text-uppercase mb-0">Detail Komentar</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="d-flex align-items-center gap-2 mb-3">
                    <span id="komentarRating" class="text-warning small"></span>
                    <span class="small text-secondary" id="komentarTanggal">-</span>
                    <span class="badge rounded-pill badge-admin bg-secondary-subtle text-secondary-emphasis ms-auto">Anonim</span>
                </div>

                <p class="mb-0" style="white-space: pre-wrap;" id="komentarIsi">-</p>
            </div>
            <div class="admin-footer px-4 py-3 d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary bg-white px-5" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showKomentar(btn) {
        const item = JSON.parse(btn.dataset.item);

        document.getElementById('komentarIsi').innerText = item.komentar;
        document.getElementById('komentarTanggal').innerText =
            new Date(item.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

        const bintang = document.getElementById('komentarRating');
        bintang.innerHTML = '';
        for (let i = 0; i < item.rating; i++) {
            bintang.insertAdjacentHTML('beforeend', '<i class="fa-solid fa-star"></i>');
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKomentar')).show();
    }

    function toggleSemua(cb) {
        document.querySelectorAll('.check-item').forEach(c => c.checked = cb.checked);
        updateBulkBar();
    }

    function updateBulkBar() {
        const terpilih = document.querySelectorAll('.check-item:checked').length;
        document.getElementById('jumlahPilih').innerText = terpilih;
        document.getElementById('btnBulk').classList.toggle('d-none', terpilih === 0);
    }

    function hapusTerpilih() {
        if (!confirm('Yakin ingin menghapus komentar yang dipilih?')) return;

        const ids = [...document.querySelectorAll('.check-item:checked')].map(c => c.value);
        const form = document.getElementById('formBulk');
        const input = document.getElementById('bulkIds');

        input.value = '';
        ids.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'ids[]';
            hidden.value = id;
            form.appendChild(hidden);
        });

        form.submit();
    }
</script>

@endsection