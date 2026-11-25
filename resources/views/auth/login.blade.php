@extends('layouts.auth')

@section('title', request()->is('admin/*') ? 'Log In Admin' : 'Log In')

@section('content')
<div class="w-full max-w-[550px] rounded-[24px] bg-white p-6 sm:p-7 shadow-[0px_11px_20px_0px_rgba(0,0,0,0.09),0px_3px_6px_0px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col gap-4"
     x-data="{
       step: 'login', // 'login' | 'otp'
       email: '',
       showPassword: false,
       otpDigits: ['', '', '', '', '', ''],
       timer: 60,
       timerInterval: null,
       loading: false,
       errorMessage: '',
       successMessage: '',

       startTimer() {
         this.timer = 60;
         if (this.timerInterval) clearInterval(this.timerInterval);
         this.timerInterval = setInterval(() => {
           if (this.timer > 0) {
             this.timer--;
           } else {
             clearInterval(this.timerInterval);
           }
         }, 1000);
       },

       async submitLogin() {
         this.loading = true;
         this.errorMessage = '';
         this.successMessage = '';

         try {
           const res = await fetch('/api/request-otp', {
             method: 'POST',
             headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             body: JSON.stringify({ email: this.email })
           });

           const data = await res.json();

           if (res.ok && data.success) {
             this.step = 'otp';
             this.successMessage = data.message || 'OTP berhasil dikirim ke email admin.';
             this.startTimer();
             this.$nextTick(() => {
               const firstInput = document.getElementById('otp-0');
               if (firstInput) firstInput.focus();
             });
           } else {
             this.errorMessage = data.message || 'Gagal mengirim OTP. Pastikan email terdaftar.';
           }
         } catch (err) {
           this.errorMessage = 'Terjadi kesalahan jaringan atau server.';
         } finally {
           this.loading = false;
         }
       },

       handleOtpInput(e, index) {
         const val = e.target.value;
         if (val.length > 1) {
           this.otpDigits[index] = val.slice(-1);
         }
         if (val && index < 5) {
           const next = document.getElementById('otp-' + (index + 1));
           if (next) next.focus();
         }
       },

       handleOtpKeydown(e, index) {
         if (e.key === 'Backspace' && !this.otpDigits[index] && index > 0) {
           const prev = document.getElementById('otp-' + (index - 1));
           if (prev) prev.focus();
         }
       },

       async resendOtp() {
         if (this.timer > 0) return;
         this.loading = true;
         this.errorMessage = '';
         this.successMessage = '';

         try {
           const res = await fetch('/api/request-otp', {
             method: 'POST',
             headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             body: JSON.stringify({ email: this.email })
           });

           const data = await res.json();
           if (res.ok && data.success) {
             this.otpDigits = ['', '', '', '', '', ''];
             this.successMessage = 'Kode OTP baru berhasil dikirim ke email Anda!';
             this.startTimer();
             this.$nextTick(() => {
               const firstInput = document.getElementById('otp-0');
               if (firstInput) firstInput.focus();
             });
           } else {
             this.errorMessage = data.message || 'Gagal mengirim ulang OTP.';
           }
         } catch (e) {
           this.errorMessage = 'Koneksi gagal atau pengiriman email sedang diproses.';
         } finally {
           this.loading = false;
         }
       },

       async verifyOtpSubmit() {
         const otpStr = this.otpDigits.join('');
         if (otpStr.length !== 6) {
           this.errorMessage = 'Silakan isi lengkap 6 digit kode OTP.';
           return;
         }

         this.loading = true;
         this.errorMessage = '';
         this.successMessage = '';

         try {
           const res = await fetch('/api/verify-otp', {
             method: 'POST',
             headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             body: JSON.stringify({
               email: this.email,
               otp: otpStr
             })
           });

           const data = await res.json();

           if (res.ok && data.success) {
             if (data.data?.token) {
               localStorage.setItem('auth_token', data.data.token);
             }
             this.successMessage = 'Verifikasi berhasil! Mengalihkan ke dashboard...';
             setTimeout(() => {
               window.location.href = '{{ route('admin.dashboard') }}';
             }, 800);
           } else {
             this.errorMessage = data.message || 'Kode OTP tidak cocok atau kedaluwarsa.';
           }
         } catch (err) {
           this.errorMessage = 'Terjadi kesalahan sistem saat memverifikasi OTP.';
         } finally {
           this.loading = false;
         }
       }
     }">

  {{-- Brand Logos Atas Sesuai Figma --}}
  <div class="flex items-center justify-center gap-10">
    <div class="flex flex-col items-center">
      <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-10 w-auto object-contain" />
      <span class="mt-1 text-sm font-bold text-primary-1 tracking-wider">Vonic</span>
    </div>
    <div class="h-12 w-12 flex items-center justify-center">
      <img src="{{ asset('images/logo-ugm.webp') }}" alt="UGM" class="h-full w-full object-contain" />
    </div>
  </div>

  {{-- Alert Pesan Sukses / Error --}}
  <template x-if="errorMessage">
    <div class="p-3 text-xs font-medium text-danger bg-rose-50 border border-rose-200 rounded-lg" x-text="errorMessage"></div>
  </template>
  <template x-if="successMessage">
    <div class="p-3 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg" x-text="successMessage"></div>
  </template>

  {{-- ==================== STEP 1: FORM LOGIN ==================== --}}
  <div x-show="step === 'login'" x-transition class="space-y-[16px]">
    <div>
      <h2 class="text-[22px] font-semibold text-[#191919] leading-[1.3]">{{ request()->is('admin/*') ? 'Log In Admin' : 'Masuk ke Vonic' }}</h2>
      <p class="text-[14px] text-[#6d6d6d] leading-[1.6] mt-0.5 tracking-[0.14px]">
        Silahkan masukkan email institusi Anda. Kode OTP verifikasi akan dikirimkan ke email.
      </p>
    </div>

    <form @submit.prevent="submitLogin()" class="space-y-[12px]">
      {{-- Input Username / Email Institusi --}}
      <div class="space-y-[6px]">
        <label class="block text-[12px] font-medium text-[#525252]">Email Institusi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-letter-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="email"
                 x-model="email"
                 required
                 placeholder="{{ request()->is('admin/*') ? 'admin@ugm.ac.id' : 'nama@ugm.ac.id' }}"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
        </div>
      </div>

      {{-- Tombol Login (Gradient Main-Blue Konsisten) --}}
      <div class="pt-[6px]">
        <button type="submit"
                :disabled="loading"
                class="w-full h-[52px] px-[16px] flex items-center justify-center gap-[12px] rounded-[12px] bg-gradient-main-blue text-white text-[14px] font-semibold shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50">
          <x-solar-login-2-linear class="h-5 w-5 shrink-0" />
          <span x-text="loading ? 'Mengirim OTP...' : 'Login & Kirim OTP'"></span>
        </button>
      </div>

      {{-- Navigasi ke Register --}}
      <div class="pt-1 text-center text-[14px] text-[#6d6d6d]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-[#196bef] hover:underline ml-1">
          Daftar
        </a>
      </div>
    </form>
  </div>

  {{-- ==================== STEP 2: VERIFIKASI OTP ==================== --}}
  <div x-show="step === 'otp'" x-cloak x-transition class="space-y-[16px]">
    <div class="text-center">
      <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary-3 text-primary-1 mb-2">
        <x-solar-shield-check-bold class="h-6 w-6" />
      </div>
      <h2 class="text-[20px] font-semibold text-[#191919]">Verifikasi Kode OTP</h2>
      <p class="mt-1 text-[13px] text-[#6d6d6d]">
        Masukkan 6 digit kode verifikasi yang telah dikirimkan ke:
      </p>
      <p class="text-[13px] font-bold text-primary-1 mt-0.5" x-text="email || 'admin@ugm.ac.id'"></p>
    </div>

    <form @submit.prevent="verifyOtpSubmit()" class="space-y-5">
      {{-- 6 Kotak Input OTP --}}
      <div class="flex items-center justify-between gap-2">
        <template x-for="(digit, idx) in otpDigits" :key="idx">
          <input type="text"
                 :id="'otp-' + idx"
                 maxlength="1"
                 x-model="otpDigits[idx]"
                 @input="handleOtpInput($event, idx)"
                 @keydown="handleOtpKeydown($event, idx)"
                 class="h-12 w-12 rounded-[8px] border border-[#c4c4c4] bg-white text-center text-lg font-bold text-black shadow-xs focus:border-primary-1 focus:ring-1 focus:ring-primary-1 focus:outline-none transition-all" />
        </template>
      </div>

      {{-- Timer & Kirim Ulang --}}
      <div class="text-center text-xs text-[#6d6d6d]">
        <template x-if="timer > 0">
          <p>Kirim ulang kode dalam <span class="font-bold text-primary-1" x-text="'00:' + String(timer).padStart(2, '0')"></span></p>
        </template>
        <template x-if="timer === 0">
          <button type="button"
                  @click="resendOtp()"
                  :disabled="loading"
                  class="font-bold text-primary-1 hover:underline cursor-pointer disabled:opacity-50">
            Kirim Ulang Kode OTP
          </button>
        </template>
      </div>

      {{-- Tombol Konfirmasi Verifikasi --}}
      <button type="submit"
              :disabled="loading"
              class="w-full h-[52px] flex items-center justify-center gap-2.5 rounded-[12px] bg-gradient-main-blue text-[14px] font-semibold text-white shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50">
        <x-solar-check-circle-linear class="h-5 w-5" />
        <span x-text="loading ? 'Memverifikasi...' : 'Verifikasi & Masuk Dashboard'"></span>
      </button>

      <div class="text-center pt-1">
        <button type="button" @click="step = 'login'; errorMessage = ''; successMessage = '';" class="text-xs text-grey-deep hover:text-primary-1 hover:underline cursor-pointer">
          ← Kembali ke halaman login
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
