<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            ->with(['hd' => 'mail.ugm.ac.id']) // hosted domain hint — tampilkan akun UGM dulu
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

        // Validasi domain — wajib @mail.ugm.ac.id
        if (! str_ends_with($email, '@mail.ugm.ac.id')) {
            return redirect()->route('user.login')
                ->with('error', 'Hanya akun email resmi UGM (@mail.ugm.ac.id) yang dapat masuk.');
        }

        // Cek apakah email terdaftar di database users (pemilih resmi DTEDI)
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            return redirect()->route('user.login')
                ->with('error', 'Email Anda belum terdaftar sebagai pemilih DTEDI UGM. Silakan hubungi Admin.');
        }

        // Buat Sanctum token
        $token = $user->createToken('vonic-user')->plainTextToken;

        // Kirim token + data user ke frontend via hidden page (localStorage hanya bisa diset via JS)
        $userData = json_encode([
            'id'    => $user->id_user,
            'nama'  => $user->nama,
            'email' => $user->email,
            'nip'   => $user->nip ?? null,
            'role'  => $user->role ?? 'user',
        ]);

        return view('auth.google-callback', [
            'token'    => $token,
            'userData' => $userData,
            'homeUrl'  => route('home'),
        ]);
    }
}
