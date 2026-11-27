@extends('layouts.auth')

@section('title', 'Reset Kata Sandi Admin')

@section('content')
<div class="w-full max-w-[550px] rounded-[24px] bg-white p-6 sm:p-8 shadow-[0px_11px_20px_0px_rgba(0,0,0,0.09),0px_3px_6px_0px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col gap-6"
     x-data="{
       password: '',
       password_confirmation: '',
       showPass: false,
       showConfirm: false,
       get hasMinLen() { return this.password.length >= 8; },
       get hasUpper() { return /[A-Z]/.test(this.password); },
       get hasLower() { return /[a-z]/.test(this.password); },
       get hasNumber() { return /[0-9]/.test(this.password); },
       get hasSpecial() { return /[^A-Za-z0-9]/.test(this.password); },
       get isMatch() { return this.password.length > 0 && this.password === this.password_confirmation; },
       get isValid() { return this.hasMinLen && this.hasUpper && this.hasLower && this.hasNumber && this.hasSpecial && this.isMatch; }
     }">

  {{-- Brand Logos Atas --}}
  <div class="flex items-center justify-center gap-10">
    <div class="flex flex-col items-center">
      <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-10 w-auto object-contain" />
      <span class="mt-1 text-sm font-bold text-primary-1 tracking-wider">Vonic</span>
    </div>
    <div class="h-12 w-12 flex items-center justify-center">
      <img src="{{ asset('images/logo-ugm.webp') }}" alt="UGM" class="h-full w-full object-contain" />
    </div>
  </div>

  <div class="space-y-4">
    <div>
      <h2 class="text-[22px] font-semibold text-[#191919] leading-[1.3]">Atur Ulang Kata Sandi</h2>
      <p class="text-[14px] text-[#6d6d6d] leading-[1.6] mt-0.5 tracking-[0.14px]">
        Masukkan kata sandi baru untuk akun admin Anda.
      </p>
    </div>

    @if ($errors->any())
      <x-alert type="error">
        {{ $errors->first() }}
      </x-alert>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="space-y-1.5">
        <label class="block text-[12px] font-bold text-primary-1">Email Institusi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-gray-50">
          <x-solar-letter-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="email"
                 name="email"
                 value="{{ old('email', $email) }}"
                 required
                 placeholder="nama@mail.ugm.ac.id"
                 class="w-full bg-transparent text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="block text-[12px] font-medium text-[#525252]">Kata Sandi Baru</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-lock-password-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input :type="showPass ? 'text' : 'password'"
                 name="password"
                 x-model="password"
                 required
                 minlength="8"
                 placeholder="Minimal 8 karakter"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
          <button type="button" @click="showPass = !showPass" class="text-[#acacac] hover:text-[#525252] cursor-pointer" aria-label="Tampilkan atau sembunyikan kata sandi">
            <x-solar-eye-closed-linear x-show="!showPass" class="h-5 w-5" />
            <x-solar-eye-linear x-show="showPass" x-cloak class="h-5 w-5" />
          </button>
        </div>

        {{-- Live Checklist Aturan Kata Sandi --}}
        <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50/70 p-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-[11px]">
          <div class="flex items-center gap-1.5" :class="hasMinLen ? 'text-success font-semibold' : 'text-grey-deep'">
            <span x-text="hasMinLen ? '✓' : '•'"></span>
            <span>Minimal 8 karakter</span>
          </div>
          <div class="flex items-center gap-1.5" :class="hasUpper ? 'text-success font-semibold' : 'text-grey-deep'">
            <span x-text="hasUpper ? '✓' : '•'"></span>
            <span>Huruf besar (A–Z)</span>
          </div>
          <div class="flex items-center gap-1.5" :class="hasLower ? 'text-success font-semibold' : 'text-grey-deep'">
            <span x-text="hasLower ? '✓' : '•'"></span>
            <span>Huruf kecil (a–z)</span>
          </div>
          <div class="flex items-center gap-1.5" :class="hasNumber ? 'text-success font-semibold' : 'text-grey-deep'">
            <span x-text="hasNumber ? '✓' : '•'"></span>
            <span>Angka (0–9)</span>
          </div>
          <div class="flex items-center gap-1.5 sm:col-span-2" :class="hasSpecial ? 'text-success font-semibold' : 'text-grey-deep'">
            <span x-text="hasSpecial ? '✓' : '•'"></span>
            <span>Karakter spesial (!@#$%^&* dll.)</span>
          </div>
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="block text-[12px] font-medium text-[#525252]">Konfirmasi Kata Sandi Baru</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-lock-password-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input :type="showConfirm ? 'text' : 'password'"
                 name="password_confirmation"
                 x-model="password_confirmation"
                 required
                 minlength="8"
                 placeholder="Ulangi kata sandi baru"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
          <button type="button" @click="showConfirm = !showConfirm" class="text-[#acacac] hover:text-[#525252] cursor-pointer" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi">
            <x-solar-eye-closed-linear x-show="!showConfirm" class="h-5 w-5" />
            <x-solar-eye-linear x-show="showConfirm" x-cloak class="h-5 w-5" />
          </button>
        </div>
        <p x-show="password_confirmation.length > 0 && !isMatch" x-cloak class="text-[11px] font-medium text-danger">
          Konfirmasi kata sandi tidak cocok.
        </p>
        <p x-show="isMatch" x-cloak class="text-[11px] font-medium text-success">
          ✓ Kata sandi cocok.
        </p>
      </div>

      <div class="pt-2">
        <button type="submit"
                :disabled="!isValid"
                class="w-full h-[52px] px-[16px] flex items-center justify-center gap-[12px] rounded-[12px] bg-gradient-main-blue text-white text-[14px] font-semibold shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
          <span>Simpan Kata Sandi Baru</span>
        </button>
      </div>

      <div class="pt-2 text-center text-[13px] text-[#6d6d6d]">
        <a href="{{ route('admin.login') }}" class="font-semibold text-primary-1 hover:underline">
          Kembali ke Log In Admin
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
