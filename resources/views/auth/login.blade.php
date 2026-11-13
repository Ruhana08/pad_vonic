@extends('layouts.auth')

@section('title', 'Log In Admin')

@section('content')
<div class="w-full max-w-[550px] rounded-[24px] bg-white p-[28px] shadow-[0px_11px_20px_0px_rgba(0,0,0,0.09),0px_3px_6px_0px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col gap-[20px]"
     x-data="{
       step: 'login', // 'login' | 'otp'
       email: '',
       showPassword: false,
       otpDigits: ['', '', '', '', '', ''],
       timer: 60,
       timerInterval: null,

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

       submitLogin() {
         this.step = 'otp';
         this.startTimer();
         this.$nextTick(() => {
           const firstInput = document.getElementById('otp-0');
           if (firstInput) firstInput.focus();
         });
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

       resendOtp() {
         if (this.timer === 0) {
           this.startTimer();
         }
       }
     }">

  {{-- Brand Logos Atas Sesuai Figma --}}
  <div class="flex items-center justify-center gap-[20px] h-[50px]">
    <div class="flex flex-col items-center">
      <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-[32px] w-[40px] object-contain" />
      <span class="text-[12px] font-bold text-[#196bef] tracking-[0.96px]">Vonic</span>
    </div>
    <div class="h-[40px] w-[40px]">
      <img src="{{ asset('images/logo-ugm.webp') }}" alt="UGM" class="h-full w-full object-contain" />
    </div>
  </div>

  {{-- ==================== STEP 1: FORM LOGIN ==================== --}}
  <div x-show="step === 'login'" x-transition class="space-y-[16px]">
    <div>
      <h2 class="text-[22px] font-semibold text-[#191919] leading-[1.3]">Log In Admin</h2>
      <p class="text-[14px] text-[#6d6d6d] leading-[1.6] mt-0.5 tracking-[0.14px]">
        Selamat datang kembali Admin Vonic
      </p>
    </div>

    <form @submit.prevent="submitLogin()" class="space-y-[14px]">
      <p class="text-[12px] font-bold text-primary-1">Masuk ke Akun</p>

      {{-- Input Username / Email --}}
      <div class="space-y-[6px]">
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-user-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="text"
                 x-model="email"
                 required
                 placeholder="Username atau Email Institusi"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
        </div>
      </div>

      {{-- Input Password --}}
      <div class="space-y-[6px]">
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-lock-keyhole-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input :type="showPassword ? 'text' : 'password'"
                 required
                 placeholder="Password"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
          <button type="button"
                  @click="showPassword = !showPassword"
                  class="text-[#acacac] hover:text-[#525252] focus:outline-none">
            <template x-if="!showPassword">
              <x-solar-eye-linear class="h-4 w-4" />
            </template>
            <template x-if="showPassword">
              <x-solar-eye-closed-linear class="h-4 w-4" />
            </template>
          </button>
        </div>
        <div class="text-right">
          <a href="#" class="text-[12px] font-medium text-[#6d6d6d] hover:text-primary-1 transition-colors">
            Lupa kata sandi?
          </a>
        </div>
      </div>

      {{-- Tombol Login (Warna #196BEF / Gradient Sesuai Figma) --}}
      <div class="pt-[6px]">
        <button type="submit"
                class="w-full h-[54px] px-[16px] flex items-center justify-center gap-[12px] rounded-[8px] bg-primary-1 text-white text-[14px] font-medium shadow-md hover:bg-primary-4 transition-all cursor-pointer">
          <span>Log In</span>
          <x-solar-arrow-right-linear class="h-5 w-5" />
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

    <form action="{{ route('admin.dashboard') }}" method="GET" class="space-y-5">
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
                  class="font-bold text-primary-1 hover:underline cursor-pointer">
            Kirim Ulang Kode OTP
          </button>
        </template>
      </div>

      {{-- Tombol Konfirmasi Verifikasi --}}
      <button type="submit"
              class="w-full h-[50px] flex items-center justify-center gap-2 rounded-[8px] bg-primary-1 text-[14px] font-medium text-white shadow-md hover:bg-primary-4 transition-all cursor-pointer">
        <x-solar-check-circle-linear class="h-5 w-5" />
        <span>Verifikasi & Masuk Dashboard</span>
      </button>

      <div class="text-center">
        <button type="button"
                @click="step = 'login'"
                class="text-xs font-semibold text-grey-2 hover:text-black transition-colors">
          &larr; Ganti Akun / Kembali
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
