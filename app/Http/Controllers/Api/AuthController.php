<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailLoginOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // 1. Admin meminta OTP melalui email
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->whereIn('role', ['admin', 'master_admin'])
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
            ->whereIn('role', ['admin', 'master_admin'])
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

    // 3. User login hanya menggunakan email terdaftar (Google SSO)
    public function loginUser(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $client = new \Google\Client([
            'client_id' => config('services.google.client_id'),
        ]);

        $payload = $client->verifyIdToken($request->id_token);

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Token Google tidak valid.',
                'data' => null
            ], 401);
        }

        $email = $payload['email'] ?? null;
        $emailVerified = $payload['email_verified'] ?? false;

        if (!$email || !$emailVerified || !str_ends_with($email, '@ugm.ac.id')) {
            return response()->json([
                'success' => false,
                'message' => 'Gunakan akun Google UGM yang terverifikasi.',
                'data' => null
            ], 401);
        }

        $user = User::where('email', $email)
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

    // 4. Menampilkan profil (GET /api/profile)
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diambil.',
            'data' => array_merge($user->toArray(), [
                'foto_url' => $user->foto
                    ? Storage::url($user->foto)
                    : null,
            ])
        ]);
    }

    // 5. Update profil + upload foto (POST /api/profile)
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nama'       => 'sometimes|required|string|max:255',
            'pekerjaan'  => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:255',
            'no_telp'    => 'nullable|string|max:20',
            'foto'       => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['nama', 'pekerjaan', 'unit_kerja', 'no_telp']);

        if ($request->boolean('remove_foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = null;
        } elseif ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto')->store('foto_profil', 'public');
            $data['foto'] = $path;
        }

        $user->fill(array_filter($data, fn($v) => !is_null($v)));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => array_merge($user->fresh()->toArray(), [
                'foto_url' => $user->foto
                    ? Storage::url($user->foto)
                    : null,
            ])
        ]);
    }

    // 6. Logout
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
