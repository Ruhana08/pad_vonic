<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|ends_with:@mail.ugm.ac.id',
            'password' => 'required|string|min:6',
        ], [
            'email.required'    => 'Email institusi wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.ends_with'   => 'Email admin wajib menggunakan domain @mail.ugm.ac.id.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal berjumlah 6 karakter.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::whereRaw('LOWER(email) = ?', [$email])
            ->whereIn('role', ['admin', 'master_admin'])
            ->first();

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Email admin tidak terdaftar di sistem.'], 404);
            }
            return back()->withErrors(['email' => 'Email admin tidak terdaftar di sistem.'])->withInput();
        }

        if (!$user->password || !Hash::check($request->password, $user->password)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Kata sandi yang Anda masukkan salah.'], 401);
            }
            return back()->withErrors(['password' => 'Kata sandi yang Anda masukkan salah.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Login berhasil! Mengalihkan ke Dashboard...',
                'redirect' => route('admin.dashboard'),
            ]);
        }

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Selamat datang kembali di Panel Admin Vonic.');
    }

    public function showForgotPassword()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|ends_with:@mail.ugm.ac.id',
        ], [
            'email.required'  => 'Email institusi wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.ends_with' => 'Email admin wajib menggunakan domain @mail.ugm.ac.id.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::whereRaw('LOWER(email) = ?', [$email])
            ->whereIn('role', ['admin', 'master_admin'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email admin tidak ditemukan di sistem.',
            ], 404);
        }

        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Tautan reset kata sandi telah dikirim ke email Anda.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status) ?: 'Gagal mengirim tautan reset kata sandi. Periksa konfigurasi email.',
        ], 500);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email|ends_with:@mail.ugm.ac.id',
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/', 'confirmed'],
        ], [
            'email.required'     => 'Email institusi wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.ends_with'    => 'Email admin wajib menggunakan domain @mail.ugm.ac.id.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.regex'     => 'Kata sandi harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::whereRaw('LOWER(email) = ?', [$email])
            ->whereIn('role', ['admin', 'master_admin'])
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email admin tidak terdaftar di sistem.'])->withInput();
        }

        $status = Password::broker()->reset(
            [
                'email'                 => $user->email,
                'password'              => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token'                 => $request->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('admin.login')
                ->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru Anda.');
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }

    public function logout(Request $request)
    {
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($isAdmin ? 'admin.login' : 'user.login')
            ->with('success', 'Anda telah berhasil keluar dari sistem (Log Out).');
    }
}
