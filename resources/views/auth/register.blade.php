@extends('layouts.auth')

@section('title', 'Registrasi Admin')

@section('content')
<div class="w-full max-w-[550px] rounded-[24px] bg-white p-6 sm:p-7 shadow-[0px_11px_20px_0px_rgba(0,0,0,0.09),0px_3px_6px_0px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col gap-4"
     x-data="{
       step: 'register', // 'register' | 'otp'
       name: '',
       email: '',
       password: '',
       passwordConfirmation: '',
       showPassword: false,
       showConfirmPassword: false,
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

       get hasMin8() { return this.password.length >= 8; },
       get hasUpper() { return /[A-Z]/.test(this.password); },
       get hasLower() { return /[a-z]/.test(this.password); },
       get hasNumber() { return /[0-9]/.test(this.password); },
       get hasSymbol() { return /[^A-Za-z0-9]/.test(this.password); },
       get isPasswordStrong() { return this.hasMin8 && this.hasUpper && this.hasLower && this.hasNumber && this.hasSymbol; },

       async submitRegister() {
         if (!this.isPasswordStrong) {
           this.errorMessage = 'Kata sandi wajib minimal 8 karakter serta mengandung huruf besar, huruf kecil, angka, dan karakter spesial.';
           return;
         }
         if (this.password !== this.passwordConfirmation) {
           this.errorMessage = 'Konfirmasi kata sandi tidak cocok dengan kata sandi.';
           return;
         }

         this.loading = true;
         this.errorMessage = '';
         this.successMessage = '';

         try {
           const res = await fetch('{{ route('register.submit') }}', {
             method: 'POST',
             headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             body: JSON.stringify({
               name: this.name,
               email: this.email,
               password: this.password,
               password_confirmation: this.passwordConfirmation
             })
           });

           const data = await res.json();

           if (res.ok && data.success) {
             this.step = 'otp';
             this.successMessage = data.message || 'OTP berhasil dikirim ke email pendaftaran.';
             this.startTimer();
             this.$nextTick(() => {
               const firstInput = document.getElementById('otp-0');
               if (firstInput) firstInput.focus();
             });
           } else {
             if (data.errors) {
               this.errorMessage = Object.values(data.errors).flat().join(' ');
             } else {
               this.errorMessage = data.message || 'Gagal mendaftar. Pastikan email UGM valid.';
             }
           }
         } catch (err) {
           this.errorMessage = 'Terjadi kesalahan sistem atau koneksi.';
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
             this.successMessage = 'Kode OTP pendaftaran baru berhasil dikirim!';
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
           const res = await fetch('{{ route('verify.otp') }}', {
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
             this.successMessage = 'Aktivasi berhasil! Mengalihkan ke dashboard...';
             setTimeout(() => {
               window.location.href = data.redirect || '{{ route('admin.dashboard') }}';
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

  {{-- ==================== STEP 1: FORM REGISTRASI ==================== --}}
  <div x-show="step === 'register'" x-transition class="space-y-[16px]">
    <div>
      <h2 class="text-[22px] font-semibold text-[#191919] leading-[1.3]">Registrasi Admin</h2>
      <p class="text-[14px] text-[#6d6d6d] leading-[1.6] mt-0.5 tracking-[0.14px]">
        Lengkapi data berikut untuk membuat akun admin baru.
      </p>
    </div>

    <form @submit.prevent="submitRegister()" class="space-y-[12px]">
      {{-- Input Nama Lengkap --}}
      <div class="space-y-[6px]">
        <label class="block text-[12px] font-medium text-[#525252]">Nama lengkap</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-user-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="text"
                 x-model="name"
                 required
                 placeholder="Masukkan nama lengkap"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
        </div>
      </div>

      {{-- Input Email Institusi --}}
      <div class="space-y-[6px]">
        <label class="block text-[12px] font-medium text-[#525252]">Email institusi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-letter-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="email"
                 x-model="email"
                 required
                 placeholder="nama@ugm.ac.id"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
        </div>
      </div>

      {{-- Input Kata Sandi --}}
      <div class="space-y-[6px]">
        <label class="block text-[12px] font-medium text-[#525252]">Kata sandi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-lock-keyhole-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input :type="showPassword ? 'text' : 'password'"
                 x-model="password"
                 required
                 placeholder="Buat kata sandi minimal 8 karakter"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
          <button type="button"
                  @click="showPassword = !showPassword"
                  class="text-[#acacac] hover:text-[#525252] focus:outline-none cursor-pointer">
            <template x-if="!showPassword">
              <x-solar-eye-closed-linear class="h-4 w-4" />
            </template>
            <template x-if="showPassword">
              <x-solar-eye-linear class="h-4 w-4" />
            </template>
          </button>
        </div>
        {{-- Indikator Syarat Kata Sandi --}}
        <div class="grid grid-cols-2 gap-x-3 gap-y-1 pt-1 text-[11px]">
          <span class="flex items-center gap-1" :class="hasMin8 ? 'text-success font-medium' : 'text-[#6d6d6d]'">
            <span x-text="hasMin8 ? '✓' : '•'"></span> Minimal 8 karakter
          </span>
          <span class="flex items-center gap-1" :class="(hasUpper && hasLower) ? 'text-success font-medium' : 'text-[#6d6d6d]'">
            <span x-text="(hasUpper && hasLower) ? '✓' : '•'"></span> Huruf besar & kecil
          </span>
          <span class="flex items-center gap-1" :class="hasNumber ? 'text-success font-medium' : 'text-[#6d6d6d]'">
            <span x-text="hasNumber ? '✓' : '•'"></span> Mengandung angka
          </span>
          <span class="flex items-center gap-1" :class="hasSymbol ? 'text-success font-medium' : 'text-[#6d6d6d]'">
            <span x-text="hasSymbol ? '✓' : '•'"></span> Karakter spesial (!@#$)
          </span>
        </div>
      </div>

      {{-- Input Konfirmasi Kata Sandi --}}
      <div class="space-y-[6px]">
        <label class="block text-[12px] font-medium text-[#525252]">Konfirmasi kata sandi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-lock-keyhole-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input :type="showConfirmPassword ? 'text' : 'password'"
                 x-model="passwordConfirmation"
                 required
                 placeholder="Ulangi kata sandi"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
          <button type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="text-[#acacac] hover:text-[#525252] focus:outline-none cursor-pointer">
            <template x-if="!showConfirmPassword">
              <x-solar-eye-closed-linear class="h-4 w-4" />
            </template>
            <template x-if="showConfirmPassword">
              <x-solar-eye-linear class="h-4 w-4" />
            </template>
          </button>
        </div>
      </div>

      {{-- Tombol Daftar Admin (Gradient Main-Blue Konsisten) --}}
      <div class="pt-[8px]">
        <button type="submit"
                :disabled="loading"
                class="w-full h-[52px] px-[16px] flex items-center justify-center gap-[12px] rounded-[12px] bg-gradient-main-blue text-white text-[14px] font-semibold shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50">
          <x-solar-user-plus-linear class="h-5 w-5" />
          <span x-text="loading ? 'Memproses Pendaftaran...' : 'Daftar Admin'"></span>
        </button>
      </div>

      {{-- Navigasi ke Login Admin --}}
      <div class="pt-1 text-center text-[14px] text-[#6d6d6d]">
        Sudah punya akun?
        <a href="{{ route('login.page') }}" class="font-semibold text-[#196bef] hover:underline ml-1">
          Login
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
      <h2 class="text-[20px] font-semibold text-[#191919]">Verifikasi Akun Baru</h2>
      <p class="mt-1 text-[13px] text-[#6d6d6d]">
        Kode OTP pendaftaran telah dikirim ke:
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
              class="w-full h-[52px] flex items-center justify-center gap-2 rounded-[12px] bg-gradient-main-blue text-[14px] font-semibold text-white shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50">
        <x-solar-check-circle-linear class="h-5 w-5" />
        <span x-text="loading ? 'Mengaktivasi Akun...' : 'Aktivasi Akun & Masuk'"></span>
      </button>

      <div class="text-center pt-1">
        <button type="button" @click="step = 'register'; errorMessage = ''; successMessage = '';" class="text-xs text-grey-deep hover:text-primary-1 hover:underline cursor-pointer">
          ← Kembali ke data pendaftaran
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
