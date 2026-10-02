@extends('layouts.perpustakaan')

@section('title', 'Data Peminjam')

@section('content')

<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Data Peminjam</h1>
        <div class="admin-breadcrumb">Dashboard / Data Peminjam</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Data Peminjam
    </button>
</div>

{{-- SEARCH & SORT --}}
<form action="{{ route('perpustakaan.peminjam.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari Data Peminjam..."
                   onchange="this.form.submit()">
        </div>
    </div>
    <div class="col-md-5">
        <select name="sort" class="form-select admin-filter-select" onchange="this.form.submit()">
            <option value="">Urutan berdasarkan</option>
            <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A - Z</option>
            <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z - A</option>
        </select>
    </div>
</form>

<p class="small text-secondary mb-3">
    <i class="fa-solid fa-circle-info me-1"></i>
    Siswa otomatis muncul di sini setelah meminjam buku. Tombol "+ Data Peminjam" khusus untuk guru/umum di luar data siswa.
</p>

{{-- TABLE + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive admin-scroll">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="admin-thead">
                <tr>
                    <th>Peminjam</th>
                    <th>NISN/NIP</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswas as $item)
                    <tr>
                        <td class="text-uppercase">
                            {{ $item->nama }}
                            <span class="d-block small text-secondary">{{ $item->tipe === 'siswa' ? 'Siswa' : 'Guru/Umum' }}</span>
                        </td>
                        <td>{{ $item->nis ?? '-' }}</td>
                        <td>{{ $item->kelas ?? '-' }}</td>
                        <td>
                            <span class="badge rounded-pill badge-admin {{ $item->status ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                {{ $item->status ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-item="{{ json_encode($item) }}"
                                        data-sedang="{{ $item->sedang_dipinjam }}"
                                        data-telah="{{ $item->telah_meminjam }}"
                                        data-terlambat="{{ $item->terlambat_count }}"
                                        onclick="showDetail(this)" title="Lihat">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                @if ($item->tipe === 'luar')
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-item="{{ json_encode($item) }}"
                                            onclick="prepareEdit(this)" title="Edit">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <form action="{{ route('perpustakaan.peminjam.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-secondary">Belum ada data peminjam.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $siswas->firstItem() ?? 0 }} sampai {{ $siswas->lastItem() ?? 0 }} dari {{ $siswas->total() }} data
        </span>
        {{ $siswas->links() }}
    </div>
</div>

{{-- DETAIL --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-4 position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>

        <h2 class="h5 fw-bold mb-4">Detail Peminjam</h2>

        <div class="d-flex flex-column flex-md-row align-items-md-start gap-4">
            <div class="flex-shrink-0">
                <img id="detailImage" src="" class="rounded-3"
                     style="width: 140px; height: 170px; object-fit: cover;" alt="Foto peminjam">
            </div>
            <div class="flex-grow-1">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr><td class="text-secondary" style="width: 45%;">Nama</td><td id="detailNama" class="fw-semibold text-uppercase">-</td></tr>
                        <tr><td class="text-secondary">NISN/NIP</td><td id="detailNis">-</td></tr>
                        <tr><td class="text-secondary">Kelas</td><td id="detailKelas">-</td></tr>
                        <tr><td class="text-secondary">Sedang dipinjam</td><td id="detailSedang">-</td></tr>
                        <tr><td class="text-secondary">Telah Meminjam</td><td id="detailTelah">-</td></tr>
                        <tr><td class="text-secondary">Terlambat Mengembalikan</td><td id="detailTerlambat">-</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="admin-footer d-flex justify-content-center gap-3 px-4 py-3">
        <form id="formDelete" action="" method="POST"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger px-4" id="btnDeleteDetail">Hapus</button>
        </form>
        <button class="btn btn-primary px-4" id="btnEditDetail" onclick="prepareEditFromDetail()">Edit</button>
        <button class="btn btn-outline-secondary bg-white px-4" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT (khusus guru/umum) --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" enctype="multipart/form-data"
              class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="status" id="inputStatus" value="1">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">Tambah Peminjam</h2>
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
                        <input type="text" name="nama" id="inputNama" required
                               class="form-control bg-body-tertiary border-0 mb-2 py-2" placeholder="Nama lengkap">

                        <input type="text" name="nis" id="inputNis"
                               class="form-control bg-body-tertiary border-0 mb-2 py-2" placeholder="NIP / identitas (opsional)">

                        <input type="text" name="kelas" id="inputKelas"
                               class="form-control bg-body-tertiary border-0 mb-3 py-2" placeholder="Kategori (contoh: Guru)">

                        <div class="d-flex gap-2">
                            <button type="button" id="btnAktif" class="btn rounded-3 px-4" onclick="setStatus(1)">Aktif</button>
                            <button type="button" id="btnNonaktif" class="btn rounded-3 px-4" onclick="setStatus(0)">Nonaktif</button>
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
            ? '{{ asset("assets/images/siswa") }}/' + item.foto
            : '{{ asset("assets/images/sekolah/logo.png") }}';
    }

    function previewGambar(input) {
        if (input.files && input.files[0]) {
            document.getElementById('modalPreview').src = URL.createObjectURL(input.files[0]);
        }
    }

    function setStatus(status) {
        document.getElementById('inputStatus').value = status;
        document.getElementById('btnAktif').className = 'btn rounded-3 px-4 ' +
            (status == 1 ? 'bg-success-subtle text-success-emphasis' : 'btn-light border');
        document.getElementById('btnNonaktif').className = 'btn rounded-3 px-4 ' +
            (status == 0 ? 'btn-warning' : 'btn-light border');
    }

    function showDetail(btn) {
        current = JSON.parse(btn.dataset.item);

        const isLuar = current.tipe === 'luar';
        document.getElementById('btnDeleteDetail').style.display = isLuar ? '' : 'none';
        document.getElementById('btnEditDetail').style.display   = isLuar ? '' : 'none';

        document.getElementById('detailImage').src      = fotoPreview(current);
        document.getElementById('detailNama').innerText = current.nama;
        document.getElementById('detailNis').innerText  = current.nis ?? '-';
        document.getElementById('detailKelas').innerText  = current.kelas ?? '-';
        document.getElementById('detailSedang').innerText = btn.dataset.sedang;
        document.getElementById('detailTelah').innerText  = btn.dataset.telah;
        document.getElementById('detailTerlambat').innerText = btn.dataset.terlambat + ' kali';

        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function prepareCreate() {
        document.getElementById('modalTitle').innerText = 'Tambah Peminjam';
        form.action = "{{ route('perpustakaan.peminjam.store') }}";
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        setStatus(1);
        document.getElementById('modalPreview').src = '{{ asset("assets/images/sekolah/logo.png") }}';
        getModal().show();
    }

    function prepareEdit(btn) {
        current = JSON.parse(btn.dataset.item);
        openEditModal();
    }

    function prepareEditFromDetail() {
        if (current && current.tipe === 'luar') openEditModal();
    }

    function openEditModal() {
        document.getElementById('modalTitle').innerText = 'Data Peminjam';
        form.action = `/perpustakaan/peminjam/${current.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('inputNama').value  = current.nama;
        document.getElementById('inputNis').value   = current.nis ?? '';
        document.getElementById('inputKelas').value = current.kelas ?? '';
        setStatus(current.status ? 1 : 0);
        document.getElementById('modalPreview').src = fotoPreview(current);
        getModal().show();
    }
</script>

@endsection