<aside class="admin-sidebar">
    <div class="d-flex align-items-center gap-3 mb-5">
        <img src="{{ asset('assets/images/sekolah/logo.png') }}" class="admin-avatar" alt="Logo">
        <div class="text-white lh-sm">
            <div class="fw-bold fs-5">{{ auth()->user()->username ?? 'Admin' }}</div>
            <div class="small text-white-50">{{ auth()->user()->email }}</div>
        </div>
    </div>

    @php $pesanBelumDibalas = \App\Models\Kontak::whereNull('dibalas_at')->count(); @endphp

    <nav class="nav flex-column gap-2">
        <a href="{{ route('admin.dashboard') }}"
           class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-cells-large"></i> Dashboard
        </a>
        <a href="{{ route('admin.ekstrakurikuler.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.ekstrakurikuler.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Ekstrakurikuler
        </a>
        <a href="{{ route('admin.siswa.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-graduate"></i> Data Siswa
        </a>
        <a href="{{ route('admin.prestasi.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.prestasi.*') ? 'active' : '' }}">
            <i class="fa-solid fa-star"></i> Prestasi
        </a>
        <a href="{{ route('admin.pengumuman.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines"></i> Pengumuman
        </a>
        <a href="{{ route('admin.galeri.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}">
            <i class="fa-solid fa-image"></i> Galeri
        </a>
        <a href="{{ route('admin.kontak.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.kontak.*') ? 'active' : '' }}">
            <i class="fa-solid fa-envelope"></i> Kontak
            @if ($pesanBelumDibalas > 0)
                <span class="ms-auto badge rounded-pill bg-warning text-dark">{{ $pesanBelumDibalas }}</span>
            @endif
        </a>
        <a href="{{ route('admin.akun.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i> Akun
        </a>
    </nav>

    <div class="mt-auto">
        
     <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-danger rounded-3 px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Keluar
            </button>
        </form>
    </div>

    <div class="sidebar-resize" title="Geser untuk mengatur lebar"></div>
</aside>