@extends('layouts.perpustakaan')

@section('title', 'Peminjaman')

@section('content')

<div class="admin-header-line pb-3 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="admin-title">Peminjaman</h1>
        <div class="admin-breadcrumb">Dashboard / Peminjaman</div>
    </div>
    <button class="btn btn-primary" onclick="prepareCreate()">
        <i class="fa-solid fa-plus me-1"></i> Pinjam Buku
    </button>
</div>

{{-- SEARCH & SORT --}}
<form action="{{ route('perpustakaan.peminjaman.index') }}" method="GET" class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="input-group admin-search">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari Buku Yang Dipinjam..."
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
                    <th>Nama</th>
                    <th>Judul Buku</th>
                    <th>Tgl.Pinjam</th>
                    <th>Tgl.Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $item)
                    <tr>
                        <td> {{ $item->nama_peminjam }}
                            <span class="d-block small text-secondary">{{ $item->siswa_id ? 'Siswa' : 'Guru/Umum' }}</span>
                        </td>
                        <td>{{ $item->buku?->judul ?? '-' }}</td>
                        <td>{{ $item->tgl_pinjam->translatedFormat('d/m/Y') }}</td>
                        <td>{{ $item->tgl_kembali->translatedFormat('d/m/Y') }}</td>
                        <td>
                            <span class="badge rounded-pill badge-admin {{ $item->status_label === 'Dipinjam' ? 'bg-success-subtle text-success-emphasis' : ($item->status_label === 'Terlambat' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-secondary-subtle text-secondary-emphasis') }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td>
    <div class="aksi-pinjam d-flex flex-column gap-1 mx-auto">
        @if (! $item->tgl_dikembalikan)
            <form action="{{ route('perpustakaan.peminjaman.kembalikan', $item->id) }}" method="POST">
                @csrf
                <button class="btn btn-primary btn-sm" style="font-size: 12px;">Kembalikan</button>
            </form>
        @endif
        <button type="button" class="btn btn-outline-secondary btn-sm" style="font-size: 12px;"
                data-item="{{ json_encode($item) }}"
                onclick="prepareEdit(this)">Edit</button>
    </div>
</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 text-secondary">Data peminjaman tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-footer d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
        <span class="small text-secondary">
            Menampilkan {{ $peminjaman->firstItem() ?? 0 }} sampai {{ $peminjaman->lastItem() ?? 0 }} dari {{ $peminjaman->total() }} data
        </span>
        {{ $peminjaman->links() }}
    </div>
</div>

{{-- MODAL TAMBAH / EDIT --}}
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMain" method="POST" class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="tipe" id="inputTipe" value="siswa">
            <input type="hidden" name="siswa_id" id="inputSiswaId">
            <input type="hidden" name="peminjam_id" id="inputPeminjamId">

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0" id="modalTitle">Pinjam Buku</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <select id="pilihTipe" class="form-select bg-body-tertiary border-0 py-3 mb-3"
                        onchange="gantiTipe(this.value)">
                    <option value="siswa">Peminjam: Siswa</option>
                    <option value="luar">Peminjam: Guru / Umum</option>
                </select>

                {{-- SISWA: input + datalist (bisa ketik nama / NIS / kelas) --}}
                <div id="wrapSiswa">
                    <input type="text" id="cariSiswa" list="daftarSiswa"
                           class="form-control bg-body-tertiary border-0 py-3 mb-1"
                           placeholder="Ketik nama / NIS / kelas..."
                           oninput="pilihSiswa(this)">
                    <datalist id="daftarSiswa">
                        @foreach ($siswaOption as $s)
                            <option data-id="{{ $s->id }}" value="{{ $s->nama }} — {{ $s->nis }} — {{ $s->kelas }}">
                        @endforeach
                    </datalist>
                    <small class="text-secondary">Ketik untuk mencari, lalu klik nama yang muncul.</small>
                </div>

                {{-- GURU / UMUM --}}
                <div id="wrapLuar" class="d-none">
                    <select id="inputPeminjamLuar" class="form-select bg-body-tertiary border-0 py-3"
                            onchange="document.getElementById('inputPeminjamId').value = this.value">
                        <option value="" disabled selected>-- Pilih Guru / Umum --</option>
                        @foreach ($peminjamOption as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kelas ?? 'Umum' }})</option>
                        @endforeach
                    </select>
                    <small class="text-secondary">Belum terdaftar? Tambahkan lewat menu Data Peminjam.</small>
                </div>

                <select name="buku_id" id="inputBuku" required
                        class="form-select bg-body-tertiary border-0 py-3 mt-3 mb-3">
                    <option value="" disabled selected>-- Pilih Buku --</option>
                    @foreach ($bukuOption as $b)
                        <option value="{{ $b->id }}">{{ $b->judul }} (stok: {{ $b->stok }})</option>
                    @endforeach
                </select>

                <input type="date" name="tgl_pinjam" id="inputTglPinjam" required
                       class="form-control bg-body-tertiary border-0 mb-3 py-3">

                <input type="date" name="tgl_kembali" id="inputTglKembali" required
                       class="form-control bg-body-tertiary border-0 py-3">
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

    function gantiTipe(tipe) {
        document.getElementById('inputTipe').value = tipe;
        document.getElementById('wrapSiswa').classList.toggle('d-none', tipe !== 'siswa');
        document.getElementById('wrapLuar').classList.toggle('d-none', tipe !== 'luar');
        document.getElementById('inputSiswaId').value = '';
        document.getElementById('inputPeminjamId').value = '';
        if (tipe === 'siswa') document.getElementById('cariSiswa').value = '';
    }

    // Cocokkan input dengan opsi datalist -> ambil id siswa
    function pilihSiswa(input) {
        const opt = [...document.querySelectorAll('#daftarSiswa option')]
            .find(o => o.value === input.value);
        document.getElementById('inputSiswaId').value = opt ? opt.dataset.id : '';
    }

    function prepareCreate() {
        document.getElementById('modalTitle').innerText = 'Pinjam Buku';
        form.action = "{{ route('perpustakaan.peminjaman.store') }}";
        document.getElementById('formMethod').value = 'POST';
        form.reset();
        gantiTipe('siswa');
        document.getElementById('pilihTipe').value = 'siswa';
        getModal().show();
    }

    function prepareEdit(btn) {
        current = JSON.parse(btn.dataset.item);
        document.getElementById('modalTitle').innerText = 'Data Peminjaman';
        form.action = `/perpustakaan/peminjaman/${current.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('inputBuku').value       = current.buku_id;
        document.getElementById('inputTglPinjam').value  = (current.tgl_pinjam || '').slice(0, 10);
        document.getElementById('inputTglKembali').value = (current.tgl_kembali || '').slice(0, 10);

        if (current.siswa_id) {
            document.getElementById('pilihTipe').value = 'siswa';
            gantiTipe('siswa');
            const s = current.siswa;
            document.getElementById('cariSiswa').value = `${s.nama} — ${s.nis ?? ''} — ${s.kelas ?? ''}`;
            document.getElementById('inputSiswaId').value = s.id;
        } else {
            document.getElementById('pilihTipe').value = 'luar';
            gantiTipe('luar');
            document.getElementById('inputPeminjamLuar').value = current.peminjam_id;
            document.getElementById('inputPeminjamId').value = current.peminjam_id;
        }

        getModal().show();
    }
</script>

@endsection