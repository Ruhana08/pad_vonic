<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailLoginOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // 1. Admin meminta OTP melalui email
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email admin tidak terdaftar.',
                'data' => null
            ], 401);
        }

        $otp = (string) random_int(100000, 999999);

        EmailLoginOtp::updateOrCreate(
            ['email' => $user->email],
            [
                'otp_hash' => Hash::make($otp),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        Mail::raw(
            "Halo {$user->nama},\n\nKode OTP login VONIC kamu adalah: {$otp}\n\nKode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode OTP Login VONIC');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil dikirim ke email admin.',
            'data' => null
        ]);
    }

    // 2. Admin memverifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email admin tidak terdaftar.',
                'data' => null
            ], 401);
        }

        $otpRecord = EmailLoginOtp::where('email', $user->email)->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'OTP tidak ditemukan atau sudah digunakan.',
                'data' => null
            ], 400);
        }

        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->delete();

            return response()->json([
                'success' => false,
                'message' => 'OTP sudah kedaluwarsa. Silakan minta OTP baru.',
                'data' => null
            ], 400);
        }

        if ($otpRecord->attempts >= 5) {
            $otpRecord->delete();

            return response()->json([
                'success' => false,
                'message' => 'Batas percobaan OTP sudah tercapai.',
                'data' => null
            ], 429);
        }

        if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');
            $otpRecord->refresh();

            if ($otpRecord->attempts >= 5) {
                $otpRecord->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Batas percobaan OTP sudah tercapai. Silakan minta OTP baru.',
                    'data' => null
                ], 429);
            }

            return response()->json([
                'success' => false,
                'message' => 'OTP salah.',
                'data' => [
                    'remaining_attempts' => 5 - $otpRecord->attempts
                ]
            ], 400);
        }

        $otpRecord->delete();

        $token = $user->createToken('vonic-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login admin berhasil.',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    // 3. User login hanya menggunakan email terdaftar
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'user')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email belum terdaftar. Silakan hubungi admin.',
                'data' => null
            ], 401);
        }

        $token = $user->createToken('vonic-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login user berhasil.',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    // 4. Menampilkan profile
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diambil.',
            'data' => $user
        ]);
    }

    // 5. Logout
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
