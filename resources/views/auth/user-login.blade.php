@extends('layouts.auth')

@section('title', 'Log In Sivitas')

@section('content')
<div class="w-full max-w-[580px] rounded-[24px] bg-white px-7 sm:px-8 pt-8 pb-10 shadow-[0_4px_27.7px_0_rgba(0,0,0,0.04),0_3px_6px_0_rgba(0,0,0,0.1)] flex flex-col items-center gap-10"
     x-data="{
        errorMsg: '{{ session('error') }}',
        infoMsg: '',
        init() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('expired') === '1') {
                this.infoMsg = 'Sesi Anda telah berakhir. Silakan masuk kembali.';
            }
        }
     }">

  {{-- 1. Brand Logos (Vonic & UGM) --}}
  <div class="flex items-center justify-center gap-10">
    <div class="flex flex-col items-center">
      <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-10 w-auto object-contain" />
      <span class="mt-1 text-sm font-bold text-primary-1 tracking-wider">Vonic</span>
    </div>
    <div class="h-12 w-12 flex items-center justify-center">
      <img src="{{ asset('images/logo-ugm.webp') }}" alt="UGM" class="h-full w-full object-contain" />
    </div>
  </div>

  {{-- 2. Teks Utama --}}
  <div class="flex flex-col items-center text-center gap-3">
    <h2 class="text-2xl sm:text-[28px] font-bold text-black tracking-tight leading-tight">
      Halo Sivitas DTEDI!
    </h2>
    <p class="text-sm text-grey-dark max-w-[480px] leading-relaxed">
      Selamat datang di Sistem Pemilihan Destinasi Kegiatan Bersama <span class="font-semibold text-primary-1">Vonic</span>. Silakan masuk untuk memberikan hak suara Anda!
    </p>
  </div>

  {{-- Session Expired Banner --}}
  <div x-show="infoMsg" x-cloak
       class="w-full rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800">
    <div class="flex items-center gap-2">
      <x-solar-info-circle-bold class="h-4 w-4 shrink-0 text-amber-600" />
      <span x-text="infoMsg"></span>
    </div>
  </div>

  {{-- Error dari callback Google --}}
  <div x-show="errorMsg" x-cloak
       class="w-full rounded-xl border border-danger/20 bg-red-50 px-4 py-3 text-xs font-medium text-danger">
    <div class="flex items-center gap-2">
      <x-solar-danger-triangle-bold class="h-4 w-4 shrink-0" />
      <span x-text="errorMsg"></span>
    </div>
  </div>

  {{-- 3. CTA — Langsung redirect ke Google OAuth, tanpa popup --}}
  <div class="w-full">
    <a href="{{ route('auth.google') }}"
       class="group relative flex h-[52px] w-full items-center justify-center gap-3 rounded-[12px] bg-gradient-main-blue px-6 font-semibold text-white shadow-primary-glow transition-all hover:opacity-95 active:scale-[0.99] cursor-pointer">
      {{-- Google icon --}}
      <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#ffffff" fill-opacity="0.9"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#ffffff" fill-opacity="0.75"/>
        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#ffffff" fill-opacity="0.75"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#ffffff" fill-opacity="0.75"/>
      </svg>
      <span class="text-base font-semibold tracking-wide">Login dengan Akun UGM</span>
    </a>
  </div>

</div>
@endsection
