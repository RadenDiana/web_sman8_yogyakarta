<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function index()
    {
        return view('guest.login', [
            'title' => 'Login',
        ]);
    }

    /**
     * Memproses login admin
     */
    public function store(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Redirect sesuai role
        return redirect()->route(
            auth()->user()->role === 'perpustakaan'
                ? 'perpustakaan.dashboard'
                : 'admin.dashboard'
        );
    }

    return back()
        ->withErrors(['email' => 'Email atau password salah.'])
        ->withInput($request->only('email'));
}

    /**
     * Reset password langsung (untuk kebutuhan lokal/demo)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.exists'       => 'Email tidak terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
        
    }

    /**
     * Logout
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}