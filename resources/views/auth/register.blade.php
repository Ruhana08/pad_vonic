@extends('layouts.auth')

@section('title', 'Registrasi Admin')

@section('content')
<div class="w-full max-w-[550px] rounded-[24px] bg-white p-[28px] shadow-[0px_11px_20px_0px_rgba(0,0,0,0.09),0px_3px_6px_0px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col gap-[20px]"
     x-data="{
       step: 'register', // 'register' | 'otp'
       email: '',
       showPassword: false,
       showConfirmPassword: false,
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

       submitRegister() {
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

  {{-- ==================== STEP 1: FORM REGISTRASI ==================== --}}
  <div x-show="step === 'register'" x-transition class="space-y-[16px]">
    <div>
      <h2 class="text-[22px] font-semibold text-[#191919] leading-[1.3]">Registrasi Admin</h2>
      <p class="text-[14px] text-[#6d6d6d] leading-[1.6] mt-0.5 tracking-[0.14px]">
        Lengkapi data berikut untuk membuat akun admin.
      </p>
    </div>

    <form @submit.prevent="submitRegister()" class="space-y-[12px]">
      {{-- Input Nama Lengkap --}}
      <div class="space-y-[6px]">
        <label class="block text-[12px] font-medium text-[#525252]">Nama lengkap</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-user-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="text"
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
                 required
                 placeholder="Buat kata sandi"
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
      </div>

      {{-- Input Konfirmasi Kata Sandi --}}
      <div class="space-y-[6px]">
        <label class="block text-[12px] font-medium text-[#525252]">Konfirmasi kata sandi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-lock-keyhole-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input :type="showConfirmPassword ? 'text' : 'password'"
                 required
                 placeholder="Ulangi kata sandi"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
          <button type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="text-[#acacac] hover:text-[#525252] focus:outline-none">
            <template x-if="!showConfirmPassword">
              <x-solar-eye-linear class="h-4 w-4" />
            </template>
            <template x-if="showConfirmPassword">
              <x-solar-eye-closed-linear class="h-4 w-4" />
            </template>
          </button>
        </div>
      </div>

      {{-- Tombol Daftar Admin (Warna #053788 Sesuai Figma) --}}
      <div class="pt-[8px]">
        <button type="submit"
                class="w-full h-[54px] px-[16px] flex items-center justify-center gap-[12px] rounded-[8px] bg-[#053788] text-white text-[14px] font-medium shadow-md hover:bg-[#032661] transition-all cursor-pointer">
          <span>Daftar Admin</span>
          <x-solar-user-plus-linear class="h-5 w-5" />
        </button>
      </div>

      {{-- Navigasi ke Login --}}
      <div class="pt-1 text-center text-[14px] text-[#6d6d6d]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-[#196bef] hover:underline ml-1">
          Masuk
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
              class="w-full h-[50px] flex items-center justify-center gap-2 rounded-[8px] bg-[#053788] text-[14px] font-medium text-white shadow-md hover:bg-[#032661] transition-all cursor-pointer">
        <x-solar-check-circle-linear class="h-5 w-5" />
        <span>Aktivasi Akun & Masuk</span>
      </button>

      <div class="text-center">
        <button type="button"
                @click="step = 'register'"
                class="text-xs font-semibold text-grey-2 hover:text-black transition-colors">
          &larr; Ubah Data Registrasi
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
