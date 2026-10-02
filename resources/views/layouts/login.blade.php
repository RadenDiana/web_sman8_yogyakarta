<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk | SMA Negeri 8 Yogyakarta')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @yield('content')

    <script>
        // Alert error/sukses hilang sendiri setelah ±3 detik
        document.querySelectorAll('.alert').forEach(el => {
            setTimeout(() => { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; }, 2500);
            setTimeout(() => el.remove(), 3000);
        });
    </script>
</body>
</html>