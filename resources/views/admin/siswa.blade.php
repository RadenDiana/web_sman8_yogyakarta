@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')

{{-- HEADER --}}
<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Data Siswa</h1>
        <div class="admin-breadcrumb">Dashboard / Data Siswa</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Siswa
    </button>
</div>

{{-- ERROR VALIDASI --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        Periksa kembali data yang diisi:
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- SEARCH & FILTER --}}
<form action="{{ route('admin.siswa.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari siswa..."
                   onchange="this.form.submit()">
        </div>
    </div>
    <div class="col-md-5">
        <select name="kelas" class="form-select admin-filter-select" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach ($daftarKelas as $kelas)
                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
            @endforeach
        </select>
    </div>
</form>

{{-- TABLE + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive admin-scroll">
        <table class="table table-hover align-middle text-center mb-0">
            <thead class="admin-thead">
                <tr>
                    <th>Siswa</th>
                    <th>NISN</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswas as $item)
                    <tr>
                        <td class="text-uppercase">{{ $item->nama }}</td>
                        <td>{{ $item->nis }}</td>
                        <td>{{ $item->kelas }}</td>
                        <td>
                            <span class="badge rounded-pill badge-admin {{ $item->status ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                {{ $item->status ? 'Aktif' : 'Nonaktif' }}
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
                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST"
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
                    <tr><td colspan="5" class="py-4 text-secondary">Data siswa tidak ditemukan.</td></tr>
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

{{-- DETAIL (panel, sama seperti pengumuman) --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-4 position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>

        <h2 class="h5 fw-bold mb-4">Detail Siswa</h2>

        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <div class="text-center flex-shrink-0">
                <img id="detailImage" src="{{ asset('assets/images/sekolah/logo.png') }}"
                     class="rounded-3" style="width: 150px; height: 190px; object-fit: cover;" alt="Foto siswa">
            </div>

            <div class="flex-grow-1 w-100">
                <table class="table table-borderless table-sm mb-0 text-start">
                    <tbody>
                        <tr>
                            <td class="text-secondary" style="width: 42%;">Nama</td>
                            <td id="detailNama" class="fw-semibold text-uppercase">-</td>
                        </tr>
                        <tr><td class="text-secondary">NIS</td><td id="detailNis">-</td></tr>
                        <tr><td class="text-secondary">Kelas</td><td id="detailKelas">-</td></tr>
                        <tr><td class="text-secondary">Jenis Kelamin</td><td id="detailJk">-</td></tr>
                        <tr><td class="text-secondary">Agama</td><td id="detailAgama">-</td></tr>
                        <tr><td class="text-secondary">Tempat, Tgl Lahir</td><td id="detailTtl">-</td></tr>
                        <tr><td class="text-secondary">Alamat</td><td id="detailAlamat">-</td></tr>
                        <tr>
                            <td class="text-secondary">Status</td>
                            <td><span class="badge rounded-pill badge-admin" id="detailStatus">-</span></td>
                        </tr>
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

{{-- MODAL TAMBAH / EDIT (style galeri) --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" enctype="multipart/form-data"
              class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="status" id="inputStatus" value="1">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">Tambah Siswa</h2>
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
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Nama lengkap">

                        <input type="text" name="nis" id="inputNis" required
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="NIS">

                        <input type="text" name="kelas" id="inputKelas" required
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Kelas (contoh: XII F 1)">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <select name="jenis_kelamin" id="inputJk"
                                        class="form-select bg-body-tertiary border-0 py-2">
                                    <option value="P">Perempuan</option>
                                    <option value="L">Laki-laki</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="agama" id="inputAgama"
                                        class="form-select bg-body-tertiary border-0 py-2">
                                    @foreach (['Islam', 'Kristen', 'Katholik', 'Hindu', 'Budha'] as $agama)
                                        <option value="{{ $agama }}">{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="text" name="tempat_lahir" id="inputTempat"
                               class="form-control bg-body-tertiary border-0 mb-2 py-2"
                               placeholder="Tempat lahir">

                        <input type="date" name="tanggal_lahir" id="inputTanggal"
                               class="form-control bg-body-tertiary border-0 mb-2 py-2">

                        <textarea name="alamat" id="inputAlamat" rows="2"
                                  class="form-control bg-body-tertiary border-0 mb-3 py-2"
                                  placeholder="Alamat"></textarea>

                        <div class="d-flex gap-2">
                            <button type="button" id="btnAktif" class="btn rounded-3 px-4"
                                    onclick="setStatus(1)">Aktif</button>
                            <button type="button" id="btnNonaktif" class="btn rounded-3 px-4"
                                    onclick="setStatus(0)">Nonaktif</button>
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

    function formatTtl(item) {
        if (!item.tempat_lahir && !item.tanggal_lahir) return '-';
        const tgl = item.tanggal_lahir
            ? new Date(item.tanggal_lahir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
            : '-';
        return (item.tempat_lahir || '-') + ', ' + tgl;
    }

    function setStatus(status) {
    document.getElementById('inputStatus').value = status;
    document.getElementById('btnAktif').className = 'btn rounded-3 px-4 ' +
        (status == 1 ? 'bg-success-subtle text-success-emphasis' : 'btn-light border');
    document.getElementById('btnNonaktif').className = 'btn rounded-3 px-4 ' +
        (status == 0 ? 'btn-warning' : 'btn-light border');
}

    function previewGambar(input) {
        if (input.files && input.files[0]) {
            document.getElementById('modalPreview').src = URL.createObjectURL(input.files[0]);
        }
    }

    function setStatusBadge(status) {
        const badge = document.getElementById('detailStatus');
        badge.textContent = status == 1 ? 'Aktif' : 'Nonaktif';
        badge.className = 'badge rounded-pill badge-admin ' +
            (status == 1 ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis');
    }

    function showDetail(btn) {
        current = JSON.parse(btn.dataset.item);

        document.getElementById('detailImage').src = fotoPreview(current);
        document.getElementById('detailNama').innerText   = current.nama;
        document.getElementById('detailNis').innerText    = current.nis;
        document.getElementById('detailKelas').innerText  = current.kelas;
        document.getElementById('detailJk').innerText     = current.jenis_kelamin == 'P' ? 'Perempuan' : 'Laki-laki';
        document.getElementById('detailAgama').innerText  = current.agama;
        document.getElementById('detailTtl').innerText    = formatTtl(current);
        document.getElementById('detailAlamat').innerText = current.alamat || '-';
        setStatusBadge(current.status);

        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function prepareCreate() {
        document.getElementById('modalTitle').innerText = 'Tambah Siswa';
        form.action = "{{ route('admin.siswa.store') }}";
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        setStatus(1);
        document.getElementById('modalPreview').src = '{{ asset("assets/images/sekolah/logo.png") }}';
        getModal().show();
    }

    function fillForm(item) {
        document.getElementById('modalTitle').innerText = 'Data Siswa';
        form.action = `/admin/siswa/${item.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('inputNama').value    = item.nama;
        document.getElementById('inputNis').value     = item.nis;
        document.getElementById('inputKelas').value   = item.kelas;
        document.getElementById('inputJk').value      = item.jenis_kelamin;
        document.getElementById('inputAgama').value   = item.agama;
        document.getElementById('inputTempat').value  = item.tempat_lahir ?? '';
        document.getElementById('inputTanggal').value = (item.tanggal_lahir ?? '').slice(0, 10);
        document.getElementById('inputAlamat').value  = item.alamat ?? '';
        setStatus(item.status ? 1 : 0);
        document.getElementById('modalPreview').src = fotoPreview(item);
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