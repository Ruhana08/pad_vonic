<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailLoginOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $step = $request->query('step', 'login');
        return view('auth.login', compact('step'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required'    => 'Username atau email institusi wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal berjumlah 6 karakter.',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Email admin tidak terdaftar di sistem.'], 404);
            }
            return back()->withErrors(['email' => 'Email admin tidak terdaftar di sistem.'])->withInput();
        }

        // Jika user memiliki password di database, periksa kecocokannya
        if ($user->password && !Hash::check($request->password, $user->password)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Kata sandi yang Anda masukkan salah.'], 401);
            }
            return back()->withErrors(['password' => 'Kata sandi yang Anda masukkan salah.'])->withInput();
        }

        // Generate OTP 6 digit
        $otp = (string) random_int(100000, 999999);

        EmailLoginOtp::updateOrCreate(
            ['email' => $user->email],
            [
                'otp_hash'   => Hash::make($otp),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        // Kirim email OTP
        try {
            Mail::raw(
                "Halo {$user->nama},\n\nKode OTP login VONIC kamu adalah: {$otp}\n\nKode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Kode OTP Login VONIC Admin');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP login: ' . $e->getMessage());
        }

        session([
            'auth_email' => $user->email,
            'auth_mode'  => 'login',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP 6 digit telah dikirimkan ke email Anda.',
                'email'   => $user->email,
            ]);
        }

        return redirect()->route('login.page', ['step' => 'otp'])
            ->with('info', 'Kode OTP 6 digit telah dikirimkan ke email Anda.');
    }

    public function showRegister(Request $request)
    {
        $step = $request->query('step', 'register');
        return view('auth.register', compact('step'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|ends_with:@ugm.ac.id,@mail.ugm.ac.id|max:150',
            'password'              => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
                'same:password_confirmation',
            ],
            'password_confirmation' => 'required|string',
        ], [
            'name.required'                  => 'Nama lengkap wajib diisi.',
            'email.required'                 => 'Email institusi wajib diisi.',
            'email.ends_with'                => 'Email harus menggunakan domain resmi UGM (@ugm.ac.id atau @mail.ugm.ac.id).',
            'password.required'              => 'Kata sandi wajib diisi.',
            'password.min'                   => 'Kata sandi minimal berjumlah 8 karakter.',
            'password.regex'                 => 'Kata sandi harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.',
            'password.same'                  => 'Konfirmasi kata sandi tidak cocok dengan kata sandi.',
            'password_confirmation.required' => 'Ulangi kata sandi wajib diisi.',
        ]);

        // Cek jika email sudah terdaftar
        $existing = User::where('email', $request->email)->first();
        if ($existing && $existing->role === 'admin' && $existing->password) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Email admin ini sudah aktif dan terdaftar. Silakan login.'], 422);
            }
            return back()->withErrors(['email' => 'Email admin ini sudah aktif dan terdaftar. Silakan login.'])->withInput();
        }

        // Generate OTP 6 digit
        $otp = (string) random_int(100000, 999999);

        EmailLoginOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp_hash'   => Hash::make($otp),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        // Simpan data calon admin di session sementara
        session([
            'reg_admin_name'     => $request->name,
            'reg_admin_email'    => $request->email,
            'reg_admin_password' => Hash::make($request->password),
            'auth_email'         => $request->email,
            'auth_mode'          => 'register',
        ]);

        // Kirim email OTP
        try {
            Mail::raw(
                "Halo {$request->name},\n\nKode OTP registrasi Akun Admin VONIC kamu adalah: {$otp}\n\nKode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun.",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Kode OTP Registrasi Admin VONIC');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP registrasi: ' . $e->getMessage());
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil. Silakan masukkan kode OTP yang dikirimkan ke email.',
                'email'   => $request->email,
            ]);
        }

        return redirect()->route('register', ['step' => 'otp'])
            ->with('info', 'Pendaftaran berhasil. Silakan masukkan kode OTP yang dikirimkan ke email.');
    }

    public function resendOtp(Request $request)
    {
        $email = $request->email ?: session('auth_email') ?: session('reg_admin_email');

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Sesi email tidak ditemukan. Silakan isi form kembali.'], 400);
        }

        $otp = (string) random_int(100000, 999999);

        EmailLoginOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp_hash'   => Hash::make($otp),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        try {
            Mail::raw(
                "Halo,\n\nKode OTP baru kamu adalah: {$otp}\n\nKode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun.",
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Kode OTP Baru VONIC');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim kirim ulang OTP: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru berhasil dikirimkan ke email Anda.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $inputOtp = is_array($request->otp) ? implode('', $request->otp) : $request->otp;
        $email = $request->email ?: session('auth_email') ?: session('reg_admin_email');

        if (!$email) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi telah kedaluwarsa. Silakan ulangi pendaftaran/login.'], 400);
            }
            return redirect()->route('login.page')->withErrors(['email' => 'Sesi telah kedaluwarsa. Silakan ulangi login.']);
        }

        $otpRecord = EmailLoginOtp::where('email', $email)->first();

        if (!$otpRecord) {
            $msg = 'Kode OTP tidak ditemukan atau sudah digunakan.';
            if ($request->expectsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => $msg], 400);
            return back()->withErrors(['otp' => $msg]);
        }

        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->delete();
            $msg = 'Kode OTP sudah kedaluwarsa (lebih dari 5 menit). Silakan klik kirim ulang OTP.';
            if ($request->expectsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => $msg], 400);
            return back()->withErrors(['otp' => $msg]);
        }

        if ($otpRecord->attempts >= 5) {
            $otpRecord->delete();
            $msg = 'Batas percobaan OTP tercapai (5x). Silakan kirim ulang kode baru.';
            if ($request->expectsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => $msg], 429);
            return back()->withErrors(['otp' => $msg]);
        }

        if (!Hash::check($inputOtp, $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');
            $sisa = 5 - $otpRecord->attempts;
            $msg = "Kode OTP tidak cocok. Sisa kesempatan: {$sisa} kali.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg, 'remaining' => $sisa], 400);
            }
            return back()->withErrors(['otp' => $msg]);
        }

        // Hapus record OTP karena sukses
        $otpRecord->delete();

        // Cek apakah flow registrasi baru
        if (session('auth_mode') === 'register' && session('reg_admin_email')) {
            $admin = User::updateOrCreate(
                ['email' => session('reg_admin_email')],
                [
                    'nama'       => session('reg_admin_name'),
                    'password'   => session('reg_admin_password'),
                    'role'       => 'admin',
                    'unit_kerja' => 'DTEDI SV UGM',
                    'pekerjaan'  => 'Administrator',
                ]
            );
            Auth::login($admin);
        } else {
            $admin = User::where('email', $email)->where('role', 'admin')->first();
            if ($admin) {
                Auth::login($admin);
            }
        }

        session()->forget(['auth_email', 'auth_mode', 'reg_admin_name', 'reg_admin_email', 'reg_admin_password']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Autentikasi berhasil! Mengalihkan ke Dashboard...',
                'redirect' => route('admin.dashboard')
            ]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Autentikasi berhasil! Selamat datang di Panel Admin Vonic.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->flush();

        return redirect()->route('login.page')
            ->with('success', 'Anda telah berhasil keluar dari sistem (Log Out).');
    }
}
