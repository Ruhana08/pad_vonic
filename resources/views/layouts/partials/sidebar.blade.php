@php
  $isPollingActive = request()->routeIs('admin.polling.*');

  // Menu tunggal
  $links = [
    [
      'label'  => 'Dashboard',
      'route'  => 'admin.dashboard',
      'active' => request()->routeIs('admin.dashboard'),
      'icon'   => 'solar-widget-2-linear',
    ],
    [
      'label'  => 'Akun Pengguna',
      'route'  => 'admin.akun.index',
      'active' => request()->routeIs('admin.akun.*'),
      'icon'   => 'solar-user-linear',
    ],
  ];

  if (auth()->user()?->isMasterAdmin()) {
    $links[] = [
      'label'  => 'Kelola Akun Admin',
      'route'  => 'admin.kelola-admin.index',
      'active' => request()->routeIs('admin.kelola-admin.*'),
      'icon'   => 'solar-shield-user-linear',
    ];
  }

  // Submenu Polling
  $pollingMenu = [
    ['label' => 'Polling Aktif',   'route' => 'admin.polling.aktif', 'tag' => 'Aktif'],
    ['label' => 'Riwayat / Arsip', 'route' => 'admin.polling.arsip', 'tag' => 'Arsip'],
  ];
  $activeSubmenu = collect($pollingMenu)->first(fn ($m) => request()->routeIs($m['route']))['tag'] ?? null;

  // Class card menu Figma: bayangan presisi drop-shadow 4px/9px dan radius 8px (rounded-sm)
  $btnBase   = 'flex h-12 w-full items-center rounded-sm text-sm font-medium transition-all duration-200 shadow-menu shrink-0';
  $btnIdle   = 'bg-white text-black hover:bg-primary-3/60 hover:text-primary-1 group';
  $btnActive = 'bg-primary-1 text-white font-bold shadow-primary-glow';

  // Class submenu
  $subBase   = 'flex h-11 w-full items-center rounded-sm px-3 text-xs font-medium transition-all duration-200 shadow-menu';
  $subIdle   = 'bg-white text-black hover:bg-primary-3 hover:text-primary-1';
  $subActive = 'bg-primary-1 text-white font-semibold';
@endphp

<!-- Sidebar Desktop: outer wrapper provides padding so item shadows are not clipped -->
<aside class="fixed top-[128px] bottom-4 left-0 z-20 hidden lg:flex w-[224px] flex-col select-none overflow-visible pl-4 pr-3 pt-1">

  <!-- Navigasi -->
  <nav class="flex flex-col gap-4">

    <!-- Tambah Polling -->
    <a href="{{ route('admin.polling.create') }}" data-turbo="false"
       class="flex h-12 w-full items-center justify-center gap-2 rounded-sm bg-gradient-to-r from-primary-1 to-secondary-1 text-sm font-semibold text-white shadow-primary-glow transition-all duration-200 hover:opacity-90 shrink-0">
      <x-solar-add-circle-linear class="h-5 w-5" />
      <span>Tambah Polling</span>
    </a>

    <!-- Dashboard dan Akun Pengguna -->
    @foreach ($links as $link)
      <a href="{{ route($link['route']) }}"
         class="{{ $btnBase }} {{ $link['active'] ? $btnActive : $btnIdle }} justify-start px-3.5">
        <x-dynamic-component :component="$link['icon']"
          class="h-5 w-5 shrink-0 transition-colors duration-200 {{ $link['active'] ? 'text-white' : 'text-gray-700 group-hover:text-primary-1' }}" />
        <span class="ml-3 truncate text-sm {{ $link['active'] ? 'font-bold text-white' : 'font-medium' }}">
          {{ $link['label'] }}
        </span>
      </a>
    @endforeach

    <!-- Polling Menu (Accordion Auto Hover) -->
    <div class="relative w-full shrink-0"
         x-data="{ isHovered: false }"
         @mouseenter="isHovered = true"
         @mouseleave.debounce.150ms="isHovered = false">

      <!-- Tombol Induk Polling -->
      <button class="{{ $btnBase }} {{ $isPollingActive ? $btnActive : $btnIdle }} justify-between px-3.5">
        <div class="flex items-center truncate">
          <x-solar-clipboard-check-linear
            class="h-5 w-5 shrink-0 transition-colors duration-200 {{ $isPollingActive ? 'text-white' : 'text-gray-700 group-hover:text-primary-1' }}" />

          <div class="ml-3 flex items-center truncate text-sm font-medium">
            <span>Polling</span>

            @if ($activeSubmenu)
              <span class="ml-1 font-semibold">\ {{ $activeSubmenu }}</span>
            @else
              <span x-show="isHovered" x-cloak
                    class="ml-1 transition-opacity duration-200 {{ $isPollingActive ? 'text-white/60' : 'text-gray-400' }}">
                \ ...
              </span>
            @endif
          </div>
        </div>

        <x-solar-alt-arrow-down-linear
          class="h-4 w-4 shrink-0 transition-transform duration-200"
          ::class="isHovered ? 'rotate-180' : ''" />
      </button>

      <!-- Submenu: Muncul Saat Hover -->
      <div class="grid transition-all duration-300 ease-in-out"
           :class="isHovered ? 'mt-2.5 grid-rows-[1fr] opacity-100' : 'mt-0 grid-rows-[0fr] opacity-0'">
        <div class="flex flex-col gap-2.5 overflow-visible px-2 py-2 -mx-2 -my-2">
          @foreach ($pollingMenu as $sub)
            <a href="{{ route($sub['route']) }}"
               class="{{ $subBase }} {{ request()->routeIs($sub['route']) ? $subActive : $subIdle }}">
              <span class="truncate">{{ $sub['label'] }}</span>
            </a>
          @endforeach
        </div>
      </div>

    </div>

  </nav>

</aside>

<!-- Sidebar Mobile Drawer (< lg) -->
<div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
  <div x-show="sidebarOpen"
       x-transition:enter="transition-opacity ease-out duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity ease-in duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       @click="sidebarOpen = false"
       class="fixed inset-0 bg-black/35 backdrop-blur-xs"></div>

  <aside x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col bg-[#FAFAFA] p-4 shadow-2xl overflow-y-auto">

    <div class="mb-5 flex items-center justify-between border-b border-grey-1 pb-3">
      <div class="flex items-center gap-2.5">
        <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-7 w-auto" onerror="this.style.display='none'">
        <span class="font-sans text-lg font-bold text-primary-1">Admin Area</span>
      </div>
      <button type="button" @click="sidebarOpen = false" aria-label="Tutup Menu" class="rounded-lg p-1.5 text-grey-2 hover:bg-gray-100 hover:text-black cursor-pointer">
        <x-solar-close-circle-linear class="h-5 w-5" />
      </button>
    </div>

    <nav class="flex flex-col gap-3">
      <a href="{{ route('admin.polling.create') }}" data-turbo="false" @click="sidebarOpen = false"
         class="flex h-12 w-full items-center justify-center gap-2 rounded-sm bg-gradient-to-r from-primary-1 to-secondary-1 text-sm font-semibold text-white shadow-primary-glow transition-all duration-200 hover:opacity-90 shrink-0">
        <x-solar-add-circle-linear class="h-5 w-5" />
        <span>Tambah Polling</span>
      </a>

      @foreach ($links as $link)
        <a href="{{ route($link['route']) }}" @click="sidebarOpen = false"
           class="{{ $btnBase }} {{ $link['active'] ? $btnActive : $btnIdle }} justify-start px-3.5">
          <x-dynamic-component :component="$link['icon']"
            class="h-5 w-5 shrink-0 {{ $link['active'] ? 'text-white' : 'text-gray-700' }}" />
          <span class="ml-3 truncate text-sm {{ $link['active'] ? 'font-bold text-white' : 'font-medium' }}">
            {{ $link['label'] }}
          </span>
        </a>
      @endforeach

      <div class="relative w-full shrink-0" x-data="{ openSub: {{ $isPollingActive ? 'true' : 'false' }} }">
        <button type="button" @click="openSub = !openSub"
                class="{{ $btnBase }} {{ $isPollingActive ? $btnActive : $btnIdle }} justify-between px-3.5 cursor-pointer">
          <div class="flex items-center truncate">
            <x-solar-clipboard-check-linear class="h-5 w-5 shrink-0 {{ $isPollingActive ? 'text-white' : 'text-gray-700' }}" />
            <div class="ml-3 flex items-center truncate text-sm font-medium">
              <span>Polling</span>
              @if ($activeSubmenu)
                <span class="ml-1 font-semibold">\ {{ $activeSubmenu }}</span>
              @endif
            </div>
          </div>
          <x-solar-alt-arrow-down-linear class="h-4 w-4 shrink-0 transition-transform duration-200" ::class="openSub ? 'rotate-180' : ''" />
        </button>

        <div x-show="openSub" x-cloak class="mt-2.5 flex flex-col gap-2 pl-2">
          @foreach ($pollingMenu as $sub)
            <a href="{{ route($sub['route']) }}" @click="sidebarOpen = false"
               class="{{ $subBase }} {{ request()->routeIs($sub['route']) ? $subActive : $subIdle }}">
              <span class="truncate">{{ $sub['label'] }}</span>
            </a>
          @endforeach
        </div>
      </div>
    </nav>
  </aside>
</div>
