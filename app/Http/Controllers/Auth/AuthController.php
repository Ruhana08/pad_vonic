<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'email.required'    => 'Username atau email institusi wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal berjumlah 6 karakter.',
        ]);

        // Simpan sesi sementara untuk flow OTP
        session(['auth_email' => $request->email]);

        return redirect()->route('login.page', ['step' => 'otp'])
            ->with('info', 'Kode OTP 6 digit telah dikirimkan ke email Anda.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|ends_with:@ugm.ac.id,@mail.ugm.ac.id|max:150',
            'password'              => 'required|string|min:8|same:password_confirmation',
            'password_confirmation' => 'required|string',
        ], [
            'name.required'                  => 'Nama lengkap wajib diisi.',
            'email.required'                 => 'Email institusi wajib diisi.',
            'email.ends_with'                => 'Email harus menggunakan domain resmi UGM (@ugm.ac.id atau @mail.ugm.ac.id).',
            'password.required'              => 'Kata sandi wajib diisi.',
            'password.min'                   => 'Kata sandi minimal berjumlah 8 karakter.',
            'password.same'                  => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.',
            'password_confirmation.required' => 'Ulangi kata sandi wajib diisi.',
        ]);

        session(['auth_email' => $request->email]);

        return redirect()->route('register', ['step' => 'otp'])
            ->with('info', 'Pendaftaran berhasil. Silakan masukkan kode OTP yang dikirimkan ke email.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|digits:1',
        ], [
            'otp.required' => 'Kode OTP wajib diisi lengkap 6 digit.',
            'otp.*.required' => 'Setiap digit OTP harus diisi angka.',
        ]);

        session()->forget('auth_email');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Autentikasi berhasil! Selamat datang di Panel Admin Vonic.');
    }

    public function logout(Request $request)
    {
        // Hapus sesi auth
        session()->flush();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem (Log Out).');
    }
}
