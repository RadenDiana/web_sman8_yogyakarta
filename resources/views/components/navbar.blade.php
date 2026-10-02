<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-sman8">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/sekolah/logo.png') }}" width="40" height="40" alt="Logo">
            <span class="fw-semibold small d-none d-sm-inline">SMA NEGERI 8 YOGYAKARTA</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuUtama">
<ul class="navbar-nav ms-auto align-items-lg-center gap-1">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}#beranda">Beranda</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}#pengumuman">Pengumuman</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button"
           data-bs-toggle="dropdown" aria-expanded="false">Informasi</a>
        <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="{{ route('home') }}#ekstrakurikuler">Ekstrakurikuler</a></li>
            <li><a class="dropdown-item" href="{{ route('home') }}#prestasi">Prestasi</a></li>
            <li><a class="dropdown-item" href="{{ route('home') }}#top-siswa">Top Siswa Perpustakaan</a></li>
            <li><a class="dropdown-item" href="{{ route('home') }}#data-siswa">Data Siswa</a></li>
        </ul>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}#galeri">Galeri</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}#kontak">Kontak</a>
    </li>

    @guest
    <li class="nav-item ms-lg-2">
        <a class="btn btn-primary btn-sm px-3" href="{{ route('login') }}">Masuk</a>
    </li>
@else
    <li class="nav-item ms-lg-2">
        <a class="btn btn-primary btn-sm px-3"
           href="{{ auth()->user()->role === 'perpustakaan' ? route('perpustakaan.dashboard') : route('admin.dashboard') }}">Dashboard</a>
    </li>
@endguest
</ul>
        </div>
    </div>
</nav>