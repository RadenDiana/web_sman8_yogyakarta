@extends('layouts.perpustakaan')

@section('title', 'Data Buku')

@section('content')

<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Data Buku</h1>
        <div class="admin-breadcrumb">Dashboard / Data Buku</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Buku
    </button>
</div>

{{-- SEARCH & SORT --}}
<form action="{{ route('perpustakaan.buku.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari Buku..."
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

{{-- TABLE + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive admin-scroll">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="admin-thead">
                <tr>
                    <th>Kode</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bukus as $item)
                    <tr>
                        <td>{{ $item->kode }}</td>
                        <td>{{ $item->judul }}</td>
                        <td>{{ $item->pengarang ?? '-' }}</td>
                        <td>{{ $item->penerbit ?? '-' }}</td>
                        <td>{{ $item->stok }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        data-dipinjam="{{ $item->dipinjam_count }}"
                                        onclick="showDetail(this)" title="Lihat">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        onclick="prepareEdit(this)" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <form action="{{ route('perpustakaan.buku.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
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
                    <tr><td colspan="6" class="py-4 text-secondary">Data buku tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $bukus->firstItem() ?? 0 }} sampai {{ $bukus->lastItem() ?? 0 }} dari {{ $bukus->total() }} data
        </span>
        {{ $bukus->links() }}
    </div>
</div>

{{-- DETAIL --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-4 position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>

        <div class="d-flex flex-column flex-md-row align-items-md-start gap-4">
            <div class="flex-shrink-0">
                <img id="detailImage" src="" class="rounded-3"
                     style="width: 140px; height: 140px; object-fit: cover;" alt="Buku">
            </div>
            <div class="flex-grow-1">
                <h2 class="h4 fw-bold mb-3" id="detailTitle">-</h2>
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr><td class="text-secondary" style="width: 40%;">Pengarang</td><td id="detailPengarang">-</td></tr>
                        <tr><td class="text-secondary">Penerbit</td><td id="detailPenerbit">-</td></tr>
                        <tr><td class="text-secondary">Kode Buku</td><td id="detailKode">-</td></tr>
                        <tr><td class="text-secondary">Stok</td><td id="detailStok">-</td></tr>
                        <tr><td class="text-secondary">Dipinjam</td><td id="detailDipinjam">-</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="admin-footer d-flex justify-content-center gap-3 px-4 py-3">
        <button class="btn btn-primary px-4" onclick="prepareEditFromDetail()">Edit</button>
        <button class="btn btn-outline-secondary bg-white px-4" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" enctype="multipart/form-data"
              class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">Tambah Buku</h2>
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
                        <input type="text" name="kode" id="inputKode" required
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Kode Buku (contoh: A57)">

                        <input type="text" name="judul" id="inputJudul" required
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Judul Buku">

                        <input type="text" name="pengarang" id="inputPengarang"
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Pengarang">

                        <input type="text" name="penerbit" id="inputPenerbit"
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Penerbit">

                        <input type="number" name="stok" id="inputStok" min="0" required
                               class="form-control bg-body-tertiary border-0 py-2"
                               placeholder="Stok">
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
            ? '{{ asset("assets/images/buku") }}/' + item.foto
            : '{{ asset("assets/images/sekolah/logo.png") }}';
    }

    function previewGambar(input) {
        if (input.files && input.files[0]) {
            document.getElementById('modalPreview').src = URL.createObjectURL(input.files[0]);
        }
    }

    function showDetail(btn) {
        current = JSON.parse(btn.dataset.item);

        document.getElementById('detailImage').src     = fotoPreview(current);
        document.getElementById('detailTitle').innerText = current.judul;
        document.getElementById('detailPengarang').innerText = current.pengarang || '-';
        document.getElementById('detailPenerbit').innerText  = current.penerbit || '-';
        document.getElementById('detailKode').innerText      = current.kode;
        document.getElementById('detailStok').innerText      = current.stok;
        document.getElementById('detailDipinjam').innerText  = btn.dataset.dipinjam;

        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function prepareCreate() {
        document.getElementById('modalTitle').innerText = 'Tambah Buku';
        form.action = "{{ route('perpustakaan.buku.store') }}";
        document.getElementById('formMethod').value = 'POST';
        form.reset();
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
        document.getElementById('modalTitle').innerText = 'Data Buku';
        form.action = `/perpustakaan/buku/${current.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('inputKode').value      = current.kode;
        document.getElementById('inputJudul').value     = current.judul;
        document.getElementById('inputPengarang').value = current.pengarang ?? '';
        document.getElementById('inputPenerbit').value  = current.penerbit ?? '';
        document.getElementById('inputStok').value      = current.stok;
        document.getElementById('modalPreview').src     = fotoPreview(current);
        getModal().show();
    }
</script>

@endsection