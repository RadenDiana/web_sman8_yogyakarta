@extends('layouts.admin')

@section('title', 'Akun')

@section('content')

{{-- HEADER --}}
<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Akun</h1>
        <div class="admin-breadcrumb">Dashboard / Akun</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Akun
    </button>
</div>

<p class="fw-semibold mb-3">Total Akun : {{ $totalAkun }}</p>

{{-- LIST + PAGINATION --}}
<div class="card admin-card border-0 shadow-sm overflow-hidden mb-4">
    <ul class="list-group list-group-flush admin-scroll">
        @forelse ($akuns as $item)
            <li class="list-group-item d-flex align-items-center justify-content-between px-4 py-4"
                style="cursor: pointer;"
                data-item="{{ json_encode($item) }}"
                onclick="showDetail(this)">
                <span class="text-secondary mx-auto">{{ $item->username }}</span>
                <span class="badge rounded-pill badge-admin {{ $item->role === 'perpustakaan' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis' }}"
                      style="min-width: 270px;">
                    {{ $item->role === 'perpustakaan' ? 'Admin Perpustakaan' : 'Admin' }}
                </span>
            </li>
        @empty
            <li class="list-group-item text-center text-secondary py-4">Belum ada data akun.</li>
        @endforelse
    </ul>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $akuns->firstItem() ?? 0 }} sampai {{ $akuns->lastItem() ?? 0 }} dari {{ $akuns->total() }} data
        </span>
        {{ $akuns->links() }}
    </div>
</div>

{{-- DETAIL (muncul saat baris diklik) --}}
<div class="card admin-outline admin-card shadow-sm mx-auto d-none" id="detailPanel" style="max-width: 920px;">
    <div class="card-body p-4 p-md-5 position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" onclick="closeDetail()"></button>

        <h2 class="h4 fw-bold mb-4" id="detailNama">-</h2>

        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <div class="border rounded-3 p-3 bg-white">
                <img src="{{ asset('assets/images/sekolah/logo.png') }}"
                     style="width: 80px; height: 80px; object-fit: contain;" alt="Foto Admin">
            </div>

            <div class="flex-grow-1 w-100">
                <div class="row mb-2">
                    <div class="col-5 col-md-3 text-secondary small">Username</div>
                    <div class="col-7 col-md-9 small" id="detailUsername">-</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 col-md-3 text-secondary small">Email</div>
                    <div class="col-7 col-md-9 small" id="detailEmail">-</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 col-md-3 text-secondary small">Role</div>
                    <div class="col-7 col-md-9 small" id="detailRole">-</div>
                </div>
                <div class="row">
                    <div class="col-5 col-md-3 text-secondary small">Password</div>
                    <div class="col-7 col-md-9 small">********</div>
                </div>
            </div>
        </div>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-center gap-3 px-4 py-3">
        <form id="formDelete" action="" method="POST"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger px-4">Hapus</button>
        </form>
        <button class="btn btn-primary px-4" onclick="prepareEditFromDetail()">Edit</button>
        <button class="btn btn-outline-secondary bg-white px-4" onclick="closeDetail()">Tutup</button>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT (desain DATA AKUN) --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">DATA AKUN</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <input type="text" name="username" id="inputUsername" required
                       class="form-control bg-body-tertiary border-0 py-3 mb-3"
                       placeholder="Nama Pengguna/User Name">

                <input type="email" name="email" id="inputEmail" required
                       class="form-control bg-body-tertiary border-0 py-3 mb-3"
                       placeholder="Email">

                <select name="role" id="inputRole"
                        class="form-select bg-body-tertiary border-0 py-3 mb-3">
                    <option value="admin">Admin</option>
                    <option value="perpustakaan">Admin Perpustakaan</option>
                </select>

                <input type="password" name="password" id="inputPassword"
                       class="form-control bg-body-tertiary border-0 py-3"
                       placeholder="Kata Sandi">

                <small class="text-secondary d-none" id="passwordHelp">(Kosongkan jika tidak ingin mengubah)</small>
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

    function showDetail(btn) {
        current = JSON.parse(btn.dataset.item);

        document.getElementById('detailNama').innerText     = current.username;
        document.getElementById('detailUsername').innerText = current.username;
        document.getElementById('detailEmail').innerText    = current.email;
        document.getElementById('detailRole').innerText     = current.role === 'perpustakaan' ? 'Admin Perpustakaan' : 'Admin';

        document.getElementById('formDelete').action = `/admin/akun/${current.id}`;
        const panel = document.getElementById('detailPanel');
        panel.classList.remove('d-none');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
    }

    function prepareCreate() {
        form.action = "{{ route('admin.akun.store') }}";
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        document.getElementById('inputRole').value = 'admin';
        document.getElementById('inputPassword').required = true;
        document.getElementById('passwordHelp').classList.add('d-none');
        getModal().show();
    }

    function prepareEditFromDetail() {
        if (!current) return;

        form.action = `/admin/akun/${current.id}`;
        document.getElementById('formMethod').value = 'PUT';

        document.getElementById('inputUsername').value = current.username;
        document.getElementById('inputEmail').value    = current.email;
        document.getElementById('inputRole').value     = current.role ?? 'admin';
        document.getElementById('inputPassword').value = '';
        document.getElementById('inputPassword').required = false;
        document.getElementById('passwordHelp').classList.remove('d-none');

        getModal().show();
    }
</script>

@endsection