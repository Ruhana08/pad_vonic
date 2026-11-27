@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi Admin')

@section('content')
<div class="w-full max-w-[550px] rounded-[24px] bg-white p-6 sm:p-8 shadow-[0px_11px_20px_0px_rgba(0,0,0,0.09),0px_3px_6px_0px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col gap-6"
     x-data="{
       email: '',
       submitted: false,
       submitting: false,
       errorMsg: '',

       async submitForgot() {
         const raw = this.email.trim();
         if (!raw) {
           this.errorMsg = 'Masukkan alamat email institusi Anda.';
           return;
         }
         this.errorMsg = '';
         this.submitting = true;

         try {
           const res = await fetch('{{ route('password.email') }}', {
             method: 'POST',
             headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             body: JSON.stringify({ email: raw })
           });
           const data = await res.json();
           if (res.ok && data.success) {
             this.submitted = true;
           } else {
             this.errorMsg = data.message || 'Gagal mengirim tautan reset password.';
           }
         } catch (e) {
           this.errorMsg = 'Terjadi kesalahan jaringan atau server.';
         } finally {
           this.submitting = false;
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

  {{-- State 1: Form Input Email --}}
  <div x-show="!submitted" x-transition class="space-y-4">
    <div>
      <h2 class="text-[22px] font-semibold text-[#191919] leading-[1.3]">Lupa Kata Sandi</h2>
      <p class="text-[14px] text-[#6d6d6d] leading-[1.6] mt-0.5 tracking-[0.14px]">
        Masukkan email institusi yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
      </p>
    </div>

    {{-- Error Alert --}}
    <x-alert x-show="errorMsg" x-cloak type="error">
      <span x-text="errorMsg"></span>
    </x-alert>

    <form @submit.prevent="submitForgot()" class="space-y-4">
      <div class="space-y-1.5">
        <label class="block text-[12px] font-bold text-primary-1">Email Institusi</label>
        <div class="flex items-center h-[44px] px-[12px] gap-[12px] rounded-[8px] border border-[#c4c4c4] bg-white focus-within:border-primary-1 focus-within:ring-1 focus-within:ring-primary-1">
          <x-solar-letter-linear class="h-5 w-5 text-[#acacac] shrink-0" />
          <input type="email"
                 x-model="email"
                 required
                 placeholder="nama@mail.ugm.ac.id"
                 class="w-full text-[14px] text-[#191919] placeholder:text-[#acacac] focus:outline-none" />
        </div>
      </div>

      {{-- CTA Button Gradient --}}
      <div class="pt-2">
        <button type="submit"
                :disabled="submitting"
                class="w-full h-[52px] px-[16px] flex items-center justify-center gap-[12px] rounded-[12px] bg-gradient-main-blue text-white text-[14px] font-semibold shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-60">
          <span x-text="submitting ? 'Mengirim tautan...' : 'Kirim Tautan Reset Password'"></span>
          <x-solar-plain-linear class="h-5 w-5 shrink-0" />

        </button>
      </div>

      <div class="pt-2 text-center text-[13px] text-[#6d6d6d]">
        Ingat kata sandi Anda?
        <a href="{{ route('admin.login') }}" class="font-semibold text-primary-1 hover:underline ml-1">
          Kembali ke Log In
        </a>
      </div>
    </form>
  </div>

  {{-- State 2: Sukses Terkirim --}}
  <div x-show="submitted" x-cloak x-transition class="space-y-4 text-center py-4">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-50 text-success">
      <x-solar-check-circle-bold class="h-8 w-8" />
    </div>

    <h2 class="text-[20px] font-bold text-[#191919]">Tautan Terkirim!</h2>
    <p class="text-[13px] text-grey-deep max-w-[380px] mx-auto leading-relaxed">
      Instruksi dan tautan untuk mengatur ulang kata sandi telah dikirimkan ke alamat email:
      <strong class="text-black block mt-1" x-text="email"></strong>
    </p>

    <div class="pt-4">
      <a href="{{ route('admin.login') }}"
         class="inline-flex w-full h-[52px] items-center justify-center gap-2 rounded-[12px] bg-gradient-main-blue text-[14px] font-semibold text-white shadow-primary-glow hover:opacity-95 active:scale-[0.99] transition-all">
        <x-solar-arrow-left-linear class="h-5 w-5" />
        <span>Kembali ke Halaman Log In</span>
      </a>
    </div>
  </div>

</div>
@endsection
