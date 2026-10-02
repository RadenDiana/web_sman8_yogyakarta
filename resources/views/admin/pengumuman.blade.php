@extends('layouts.admin')

@section('title', 'Pengumuman')

@section('content')

{{-- HEADER --}}
<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Pengumuman</h1>
        <div class="admin-breadcrumb">Dashboard / Pengumuman</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Pengumuman
    </button>
</div>

{{-- SEARCH & FILTER --}}
<form action="{{ route('admin.pengumuman.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari pengumuman..."
                   onchange="this.form.submit()">
        </div>
    </div>
    <div class="col-md-5">
        <select name="filter" class="form-select admin-filter-select" onchange="this.form.submit()">
            <option value="terbaru" {{ request('filter') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ request('filter') == 'terlama' ? 'selected' : '' }}>Terlama</option>
        </select>
    </div>
</form>

{{-- TABLE + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive admin-scroll">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="admin-thead">
                <tr>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengumuman as $item)
                    <tr>
                        <td>{{ $item->judul }}</td>
                        <td>{{ ($item->tanggal ?? $item->created_at)->translatedFormat('d M Y') }}</td>
                        <td>
                            <span class="badge rounded-pill badge-admin {{ $item->status === 'publish' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        onclick="showDetail(this)" title="Lihat">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        onclick="prepareEdit(this)" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <form action="{{ route('admin.pengumuman.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                    <tr><td colspan="4" class="py-4 text-secondary">Data pengumuman tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $pengumuman->firstItem() ?? 0 }} sampai {{ $pengumuman->lastItem() ?? 0 }} dari {{ $pengumuman->total() }} data
        </span>
        {{ $pengumuman->links() }}
    </div>
</div>

{{-- DETAIL (dibuka tombol lihat) --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-4 position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>

        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <div class="text-center">
                <img id="detailImage" src="{{ asset('assets/images/sekolah/logo.png') }}"
                     class="rounded-3" style="width: 140px; height: 140px; object-fit: cover;" alt="Gambar">
                <div class="small text-secondary mt-2">Dibuat oleh Admin</div>
            </div>
            <div class="flex-grow-1">
                <h2 class="h4 fw-bold text-break" id="detailTitle">-</h2>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="small text-secondary" id="detailDate">-</span>
                    <span class="badge rounded-pill badge-admin" id="detailStatus">-</span>
                </div>
                <p class="small mb-0 text-break" id="detailText">-</p>
            </div>
        </div>
    </div>
    <div class="admin-footer d-flex justify-content-center gap-3 px-4 py-3">
        <button class="btn btn-primary px-4" onclick="prepareEditFromDetail()">Edit</button>
        <button class="btn btn-outline-secondary bg-white px-4" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT (style galeri) --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" enctype="multipart/form-data"
              class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="status" id="inputStatus" value="publish">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">Tambah Pengumuman</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="row g-4 align-items-start">
                    <div class="col-md-5 text-center">
                        <img id="modalPreview" src="{{ asset('assets/images/sekolah/logo.png') }}"
                             class="rounded-3 border shadow-sm mb-3"
                             style="width: 150px; height: 150px; object-fit: cover;" alt="Gambar">
                        <input type="file" name="gambar" id="inputGambar"
                               class="form-control form-control-sm" accept="image/*"
                               onchange="previewGambar(this)">
                    </div>

                    <div class="col-md-7">
                        <input type="text" name="judul" id="inputJudul" required
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Judul Pengumuman">

                        <input type="date" name="tanggal" id="inputTanggal"
                               class="form-control bg-body-tertiary border-0 mb-2 py-2">

                        <textarea name="isi" id="inputIsi" rows="4" required
                                  class="form-control bg-body-tertiary border-0 mb-3 py-2"
                                  placeholder="Isi pengumuman"></textarea>

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

    function formatTanggal(item) {
        const raw = (item.tanggal || item.created_at || '').slice(0, 10);
        return new Date(raw).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function gambarPreview(item) {
        return item.gambar
            ? '{{ asset("assets/images/pengumuman") }}/' + item.gambar
            : '{{ asset("assets/images/sekolah/logo.png") }}';
    }

   function setStatus(status) {
    document.getElementById('inputStatus').value = status;
    document.getElementById('btnPublish').className = 'btn rounded-3 px-4 ' +
        (status === 'publish' ? 'bg-success-subtle text-success-emphasis' : 'btn-light border');
    document.getElementById('btnDraf').className = 'btn rounded-3 px-4 ' +
        (status === 'draft' ? 'btn-warning' : 'btn-light border');
}

    function previewGambar(input) {
        if (input.files && input.files[0]) {
            document.getElementById('modalPreview').src = URL.createObjectURL(input.files[0]);
        }
    }

    function setStatusBadge(status) {
        const badge = document.getElementById('detailStatus');
        badge.textContent = status === 'publish' ? 'Publish' : 'Draft';
        badge.className = 'badge rounded-pill badge-admin ' +
            (status === 'publish' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis');
    }

    function showDetail(btn) {
        current = JSON.parse(btn.dataset.item);

        document.getElementById('detailImage').src = gambarPreview(current);
        document.getElementById('detailTitle').innerText = current.judul;
        document.getElementById('detailDate').innerText = formatTanggal(current);
        setStatusBadge(current.status);
        document.getElementById('detailText').innerText = current.isi;

        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function prepareCreate() {
        document.getElementById('modalTitle').innerText = 'Tambah Pengumuman';
        form.action = "{{ route('admin.pengumuman.store') }}";
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        setStatus('publish');
        document.getElementById('modalPreview').src = '{{ asset("assets/images/sekolah/logo.png") }}';
        getModal().show();
    }

    function fillForm(item) {
        document.getElementById('modalTitle').innerText = 'Data Pengumuman';
        form.action = `/admin/pengumuman/${item.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('inputJudul').value   = item.judul;
        document.getElementById('inputIsi').value     = item.isi;
        document.getElementById('inputTanggal').value = (item.tanggal || item.created_at || '').slice(0, 10);
        setStatus(item.status);
        document.getElementById('modalPreview').src = gambarPreview(item);
    }

    function prepareEdit(btn) {
        current = JSON.parse(btn.dataset.item);
        fillForm(current);
        getModal().show();
    }

    function prepareEditFromDetail() {
        if (current) {
            fillForm(current);
            getModal().show();
        }
    }
</script>

@endsection