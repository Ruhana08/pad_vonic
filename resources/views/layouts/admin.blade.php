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

  <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="flex min-h-screen flex-col bg-[#FAFAFA] font-sans text-black antialiased">

  <x-flash-message />

  @include('layouts.partials.navbar')
  @include('layouts.partials.sidebar')

  <!--
    Margin kiri = sidebar total width (224px) + gap ke konten (16px) = 240px
    Padding atas = jarak atas navbar (16px) + tinggi navbar (80px) + gap ke konten (16px) = 112px
    Padding bawah = ruang napas konten sebelum footer = 48px
  -->
  <main class="ml-[240px] flex-1 pb-[48px] pr-4 pt-[112px]">
    @yield('content')
  </main>

  @include('layouts.partials.footer')

</body>
</html>
