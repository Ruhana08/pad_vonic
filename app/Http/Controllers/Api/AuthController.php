<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $val = strtolower(trim($value));
                    if (!str_ends_with($val, '@ugm.ac.id') && !str_ends_with($val, '@mail.ugm.ac.id')) {
                        $fail('Wajib menggunakan alamat email resmi UGM (@ugm.ac.id atau @mail.ugm.ac.id).');
                    }
                },
            ],
        ], [
            'email.required' => 'Silakan masukkan alamat email UGM Anda.',
            'email.email'    => 'Format email tidak valid. Masukkan format email yang benar.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email ini belum terdaftar di daftar pemilih resmi DTEDI UGM. Silakan hubungi Admin jika Anda merupakan sivitas aktif.',
                'data' => null
            ], 401);
        }

        $token = $user->createToken('vonic-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil. Selamat datang di Vonic!',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diambil.',
            'data' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
            'data' => null
        ]);
    }
}