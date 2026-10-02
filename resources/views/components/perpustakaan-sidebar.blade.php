<aside class="admin-sidebar">
    <div class="d-flex align-items-center gap-3 mb-5">
        <img src="{{ asset('assets/images/sekolah/logo.png') }}" class="admin-avatar" alt="Logo">
        <div class="text-white lh-sm">
            <div class="fw-bold fs-5">Admin Perpustakaan</div>
            <div class="small text-white-50">{{ auth()->user()->email }}</div>
        </div>
    </div>

    <nav class="nav flex-column gap-1">
        <a href="{{ route('perpustakaan.dashboard') }}"
           class="admin-nav-link {{ request()->routeIs('perpustakaan.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-cells-large"></i> Dashboard
        </a>
        <a href="{{ route('perpustakaan.buku.index') }}"
           class="admin-nav-link {{ request()->routeIs('perpustakaan.buku.*') ? 'active' : '' }}">
            <i class="fa-solid fa-book"></i> Data Buku
        </a>
        <a href="{{ route('perpustakaan.peminjaman.index') }}"
           class="admin-nav-link {{ request()->routeIs('perpustakaan.peminjaman.*') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i> Peminjaman
        </a>
        <a href="{{ route('perpustakaan.peminjam.index') }}"
           class="admin-nav-link {{ request()->routeIs('perpustakaan.peminjam.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i> Data Peminjam
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