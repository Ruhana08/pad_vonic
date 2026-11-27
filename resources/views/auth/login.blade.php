@extends('layouts.auth')

@section('title', 'Log In Admin')

@section('content')
<div class="w-full max-w-[550px] rounded-[24px] bg-white p-6 sm:p-8 shadow-[0px_11px_20px_0px_rgba(0,0,0,0.09),0px_3px_6px_0px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col gap-5"
     x-data="{
       email: '{{ old('email') }}',
       password: '',
       showPassword: false,
       loading: false,
       errorMessage: '{{ $errors->first() ?: session('error') }}',
       successMessage: '{{ session('success') }}',

       async submitLogin() {
         this.loading = true;
         this.errorMessage = '';
         this.successMessage = '';

         try {
           const res = await fetch('{{ route('login.submit') }}', {
             method: 'POST',
             headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             body: JSON.stringify({
               email: this.email,
               password: this.password
             })
           });

           const data = await res.json();

           if (res.ok && data.success) {
             this.successMessage = data.message || 'Login berhasil! Mengalihkan ke dashboard...';
             window.location.href = data.redirect || '{{ route('admin.dashboard') }}';
           } else {
             if (data.errors) {
               this.errorMessage = Object.values(data.errors).flat().join(' ');
             } else {
               this.errorMessage = data.message || 'Email atau kata sandi tidak valid.';
             }
           }
         } catch (err) {
           this.errorMessage = 'Terjadi kesalahan jaringan atau server.';
         } finally {
           this.loading = false;
         }
       }
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

  {{-- Alert Pesan Sukses / Error --}}
  <template x-if="errorMessage">
    <x-alert type="error"><span x-text="errorMessage"></span></x-alert>
  </template>
  <template x-if="successMessage">
    <x-alert type="success"><span x-text="successMessage"></span></x-alert>
  </template>

  {{-- Form Login Email + Password --}}
  <div class="space-y-[16px]">
    <div>
      <h2 class="text-[22px] font-semibold text-[#191919] leading-[1.3]">Log In Admin</h2>
      <p class="text-[14px] text-[#6d6d6d] leading-[1.6] mt-0.5 tracking-[0.14px]">
        Masukkan email institusi dan kata sandi Anda untuk mengakses panel admin.
      </p>
    </div>

    <form @submit.prevent="submitLogin()" method="POST" action="{{ route('login.submit') }}" class="flex flex-col gap-3">
      @csrf

      {{-- Input Email Institusi --}}
      <div class="flex flex-col gap-1.5">
        <label class="block text-[12px] font-medium text-[#525252]">Email Institusi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-letter-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="email"
                 name="email"
                 x-model="email"
                 required
                 placeholder="nama@mail.ugm.ac.id"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
        </div>
      </div>

      {{-- Input Kata Sandi --}}
      <div class="flex flex-col gap-1.5">
        <div class="flex items-center justify-between">
          <label class="block text-[12px] font-medium text-[#525252]">Kata Sandi</label>
          <a href="{{ route('password.request') }}" class="text-[12px] font-semibold text-[#196bef] hover:underline">
            Lupa Kata Sandi?
          </a>
        </div>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-lock-password-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input :type="showPassword ? 'text' : 'password'"
                 name="password"
                 x-model="password"
                 required
                 placeholder="Masukkan kata sandi"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
          <button type="button" @click="showPassword = !showPassword" class="text-[#acacac] hover:text-[#525252] cursor-pointer">
            <x-solar-eye-closed-linear x-show="!showPassword" class="h-5 w-5" />
            <x-solar-eye-linear x-show="showPassword" x-cloak class="h-5 w-5" />
          </button>
        </div>
      </div>

      {{-- Tombol Login --}}
      <div class="pt-[6px]">
        <button type="submit"
                :disabled="loading"
                class="w-full h-[52px] px-[16px] flex items-center justify-center gap-[12px] rounded-[12px] bg-gradient-main-blue text-white text-[14px] font-semibold shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50">
          <x-solar-login-2-linear class="h-5 w-5 shrink-0" />
          <span x-text="loading ? 'Memverifikasi...' : 'Masuk ke Dashboard'"></span>
        </button>
      </div>

    </form>
  </div>

</div>
@endsection
