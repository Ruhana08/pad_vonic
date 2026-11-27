<!DOCTYPE html>
<html lang="id">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>@yield('title', 'Voting') | Vonic</title>

     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

     <script src="{{ asset('vendor/alpine.min.js') }}"></script>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
     <style>
          [x-cloak] {
               display: none !important;
          }
     </style>
</head>

<body class="flex min-h-screen flex-col bg-gray-50 font-sans text-black antialiased">

     <x-flash-message />

     <!-- Top Header Focus -->
     <header class="fixed inset-x-0 top-0 z-30 flex h-[72px] items-center gap-3 sm:gap-6 border-b border-grey-1 bg-white px-4 sm:px-8 shadow-xs">
          <div class="flex items-center gap-2.5 sm:gap-3">
               <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-6 sm:h-7 w-auto" onerror="this.style.display='none'">
               <span class="font-sans text-xl sm:text-2xl font-bold tracking-tight text-primary-1">Vonic</span>
          </div>

          <span class="h-6 w-px bg-grey-1"></span>
          <h1 class="text-sm sm:text-base font-bold text-primary-1 truncate">@yield('page_title')</h1>
     </header>

     <!-- Main Canvas: Tanpa footer, nembus ke bawah -->
     <main class="relative flex-1 pt-[72px]">
          @yield('content')
     </main>

</body>

</html>
