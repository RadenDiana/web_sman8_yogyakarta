@extends('layouts.login')

@section('title', 'Masuk | SMA Negeri 8 Yogyakarta')

@section('content')
<section class="login-page min-vh-100 d-flex flex-column align-items-center justify-content-center py-4 px-3">

    {{-- BRAND --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mb-4 text-center">
        <img src="{{ asset('assets/images/sekolah/logo.png') }}"
             width="72" height="72" alt="Logo SMA Negeri 8 Yogyakarta">
        <h1 class="login-brand-name mb-0">SMA NEGERI 8 YOGYAKARTA</h1>
    </div>

    {{-- PESAN ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger w-100 mb-4" style="max-width: 560px;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PESAN SUKSES --}}
    @if (session('success'))
        <div class="alert alert-success w-100 mb-4" style="max-width: 560px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- KARTU FORM --}}
    <div class="login-card bg-white w-100 px-4 px-md-5 py-4 py-md-5" style="max-width: 560px;">
        <form id="loginForm" action="{{ route('login.store') }}" method="POST"
              class="d-flex flex-column gap-3">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="Email/Username" autocomplete="email" required
                   class="login-input">

            <input type="password" name="password" placeholder="Password"
                   autocomplete="current-password" required
                   class="login-input">
        </form>

        <p class="text-center text-secondary mt-4 mb-0" style="font-size: 14px;">
            Lupa Password?
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalLupa"
               class="text-decoration-none" style="color: var(--bs-primary);">Reset di sini</a>
        </p>
    </div>

    {{-- TOMBOL DI LUAR KARTU --}}
    <button type="submit" form="loginForm" class="login-submit w-100 mt-4" style="max-width: 420px;">
        MASUK
    </button>

    <a href="{{ route('home') }}" class="login-back mt-3">
        <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke beranda
    </a>
</section>

{{-- MODAL LUPA PASSWORD --}}
<div class="modal fade" id="modalLupa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('password.reset') }}"
              class="modal-content border-0 rounded-4 overflow-hidden">
            @csrf
            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h2 class="h5 fw-bold text-uppercase mb-0">Lupa Password</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <input type="email" name="email" required placeholder="Email akun Anda"
                       class="login-input mb-3">

                <input type="password" name="password" required placeholder="Password Baru"
                       class="login-input mb-3">

                <input type="password" name="password_confirmation" required
                       placeholder="Ulangi Password Baru" class="login-input">
            </div>
            <div class="bg-body-tertiary px-4 py-3 d-flex justify-content-center">
               <button type="submit" class="btn btn-primary px-5" style="min-width: 220px;">Reset Password</button>
            </div>
        </form>
    </div>
</div>
@endsection