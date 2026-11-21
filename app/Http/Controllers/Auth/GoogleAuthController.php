<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pemilih;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke Google OAuth.
     * Flow: tombol "Login dengan Email UGM" → GET /auth/google → Google login page
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['hd' => 'ugm.ac.id']) // hosted domain hint — tampilkan akun UGM dulu
            ->redirect();
    }

    /**
     * Callback dari Google setelah user login.
     * Validasi domain UGM, cek database pemilih, buat Sanctum token, simpan di localStorage via redirect.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return redirect()->route('user.login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        $email = strtolower(trim($googleUser->getEmail()));

        // Validasi domain — hanya @ugm.ac.id atau @mail.ugm.ac.id
        if (! str_ends_with($email, '@ugm.ac.id') && ! str_ends_with($email, '@mail.ugm.ac.id')) {
            return redirect()->route('user.login')
                ->with('error', 'Hanya akun email resmi UGM (@ugm.ac.id / @mail.ugm.ac.id) yang dapat masuk.');
        }

        // Cek apakah email terdaftar di database pemilih
        $pemilih = Pemilih::where('email', $email)->first();

        if (! $pemilih) {
            return redirect()->route('user.login')
                ->with('error', 'Email Anda belum terdaftar sebagai pemilih DTEDI UGM. Silakan hubungi Admin.');
        }

        // Buat Sanctum token
        $token = $pemilih->createToken('vonic-user')->plainTextToken;

        // Kirim token + data user ke frontend via hidden page (localStorage hanya bisa diset via JS)
        $userData = json_encode([
            'id'    => $pemilih->id,
            'nama'  => $pemilih->nama,
            'email' => $pemilih->email,
            'nim'   => $pemilih->nim ?? null,
        ]);

        return view('auth.google-callback', [
            'token'    => $token,
            'userData' => $userData,
            'homeUrl'  => route('home'),
        ]);
    }
}
