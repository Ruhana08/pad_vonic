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

<body class="bg-[#fafafa] relative min-h-screen w-full font-sans text-black antialiased select-none flex flex-col justify-between overflow-x-hidden">

  <x-flash-message />

  {{-- ===== BACKGROUND ARTWORK LAYER ===== --}}
  <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden flex flex-col justify-between">
    
    {{-- 1. Gradient Glow Atas --}}
    <div class="absolute left-0 top-0 w-full h-[60%] pointer-events-none z-0">
      <img src="{{ asset('images/auth/top-glow-gradient.svg') }}" alt="" class="w-full h-full object-cover object-top" />
    </div>

    {{-- 2. Aset Gabungan Garis Lintasan + Pesawat Kertas (Ekstra Besar & Melebar ke Luar Sisi Layar) --}}
    <div class="absolute -top-16 sm:-top-14 md:-top-12 left-1/2 -translate-x-1/2 w-[215vw] min-w-[2500px] h-[92%] pointer-events-none z-10 opacity-95">
      <img src="{{ asset('images/auth/flight-plane-combined.svg') }}" 
           alt="Flight Path and Paper Plane" 
           class="w-full h-full object-contain object-top" />
    </div>

    {{-- 3. BOX ISOMETRIC BAWAH (Tetap pada ukuran sebelumnya yang sudah pas) --}}
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[112vw] min-w-[1440px] pointer-events-none z-0">
      <img src="{{ asset('images/auth/isometric-boxes.svg') }}" 
           alt="Box Image Admin" 
           class="w-full h-auto object-cover object-bottom block" />
    </div>
  </div>

  {{-- ===== HEADER JUDUL ATAS ===== --}}
  <header class="relative z-20 pt-10 sm:pt-14 md:pt-16 pb-2 text-center px-4 shrink-0">
    <div class="relative inline-block max-w-[90vw]">
      {{-- Layer Stroke Putih (Outside Stroke Effect 8px) --}}
      <div class="absolute inset-0 pointer-events-none select-none aria-hidden:true" aria-hidden="true"
           style="filter: drop-shadow(0 2px 4px rgba(25, 107, 239, 0.15));">
        <h1 class="text-[clamp(1.35rem,2.2vw,32px)] font-semibold tracking-[-0.96px] text-white"
            style="-webkit-text-stroke: 8px #ffffff; paint-order: stroke fill;">
          Departemen Teknik Elektro dan Informatika
        </h1>
        <p class="text-[clamp(1.1rem,1.9vw,28px)] font-semibold text-white tracking-[-0.84px] leading-[135%] mt-0.5"
           style="-webkit-text-stroke: 8px #ffffff; paint-order: stroke fill;">
          Universitas Gadjah Mada
        </p>
      </div>

      {{-- Layer Isi Text Gradient & Warna Mengkilat --}}
      <div class="relative z-10">
        <h1 class="text-[clamp(1.35rem,2.2vw,32px)] font-semibold tracking-[-0.96px] bg-clip-text text-transparent"
            style="background: linear-gradient(90deg, #6EA5FF 0%, #196BEF 27.41%, #196BEF 68.75%, #73A7FC 85.1%, #196BEF 94.23%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
          Departemen Teknik Elektro dan Informatika
        </h1>
        <p class="text-[clamp(1.1rem,1.9vw,28px)] font-semibold text-[#053788] tracking-[-0.84px] leading-[135%] mt-0.5">
          Universitas Gadjah Mada
        </p>
      </div>
    </div>
  </header>

  {{-- ===== KONTEN FORM ===== --}}
  <main class="relative z-20 my-auto flex justify-center items-center px-4 py-6">
    <div class="relative w-full max-w-[480px] sm:max-w-[520px]">
      
      {{-- Card Putih Form --}}
      @yield('content')

    </div>
  </main>

  {{-- ===== FOOTER BAWAH ===== --}}
  <footer class="relative z-20 pb-4 pt-2 text-center px-4 shrink-0">
    <div class="flex items-center justify-center gap-3 text-white opacity-90">
      <div class="flex items-center gap-2">
        <img src="{{ asset('images/logo-vonic-w.webp') }}" alt="Vonic" class="h-4 sm:h-5 w-auto" />
        <span class="text-base sm:text-lg font-bold tracking-[1.5px]">Vonic</span>
      </div>
      <span class="text-[11px] font-medium tracking-wide opacity-80">&copy; 2026 DTEDI SV UGM</span>
    </div>
  </footer>

</body>
</html>
