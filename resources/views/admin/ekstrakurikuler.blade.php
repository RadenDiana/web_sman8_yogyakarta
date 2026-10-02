@extends('layouts.admin')

@section('title', 'Ekstrakurikuler')

@section('content')

{{-- HEADER --}}
<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Ekstrakurikuler</h1>
        <div class="admin-breadcrumb">Dashboard / Ekstrakurikuler</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Ekstrakurikuler
    </button>
</div>

{{-- SEARCH & FILTER --}}
<form action="{{ route('admin.ekstrakurikuler.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari ekstrakurikuler..."
                   onchange="this.form.submit()">
        </div>
    </div>
    <div class="col-md-5">
        <select name="sort" class="form-select admin-filter-select" onchange="this.form.submit()">
            <option value="">Urutan berdasarkan</option>
            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
            <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A - Z</option>
            <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z - A</option>
        </select>
    </div>
</form>

{{-- GRID + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-body p-4 admin-scroll">
        <div class="row row-cols-2 row-cols-md-5 g-3">
            @forelse ($ekstrakurikuler as $item)
                <div class="col">
                    <button type="button"
                            class="admin-pill w-100 h-100"
                            data-item="{{ json_encode($item) }}"
                            onclick="selectEkstra(this)">
                        {{ $item->nama }}
                    </button>
                </div>
            @empty
                <div class="col"><p class="text-secondary mb-0">Belum ada data ekstrakurikuler.</p></div>
            @endforelse
        </div>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $ekstrakurikuler->firstItem() ?? 0 }} sampai {{ $ekstrakurikuler->lastItem() ?? 0 }} dari {{ $ekstrakurikuler->total() }} data
        </span>
        {{ $ekstrakurikuler->links() }}
    </div>
</div>

{{-- DETAIL (muncul saat pill diklik) --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-5 position-relative text-center">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>
        <h2 class="fw-bold display-6 mb-0" id="detailTitle">-</h2>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-center gap-3 px-4 py-3">
        <form id="formDelete" action="" method="POST"
              onsubmit="return confirm('Yakin ingin menghapus ekstrakurikuler ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger px-4">Hapus</button>
        </form>
        <button class="btn btn-primary px-4" onclick="prepareEdit()">Edit</button>
        <button class="btn btn-outline-secondary bg-white px-4" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT (desain EKSTRAKURIKULER) --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">EKSTRAKURIKULER</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <input type="text" name="nama" id="inputNama" required
                       class="form-control bg-body-tertiary border-0 py-3"
                       placeholder="English Study Club">
            </div>

            <div class="admin-footer px-4 py-3 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary px-5" style="min-width: 280px;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const form = document.getElementById('formMain');
    let current = null;

    function getModal() {
        return bootstrap.Modal.getOrCreateInstance(document.getElementById('modalForm'));
    }

    function selectEkstra(btn) {
        document.querySelectorAll('.admin-pill').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');

        current = JSON.parse(btn.dataset.item);
        document.getElementById('detailTitle').innerText = current.nama;
        document.getElementById('formDelete').action = `/admin/ekstrakurikuler/${current.id}`;

        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function prepareCreate() {
        document.getElementById('modalTitle').innerText = 'EKSTRAKURIKULER';
        form.action = "{{ route('admin.ekstrakurikuler.store') }}";
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        getModal().show();
    }

    function prepareEdit() {
        if (!current) return;
        document.getElementById('modalTitle').innerText = 'EKSTRAKURIKULER';
        form.action = `/admin/ekstrakurikuler/${current.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('inputNama').value = current.nama ?? '';
        getModal().show();
    }
</script>

@endsection