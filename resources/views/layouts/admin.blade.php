<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') | Vonic</title>

  <!-- Font: Be Vietnam Pro -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

  <script src="{{ asset('vendor/alpine.min.js') }}" defer></script>
  <link rel="stylesheet" href="{{ asset('vendor/flatpickr.min.css') }}">
  <script src="{{ asset('vendor/flatpickr.min.js') }}" defer></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <script>
    // Mempertahankan posisi scroll saat refresh / navigasi form pagination
    document.addEventListener('DOMContentLoaded', () => {
      const scrollPos = sessionStorage.getItem('vonic_scroll_pos');
      if (scrollPos !== null) {
        window.scrollTo({ top: parseInt(scrollPos, 10), behavior: 'instant' });
        sessionStorage.removeItem('vonic_scroll_pos');
      }

      window.addEventListener('beforeunload', () => {
        sessionStorage.setItem('vonic_scroll_pos', window.scrollY);
      });
    });

    // Support jika ada navigasi via Turbo
    document.addEventListener('turbo:render', () => {
      const scrollPos = sessionStorage.getItem('vonic_scroll_pos');
      if (scrollPos !== null) {
        window.scrollTo({ top: parseInt(scrollPos, 10), behavior: 'instant' });
        sessionStorage.removeItem('vonic_scroll_pos');
      }
    });
  </script>

  <style>[x-cloak] { display: none !important; }</style>
</head>

<body x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="flex min-h-screen flex-col overflow-x-hidden bg-[#FAFAFA] font-sans text-black antialiased">

  <x-flash-message />

  @include('layouts.partials.navbar')
  @include('layouts.partials.sidebar')

  <!--
    Margin kiri desktop = sidebar total width (224px) + gap ke konten (16px) = 240px
    Padding atas = jarak atas navbar (16px) + tinggi navbar (80px) + gap ke konten (32px) = 128px
    Padding bawah = ruang napas konten sebelum footer = 48px
  -->
  <main class="ml-0 lg:ml-[240px] min-w-0 flex-1 px-4 lg:pl-0 lg:pr-4 pb-[48px] pt-[112px]">
    @yield('content')
  </main>

  @include('layouts.partials.footer')

</body>
</html>
