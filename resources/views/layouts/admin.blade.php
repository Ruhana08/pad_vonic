<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') | Vonic</title>

  <!-- Font: Be Vietnam Pro (isi) dan Skranji (judul Admin Area) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Skranji:wght@400;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Alpine.js: state collapse sidebar dan flyout hover -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="bg-gray-50 font-sans text-black antialiased"
      x-data="{
        isCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        ready: false
      }"
      x-init="requestAnimationFrame(() => requestAnimationFrame(() => ready = true))"
      @sidebar-toggle.window="isCollapsed = $event.detail">

  @include('layouts.partials.navbar')
  @include('layouts.partials.sidebar')

  <!--
    Margin kiri = jarak kiri sidebar (16) + lebar sidebar + gap ke konten (16)
      terbuka  : 16 + 240 + 16 = 272px
      collapsed: 16 + 64  + 16 = 96px
    Padding atas = tinggi navbar (72) + gap (16) = 88px
    Padding bawah = tinggi footer (64) + gap (16) = 80px
  -->
  <main :class="[isCollapsed ? 'ml-[96px]' : 'ml-[272px]', ready ? 'transition-[margin] duration-300 ease-in-out' : '']"
        class="min-h-screen pb-[80px] pr-4 pt-[88px]">
    @yield('content')
  </main>

  @include('layouts.partials.footer')

</body>
</html>