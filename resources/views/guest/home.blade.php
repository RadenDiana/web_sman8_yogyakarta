@extends('layouts.guest')

@section('title', 'Beranda | SMA Negeri 8 Yogyakarta')

@section('content')

@php
    // 4 kata motivasi, bergilir otomatis lewat Bootstrap carousel (interval 4 detik)
    $motivasi = [
        'Prestasi bukan tentang siapa yang paling hebat, melainkan tentang siapa yang memiliki kemauan untuk terus belajar dan berusaha setiap hari.',
        'Pendidikan adalah senjata paling ampuh untuk mengubah dunia.',
        'Jangan takut gagal, takutlah untuk tidak pernah mencoba.',
        'Belajar hari ini adalah investasi terbaik untuk masa depan esok.',
    ];
@endphp

{{-- ============ HERO ============ --}}
<section id="beranda" class="hero-sman8"
         style="background-image: linear-gradient(rgba(10,14,26,.55), rgba(10,14,26,.55)), url('{{ asset('assets/images/sekolah/hero-foto2.png') }}');">
    <div class="container py-5">
        <h1 class="hero-title mb-3">Membangun Generasi<br>Cerdas dan Berkarakter</h1>
        <p class="hero-sub mb-4">Sekolah yang tidak hanya fokus pada belajar,<br>tetapi juga membentuk masa depan yang lebih baik.</p>
        <a href="#sambutan" class="btn btn-primary btn-lg px-4">Sambutan Kepala Sekolah</a>
    </div>
</section>

{{-- ============ SAMBUTAN (statis, bukan CRUD) ============ --}}
<section id="sambutan" class="bg-white">
    <div class="container py-5" style="max-width: 1100px;">
        <h2 class="home-title mb-2"><span class="text-primary">SAMBUTAN KEPALA</span><br>SEKOLAH</h2>
        <div class="text-secondary mb-4">01 Maret 2026</div>

        <div class="row g-4 align-items-start">
            <div class="col-md-3">
                <img src="{{ asset('assets/images/sekolah/kepala-sekolah.png') }}" class="img-fluid rounded-3" alt="Kepala Sekolah">
                <div class="mt-3">
                    <div class="fw-semibold">Editorial</div>
                    <div class="small text-secondary">{{ now()->translatedFormat('d M Y') }}</div>
                </div>
            </div>
            <div class="col-md-9">
                <p class="mb-0" style="line-height: 1.9;">
                    Assalamu'alaikum warahmatullahi wabarakataatuh. Alhamdulillahirobbil 'aalamiin. Salaam Bahagia...
                    Kita panjatkan puji syukur ke hadirat Allah SWT Tuhan Yang Maha Kuasa atas limpahan rahmat dan
                    hidayah-Nya untuk kita semua. Selamat datang di website SMA Negeri 8 Yogyakarta, media informasi
                    sekolah yang dapat diakses setiap saat..
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ============ PENGUMUMAN TERBARU ============ --}}
<section id="pengumuman" class="section-dark">
    <div class="container py-5" style="max-width: 1100px;">
        <h2 class="fw-bold mb-1">Pengumuman Terbaru</h2>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="text-white-50">Pengumuman terbaru SMA Negeri 8 Yogyakarta</span>
            <a href="{{ route('guest.pengumuman') }}" class="text-white text-decoration-none">
                Lihat Semua <i class="fa-solid fa-chevron-right small"></i>
            </a>
        </div>

        @php $pengumumanChunks = $pengumumanTerbaru->chunk(3); @endphp
        <div id="carouselPengumuman" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @forelse ($pengumumanChunks as $chunk)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }} pb-2">
                       <div class="home-slide-row">
                             @foreach ($chunk as $item)
                                <div class="home-slide-card">
                                    <div class="dark-card h-100">
                                        {{-- sesuaikan 'gambar' & 'isi' dengan kolom di tabel pengumumanmu --}}
                                        <img src="{{ $item->gambar ? asset('assets/images/pengumuman/' . $item->gambar) : asset('assets/images/sekolah/logo.png') }}"
                                             alt="{{ $item->judul }}">
                                        <div class="p-3">
                                            <div class="small text-secondary">{{ $item->created_at->translatedFormat('d M Y') }}</div>
                                            <div class="fw-semibold text-primary mb-2">{{ $item->judul }}</div>
                                            <p class="small text-secondary mb-0">"{{ \Illuminate\Support\Str::limit($item->isi ?? '', 90) }}"</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-white-50 mb-0">Belum ada pengumuman.</p>
                @endforelse
            </div>

            @if ($pengumumanChunks->count() > 1)
                <div class="carousel-indicators position-static">
                    @foreach ($pengumumanChunks as $chunk)
                        <button type="button" data-bs-target="#carouselPengumuman"
                                data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ============ INFORMASI: EKSTRAKURIKULER ============ --}}
<section id="informasi" class="bg-white">
    <div class="container py-5" style="max-width: 1100px;">
        <div class="text-center mb-5">
            <h2 class="home-title">Informasi</h2>
            <div class="title-underline mx-auto"></div>
        </div>

        <div id="ekstrakurikuler">
            <h5 class="fw-bold">Daftar Ekstrakurikuler</h5>
            <div class="text-secondary mb-4">Ekstrakurikuler SMA Negeri 8 Yogyakarta</div>

            <div class="row row-cols-1 row-cols-md-3 g-3">
                @forelse ($ekstrakurikuler as $item)
                    <div class="col">
                        <div class="pill-ekskul">{{ $item->nama }}</div>
                    </div>
                @empty
                    <div class="col"><p class="text-secondary mb-0">Belum ada ekstrakurikuler.</p></div>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- ============ PRESTASI SISWA ============ --}}
<section id="prestasi" class="section-dark">
    <div class="container py-5" style="max-width: 1100px;">
        <h2 class="fw-bold mb-1">Prestasi Siswa</h2>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="text-white-50">Prestasi Siswa SMA Negeri 8 Yogyakarta</span>
            <a href="{{ route('guest.prestasi') }}" class="text-white text-decoration-none">
                Lihat Semua <i class="fa-solid fa-chevron-right small"></i>
            </a>
        </div>

        @php $prestasiChunks = $prestasiHome->chunk(3); @endphp
        <div id="carouselPrestasi" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @forelse ($prestasiChunks as $chunk)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }} pb-2">
                        <div class="home-slide-row">
                             @foreach ($chunk as $item)
                                <div class="home-slide-card">
                                    <div class="dark-card h-100">
                                        <img src="{{ $item->foto ? asset('assets/images/prestasi/' . $item->foto) : asset('assets/images/sekolah/logo.png') }}"
                                             alt="{{ $item->judul }}">
                                        <div class="p-3">
                                            <div class="small text-secondary">{{ $item->created_at->translatedFormat('d M Y') }}</div>
                                            <div class="fw-semibold text-primary mb-2">{{ $item->judul }}</div>
                                            <p class="small text-secondary mb-0">{{ $item->penyelenggara }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-white-50 mb-0">Belum ada data prestasi.</p>
                @endforelse
            </div>

            @if ($prestasiChunks->count() > 1)
                <div class="carousel-indicators position-static">
                    @foreach ($prestasiChunks as $chunk)
                        <button type="button" data-bs-target="#carouselPrestasi"
                                data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ============ KATA MOTIVASI (otomatis ganti tiap 4 detik) ============ --}}
<section class="quote-section">
    <div id="carouselMotivasi" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner">
            @foreach ($motivasi as $quote)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <div class="quote-slide">
                        <p class="fw-bold mb-0">&ldquo;{{ $quote }}&rdquo;</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="carousel-indicators position-static pb-3">
            @foreach ($motivasi as $quote)
                <button type="button" data-bs-target="#carouselMotivasi"
                        data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></button>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ TOP SISWA PERPUSTAKAAN ============ --}}
<section id="top-siswa" class="section-dark">
    <div class="container py-5" style="max-width: 1100px;">
        <h2 class="fw-bold mb-1">Top Siswa Perpustakaan</h2>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="text-white-50">Top Peminjam Buku SMA Negeri 8 Yogyakarta</span>
            <a href="{{ route('guest.top-siswa') }}" class="text-white text-decoration-none">
                Lihat Semua <i class="fa-solid fa-chevron-right small"></i>
            </a>
        </div>

        {{-- Nanti tinggal kirim array: ['foto','kelas','nama','jumlah'] --}}
        <div class="row row-cols-2 row-cols-md-5 g-4">
            @forelse ($topSiswa as $item)
    <div class="col">
        <div class="top-siswa-card h-100">
            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
            <div class="p-3">
                <div class="small text-secondary">{{ $item->kelas }}</div>
                <div class="fw-semibold">{{ $item->nama }}</div>
                <div class="small text-secondary">{{ $item->total_pinjam }} Buku Telah Dipinjam</div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12"><p class="text-white-50 mb-0">Belum ada data perpustakaan.</p></div>
@endforelse
        </div>
    </div>
</section>

{{-- ============ DATA SISWA ============ --}}
<section id="data-siswa" class="bg-white">
    <div class="container py-5" style="max-width: 1100px;">
        <h5 class="fw-bold mb-1">Data siswa</h5>
        <div class="text-secondary mb-4">Data Siswa SMA Negeri 8 Yogyakarta</div>

        <form method="GET" action="{{ route('home') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="tingkat" class="form-select form-select-lg" onchange="this.form.submit()">
                    <option value="">Kelas</option>
                    @foreach ($daftarTingkat as $t)
                        <option value="{{ $t }}" {{ $tingkat === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-lg" onchange="this.form.submit()">
                    <option value="">Kategori Kelas</option>
                    @foreach ($daftarKategori as $k)
                        <option value="{{ $k }}" {{ $kategori === $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-rekap mb-0">
                <thead>
                    <tr>
                        <th>Kelas</th><th>Jumlah</th><th>P</th><th>L</th>
                        <th>Islam</th><th>Katholik</th><th>Kristen</th><th>Hindu</th><th>Budha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekapSiswa as $row)
                        <tr>
                            <td>{{ $row->kelas }}</td>
                            <td>{{ $row->jumlah }}</td>
                            <td>{{ $row->p }}</td>
                            <td>{{ $row->l }}</td>
                            <td>{{ $row->islam }}</td>
                            <td>{{ $row->katholik }}</td>
                            <td>{{ $row->kristen }}</td>
                            <td>{{ $row->hindu }}</td>
                            <td>{{ $row->budha }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-secondary py-4">Belum ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ============ GALERI ============ --}}
<section id="galeri" class="bg-white">
    <div class="container py-5" style="max-width: 1100px;">
        <div class="text-center mb-4">
            <h2 class="home-title">GALERI</h2>
            <div class="title-underline mx-auto"></div>
        </div>

        <div class="text-end mb-3">
            <a href="{{ route('guest.galeri') }}" class="text-primary fw-semibold text-decoration-none">
                Lihat Semua <i class="fa-solid fa-chevron-right small"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-3">
            @forelse ($galeriHome as $item)
                <div class="col">
                    <img src="{{ $item->foto_url }}" alt="{{ $item->judul }}" class="galeri-grid-img">
                </div>
            @empty
                <div class="col-12"><p class="text-center text-secondary mb-0">Belum ada galeri.</p></div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============ KONTAK ============ --}}
<section id="kontak" class="bg-white">
    <div class="container py-5" style="max-width: 1100px;">
        <div class="text-center mb-5">
            <h2 class="home-title">Kontak</h2>
            <div class="title-underline mx-auto"></div>
        </div>

        <div class="row g-4 align-items-center mb-4">
            <div class="col-md-7">
                <iframe class="w-100 rounded-3" style="height: 320px; border: 0;"
                        src="https://www.google.com/maps?q=SMA%20Negeri%208%20Yogyakarta&output=embed"
                        loading="lazy"></iframe>
            </div>
            <div class="col-md-5">
                <div class="d-flex flex-column gap-4">
                    <div class="d-flex gap-3">
                        <div class="kontak-ikon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <div class="fw-bold">Alamat</div>
                            <div class="text-secondary small">Jl. Sidobali No.1, Muja Muja, Umbulharjo, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55165</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="kontak-ikon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <div class="fw-bold">Hubungi kami</div>
                            <div class="text-secondary small">(0274) 513493</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="kontak-ikon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <div class="fw-bold">Email kami</div>
                            <div class="text-secondary small">sman8yogyakarta@yahoo.co.id</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM KIRIM PESAN --}}
        <div class="form-kartu p-4 p-md-5">
            <h5 class="fw-bold mb-4">Kirim Pesan</h5>

            @if ($errors->kontak->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0">
                        @foreach ($errors->kontak->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif
            @if (session('pesanTerkirim'))
                <div class="alert alert-success py-2 small">Pesan berhasil terkirim. Terima kasih!</div>
            @endif

            <form action="{{ route('guest.kontak.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Nama :</label>
                    <input type="text" name="nama" required value="{{ old('nama') }}"
                           class="form-control guest-input" placeholder="Nama lengkap">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email :</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="form-control guest-input" placeholder="Email aktif">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Subjek :</label>
                    <input type="text" name="subjek" required value="{{ old('subjek') }}"
                           class="form-control guest-input" placeholder="Subjek pesan">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Pesan :</label>
                    <textarea name="pesan" rows="5" required
                              class="form-control guest-input"
                              placeholder="Tulis pesan...">{{ old('pesan') }}</textarea>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-primary px-5">Kirim Pesan</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ============ RATING (ANONIM) ============ --}}
<section id="rating" class="bg-white pb-5">
    <div class="container" style="max-width: 720px;">
        <div class="rating-kartu text-center p-4 p-md-5">
            <h5 class="fw-bold mb-1">Rate Your Experience</h5>
            <div class="text-secondary small mb-4">Berikan kami bintang penilaianmu</div>

            @if ($errors->rating->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0">
                        @foreach ($errors->rating->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif
            @if (session('ratingTerkirim'))
                <div class="alert alert-success py-2 small">Penilaian berhasil terkirim. Terima kasih!</div>
            @endif

            <form action="{{ route('guest.rating.store') }}" method="POST">
                @csrf
                <input type="hidden" name="rating" id="inputRating" value="{{ old('rating') }}">

                <div class="star-rating d-flex justify-content-center gap-2 mb-4">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-{{ old('rating') >= $i ? 'solid' : 'regular' }} fa-star" role="button"
                           onclick="pilihBintang({{ $i }})"></i>
                    @endfor
                </div>

                <textarea name="komentar" rows="4"
                          class="form-control guest-input mb-4"
                          placeholder="Kritik, saran, atau komentar (dikirim secara anonim)...">{{ old('komentar') }}</textarea>

                <button class="btn btn-kuning w-100">Kirim Komentar</button>
            </form>
        </div>
    </div>
</section>

<script>
    function pilihBintang(n) {
        // Simpan nilai rating ke hidden input
        document.getElementById('inputRating').value = n;

        // Isi bintang ke-n, kosongkan sisanya
        document.querySelectorAll('.star-rating i').forEach((bintang, index) => {
            if (index < n) {
                bintang.classList.replace('fa-regular', 'fa-solid');
            } else {
                bintang.classList.replace('fa-solid', 'fa-regular');
            }
        });
    }
</script>

@endsection