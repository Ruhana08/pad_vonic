<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>@yield('title', 'Form') | Vonic</title>

     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Skranji:wght@400;700&display=swap" rel="stylesheet">

     @vite(['resources/css/app.css', 'resources/js/app.js'])
     <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
     <style>
          [x-cloak] {
               display: none !important;
          }
     </style>
</head>

<body class="bg-gray-50 font-sans text-black antialiased">

     <header class="fixed inset-x-0 top-0 z-30 flex h-[72px] items-center gap-6 border-b border-grey-1 bg-white px-8">
          {{-- GANTI dengan markup logo Vonic dari layouts/partials/navbar.blade.php --}}
          <span class="font-skranji text-2xl font-bold text-primary-1">Vonic</span>

          <span class="h-6 w-px bg-grey-1"></span>
          <h1 class="text-sm text-grey-2">@yield('page_title')</h1>

          <div class="ml-auto flex items-center gap-3">
               @yield('header_actions')
          </div>
     </header>

     <main class="mx-auto @yield('max_width', 'max-w-[1100px]') px-4 pb-[96px] pt-[96px]">
          @yield('content')
     </main>

     @include('layouts.partials.footer')

</body>

</html>