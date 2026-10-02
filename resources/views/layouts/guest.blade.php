<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SMA Negeri 8 Yogyakarta')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@hasSection('hideNavbar')
    {{-- halaman sub: pakai header putih sendiri --}}
@else
    @include('components.navbar')
@endif

<main>
    @yield('content')
</main>

@include('components.footer')

<script>
    const menu = document.getElementById('menuUtama');

    // Tutup menu saat link diklik (mode HP)
    document.querySelectorAll('#menuUtama .nav-link:not(.dropdown-toggle), #menuUtama .dropdown-item, #menuUtama .btn').forEach(a =>
        a.addEventListener('click', () =>
            bootstrap.Collapse.getOrCreateInstance(menu).hide()));

    // Tutup menu saat klik di luar navbar
    document.addEventListener('click', (e) => {
        if (!menu.classList.contains('show')) return;
        if (!e.target.closest('.navbar-sman8')) {
            bootstrap.Collapse.getOrCreateInstance(menu).hide();
        }
    });
</script>
</body>
</html>