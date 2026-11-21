<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Autentikasi Admin') | Vonic</title>

  <!-- Font: Be Vietnam Pro -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

  <script src="{{ asset('vendor/alpine.min.js') }}"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    [x-cloak] { display: none !important; }
  </style>
</head>

<body class="bg-[#fafafa] relative h-screen w-full font-sans text-black antialiased select-none flex flex-col justify-between overflow-hidden">

  <x-flash-message />

  {{-- ===== BACKGROUND ARTWORK LAYER ===== --}}
  <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
    
    {{-- 1. Gradient Glow Atas --}}
    <div class="absolute left-0 top-0 w-full h-[55%] pointer-events-none z-0">
      <img src="{{ asset('images/auth/top-glow-gradient.svg') }}" alt="" class="w-full h-full object-cover object-top" />
    </div>

    {{-- 2. Aset Gabungan Garis Lintasan + Pesawat Kertas --}}
    <div class="absolute -top-12 sm:-top-10 md:-top-8 left-1/2 -translate-x-1/2 w-[215vw] min-w-[2500px] h-[90%] pointer-events-none z-10 opacity-95">
      <img src="{{ asset('images/auth/flight-plane-combined.svg') }}" 
           alt="Flight Path and Paper Plane" 
           class="w-full h-full object-contain object-top" />
    </div>

    {{-- 3. BOX ISOMETRIC BAWAH (Original Utuh Sesuai Figma Asli Tanpa Cropping Yang Memotong Sayap Box) --}}
    <div class="absolute -bottom-8 sm:-bottom-6 md:-bottom-4 left-1/2 -translate-x-1/2 w-[112vw] min-w-[1440px] pointer-events-none z-0 overflow-hidden flex items-end justify-center">
      <img src="{{ asset('images/auth/isometric-boxes.svg') }}" 
           alt="Box Image Admin" 
           class="w-full h-auto object-cover object-bottom block" />
    </div>
  </div>

  {{-- ===== MAIN WRAPPER (Judul + Pop-Up Bersatu Dekat Sesuai Desain Figma) ===== --}}
  <main class="relative z-20 flex-1 flex flex-col justify-center items-center px-4 py-2 min-h-0 overflow-y-auto">
    
    <div class="flex flex-col items-center w-full max-w-[620px] my-auto gap-8 sm:gap-10">
      
      {{-- Judul Departemen & Universitas (Wajib 1 Baris Masing-Masing, Font Beda Sedikit Saja) --}}
      <div class="relative text-center shrink-0 w-full">
        {{-- Layer Stroke Putih (Outside Stroke Effect 8px) --}}
        <div class="absolute inset-0 pointer-events-none select-none aria-hidden:true" aria-hidden="true"
             style="filter: drop-shadow(0 2px 4px rgba(25, 107, 239, 0.15));">
          <h1 class="text-[clamp(1.15rem,1.7vw,28px)] font-bold tracking-[-0.6px] text-white whitespace-nowrap"
              style="-webkit-text-stroke: 8px #ffffff; paint-order: stroke fill;">
            Departemen Teknik Elektro dan Informatika
          </h1>
          <p class="text-[clamp(1.05rem,1.5vw,25px)] font-bold text-white tracking-[-0.5px] leading-[135%] mt-1 whitespace-nowrap"
             style="-webkit-text-stroke: 8px #ffffff; paint-order: stroke fill;">
            Universitas Gadjah Mada
          </p>
        </div>

        {{-- Layer Isi Text Gradient & Warna Mengkilat --}}
        <div class="relative z-10">
          <h1 class="text-[clamp(1.15rem,1.7vw,28px)] font-bold tracking-[-0.6px] bg-clip-text text-transparent whitespace-nowrap"
              style="background: linear-gradient(90deg, #6EA5FF 0%, #196BEF 27.41%, #196BEF 68.75%, #73A7FC 85.1%, #196BEF 94.23%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Departemen Teknik Elektro dan Informatika
          </h1>
          <p class="text-[clamp(1.05rem,1.5vw,25px)] font-bold text-[#053788] tracking-[-0.5px] leading-[135%] mt-1 whitespace-nowrap">
            Universitas Gadjah Mada
          </p>
        </div>
      </div>

      {{-- Card Putih Form / Pop-Up --}}
      <div class="w-full flex justify-center">
        @yield('content')
      </div>

    </div>

  </main>

  {{-- ===== FOOTER BAWAH (Selalu Terlihat di 1 Layar Tanpa Scroll) ===== --}}
  <footer class="relative z-20 pb-2.5 pt-1 text-center px-4 shrink-0">
    <div class="flex items-center justify-center gap-3 text-white opacity-95 drop-shadow-[0_1px_3px_rgba(0,0,0,0.6)]">
      <div class="flex items-center gap-2">
        <img src="{{ asset('images/logo-vonic-w.webp') }}" alt="Vonic" class="h-4 sm:h-4.5 w-auto" />
        <span class="text-sm sm:text-base font-bold tracking-[1.5px]">Vonic</span>
      </div>
      <span class="text-[11px] font-medium tracking-wide opacity-90">&copy; 2026 DTEDI SV UGM</span>
    </div>
  </footer>

</body>
</html>
