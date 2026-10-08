<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Login dummy sementara.
        // Nanti diganti dengan email dari hasil callback SSO UGM.
        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with(
                'error',
                'Akun Anda tidak terdaftar sebagai peserta Polling DTEDI. Silakan hubungi admin.'
            );
        }

        Auth::login($user);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}