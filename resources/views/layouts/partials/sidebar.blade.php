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

  // Submenu Polling
  $pollingMenu = [
    ['label' => 'Polling Aktif',   'route' => 'admin.polling.aktif', 'tag' => 'Aktif'],
    ['label' => 'Riwayat / Arsip', 'route' => 'admin.polling.arsip', 'tag' => 'Arsip'],
  ];
  $activeSubmenu = collect($pollingMenu)->first(fn ($m) => request()->routeIs($m['route']))['tag'] ?? null;

  // Class card menu Figma: bayangan presisi drop-shadow 4px/9px dan radius 8px (rounded-sm)
  $btnBase   = 'flex h-12 w-full items-center rounded-sm text-sm font-medium transition-all duration-200 shadow-menu shrink-0';
  $btnIdle   = 'bg-white text-black hover:bg-primary-3/60 hover:text-primary-1';
  $btnActive = 'bg-primary-1 text-white font-bold shadow-primary-glow';

  // Class submenu
  $subBase   = 'flex h-11 w-full items-center rounded-sm px-3 text-xs font-medium transition-all duration-200 shadow-menu';
  $subIdle   = 'bg-white text-black hover:bg-primary-3 hover:text-primary-1';
  $subActive = 'bg-primary-1 text-white font-semibold';
@endphp

<!-- Sidebar: outer wrapper provides padding so item shadows are not clipped -->
<aside class="fixed top-[112px] bottom-4 left-0 z-20 flex w-[224px] flex-col select-none overflow-visible pl-4 pr-3 pt-1">

  <!-- Navigasi -->
  <nav class="flex flex-col gap-4">

    <!-- Dashboard dan Akun Pengguna -->
    @foreach ($links as $link)
      <a href="{{ route($link['route']) }}"
         class="{{ $btnBase }} {{ $link['active'] ? $btnActive : $btnIdle }} justify-start px-3.5">
        <x-dynamic-component :component="$link['icon']"
          class="h-5 w-5 shrink-0 {{ $link['active'] ? 'text-white' : 'text-gray-700' }}" />
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
            class="h-5 w-5 shrink-0 {{ $isPollingActive ? 'text-white' : 'text-gray-700' }}" />

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
