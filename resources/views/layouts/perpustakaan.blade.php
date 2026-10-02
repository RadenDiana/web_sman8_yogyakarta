<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Perpustakaan') | SMA Negeri 8 Yogyakarta</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    {{-- TOPBAR MOBILE --}}
    <nav class="admin-topbar d-lg-none">
    <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('assets/images/sekolah/logo.png') }}" width="36" height="36" alt="Logo">
        <span class="text-white fw-bold small">{{ auth()->user()->username ?? 'Admin' }}</span>
    </div>
    <button class="btn btn-outline-light btn-sm" onclick="toggleSidebar()" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>
    </nav>
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>
    @include('components.perpustakaan-sidebar')

    <main class="admin-main">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show">
              <strong>Periksa kembali formulir:</strong>
             <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
             <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
             </div>
        @endif

        @yield('content')
    </main>
<script>
    const SIDEBAR_MIN = 220, SIDEBAR_MAX = 360, SIDEBAR_DEFAULT = 260;

    function setSidebar(px) {
        px = Math.round(Math.min(SIDEBAR_MAX, Math.max(SIDEBAR_MIN, px)));
        localStorage.setItem('sidebarWidth', px);
        document.documentElement.style.setProperty('--sidebar-width', px + 'px');
    }

    setSidebar(parseInt(localStorage.getItem('sidebarWidth')) || SIDEBAR_DEFAULT);

    /* ===== Sidebar mobile: buka/tutup ===== */
    function toggleSidebar() {
        document.querySelector('.admin-sidebar')?.classList.toggle('show');
        document.getElementById('sidebarBackdrop')?.classList.toggle('show');
    }

    function tutupSidebar() {
        document.querySelector('.admin-sidebar')?.classList.remove('show');
        document.getElementById('sidebarBackdrop')?.classList.remove('show');
    }

    tutupSidebar();
    window.addEventListener('pageshow', tutupSidebar);

    /* ===== Handle geser lebar (desktop) ===== */
    const handle = document.querySelector('.sidebar-resize');
    if (handle) {
        handle.addEventListener('mousedown', (e) => {
            e.preventDefault();
            document.body.classList.add('resizing');

            const geser   = (ev) => setSidebar(ev.clientX / 0.8);
            const selesai = () => {
                document.body.classList.remove('resizing');
                document.removeEventListener('mousemove', geser);
                document.removeEventListener('mouseup', selesai);
            };

            document.addEventListener('mousemove', geser);
            document.addEventListener('mouseup', selesai);
        });
    }
</script>
</body>
</html>