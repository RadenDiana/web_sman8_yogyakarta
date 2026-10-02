@extends('layouts.admin')

@section('title', 'Galeri')

@section('content')

{{-- HEADER --}}
<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Galeri</h1>
        <div class="admin-breadcrumb">Dashboard / Galeri</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Galeri
    </button>
</div>

{{-- SEARCH & SORT --}}
<form action="{{ route('admin.galeri.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari galeri..."
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
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @forelse ($galeri as $item)
                <div class="col">
                    <button type="button"
                            class="btn p-0 w-100 border-0"
                            data-item="{{ json_encode($item) }}"
                            onclick="showDetail(this)">
                        <img src="{{ asset('assets/images/galeri/' . $item->foto) }}"
                             class="rounded-3 shadow-sm w-100 object-fit-cover"
                             style="height: 115px;" alt="{{ $item->judul }}">
                    </button>
                </div>
            @empty
                <div class="col"><p class="text-secondary mb-0">Tidak ada data galeri.</p></div>
            @endforelse
        </div>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $galeri->firstItem() ?? 0 }} sampai {{ $galeri->lastItem() ?? 0 }} dari {{ $galeri->total() }} data
        </span>
        {{ $galeri->links() }}
    </div>
</div>

{{-- DETAIL --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-4 position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>

        <div class="d-flex flex-column flex-md-row align-items-md-start gap-4">
            <div class="text-center flex-shrink-0">
                <img id="detailImage" src="" class="rounded-3"
                    style="width: 140px; height: 140px; object-fit: cover;" alt="Galeri">
                <div class="small text-secondary mt-2">Dibuat oleh Admin</div>
            </div>
            <div class="flex-grow-1">
                <h2 class="h4 fw-bold" id="detailTitle">-</h2>
                <div class="d-flex align-items-center gap-2 mt-3">
                    <span class="small text-secondary" id="detailDate">-</span>
                    <span class="badge rounded-pill badge-admin" id="detailStatus">-</span>
                </div>
            </div>
        </div>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-center gap-3 px-4 py-3">
        <form id="formDelete" action="" method="POST"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus galeri ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger px-4">Hapus</button>
        </form>
        <button class="btn btn-primary px-4" onclick="prepareEditFromDetail()">Edit</button>
        <button class="btn btn-outline-secondary bg-white px-4" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT (desain DATA GALERI) --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" enctype="multipart/form-data"
              class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="status" id="inputStatus" value="publish">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">Tambah Galeri</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="row g-4 align-items-start">
                    <div class="col-md-5 text-center">
                        <img id="modalPreview" src="{{ asset('assets/images/sekolah/logo.png') }}"
                             class="rounded-3 border shadow-sm mb-3"
                             style="width: 150px; height: 150px; object-fit: cover;" alt="Foto">
                        <input type="file" name="foto" id="inputFoto"
                               class="form-control form-control-sm" accept="image/*"
                               onchange="previewGambar(this)">
                    </div>

                    <div class="col-md-7">
                        <input type="text" name="judul" id="inputJudul" required
                               class="form-control bg-body-tertiary border-0 mb-3 py-2"
                               placeholder="Judul Galeri">

                        <input type="date" name="tanggal" id="inputTanggal"
                               class="form-control bg-body-tertiary border-0 mb-3 py-2">

                        <div class="d-flex gap-2">
                            <button type="button" id="btnPublish" class="btn rounded-3 px-4"
                                    onclick="setStatus('publish')">Publish</button>
                            <button type="button" id="btnDraf" class="btn rounded-3 px-4"
                                    onclick="setStatus('draft')">Draf</button>
                        </div>
                    </div>
                </div>
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

    function fotoPreview(item) {
    return item.foto
        ? '{{ asset("assets/images/galeri") }}/' + item.foto
        : '{{ asset("assets/images/sekolah/logo.png") }}';
}

    function previewGambar(input) {
        if (input.files && input.files[0]) {
            document.getElementById('modalPreview').src = URL.createObjectURL(input.files[0]);
        }
    }

    function setStatus(status) {
        document.getElementById('inputStatus').value = status;
        document.getElementById('btnPublish').className = 'btn rounded-3 px-4 ' +
            (status === 'publish' ? 'bg-success-subtle text-success-emphasis' : 'btn-light border');
        document.getElementById('btnDraf').className = 'btn rounded-3 px-4 ' +
            (status === 'draft' ? 'btn-warning' : 'btn-light border');
    }

    function formatTanggal(item) {
        const raw = (item.tanggal || item.created_at || '').slice(0, 10);
        return new Date(raw).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function showDetail(btn) {
        current = JSON.parse(btn.dataset.item);

        document.getElementById('detailImage').src = fotoPreview(current);
        document.getElementById('detailTitle').innerText = current.judul;
        document.getElementById('detailDate').innerText  = formatTanggal(current);

        const badge = document.getElementById('detailStatus');
        badge.textContent = current.status === 'publish' ? 'Publish' : 'Draft';
        badge.className = 'badge rounded-pill badge-admin ' +
            (current.status === 'publish' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis');

        document.getElementById('formDelete').action = `/admin/galeri/${current.id}`;
        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function prepareCreate() {
    document.getElementById('modalTitle').innerText = 'Tambah Galeri';
    form.action = "{{ route('admin.galeri.store') }}";
    document.getElementById('formMethod').value = 'POST';
    form.reset();
    setStatus('publish');
    document.getElementById('modalPreview').src = '{{ asset("assets/images/sekolah/logo.png") }}';
    getModal().show();
}

    function prepareEdit(btn) {
        current = JSON.parse(btn.dataset.item);
        openEditModal();
    }

    function prepareEditFromDetail() {
        if (current) openEditModal();
    }

    function openEditModal() {
    document.getElementById('modalTitle').innerText = 'Data Galeri';
    form.action = `/admin/galeri/${current.id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputJudul').value   = current.judul;
    document.getElementById('inputTanggal').value = (current.tanggal || current.created_at || '').slice(0, 10);
    setStatus(current.status);
    document.getElementById('modalPreview').src = fotoPreview(current);
    getModal().show();
}
</script>

@endsection