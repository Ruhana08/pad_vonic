@php
  $isPollingActive = request()->routeIs('admin.polling.*');

  // Menu tunggal (selain Polling)
  $links = [
    [
      'label'  => 'Dashboard',
      'route'  => 'admin.dashboard',
      'active' => request()->routeIs('admin.dashboard'),
      'icon'   => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    ],
    [
      'label'  => 'Akun Pengguna',
      'route'  => 'admin.akun.index',
      'active' => request()->routeIs('admin.akun.*'),
      'icon'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    ],
  ];

  // Submenu Polling (dipakai accordion DAN flyout)
  $pollingMenu = [
    ['label' => 'Polling Aktif',   'route' => 'admin.polling.aktif', 'tag' => 'Aktif'],
    ['label' => 'Riwayat / Arsip', 'route' => 'admin.polling.arsip', 'tag' => 'Arsip'],
  ];
  $activeSubmenu = collect($pollingMenu)->first(fn ($m) => request()->routeIs($m['route']))['tag'] ?? null;

  // Class menu utama (dikunci h-11 w-full shrink-0 agar tidak bisa mengecil)
  $btnBase   = 'flex h-11 w-full items-center rounded-xl border text-sm font-medium transition-all duration-200 shadow-xs shrink-0';
  $btnIdle   = 'border-gray-300 bg-white text-gray-700 hover:border-primary-1 hover:bg-primary-3 hover:text-primary-1';
  $btnActive = 'border-primary-1 bg-primary-1 text-white shadow-md';

  // Class submenu
  $subBase   = 'flex h-10 w-full items-center rounded-xl border px-3 text-xs font-medium transition-all duration-200 shadow-xs';
  $subIdle   = 'border-gray-200 bg-white text-gray-700 hover:border-primary-1 hover:bg-primary-3 hover:text-primary-1';
  $subActive = 'border-primary-1 bg-primary-1 text-white font-semibold';

  // Tooltip saat collapsed
  $tooltip = 'absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg';
@endphp

<!--
  Sidebar melayang: gap 16px ke navbar (72), footer (64), dan tepi kiri layar.
  JANGAN beri overflow-hidden di sini, flyout Polling akan terpotong.
-->
<aside x-data="{
         collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
         ready: false,
         toggleSidebar() {
           this.collapsed = !this.collapsed;
           localStorage.setItem('sidebar_collapsed', this.collapsed);
           window.dispatchEvent(new CustomEvent('sidebar-toggle', { detail: this.collapsed }));
         }
       }"
       x-init="requestAnimationFrame(() => requestAnimationFrame(() => ready = true))"
       :class="[collapsed ? 'w-[64px]' : 'w-[240px]', ready ? 'transition-all duration-300 ease-in-out' : '']"
       class="fixed top-[88px] bottom-[80px] left-4 z-30 flex flex-col rounded-xl border border-gray-200 bg-white select-none">

  <!-- Header toggle -->
  <div class="flex h-12 w-full shrink-0 items-center justify-between border-b border-gray-100 px-2">
    <span x-show="!collapsed" class="truncate pl-2 text-[11px] font-bold uppercase tracking-wider text-gray-700">
      Menu Utama
    </span>

    <button @click="toggleSidebar()"
            :class="collapsed ? 'mx-auto text-primary-1' : 'text-gray-700 hover:text-primary-1'"
            :title="collapsed ? 'Perluas Sidebar' : 'Sembunyikan Sidebar'"
            :aria-label="collapsed ? 'Perluas Sidebar' : 'Sembunyikan Sidebar'"
            class="flex h-8 w-8 shrink-0 items-center justify-center transition-colors">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <rect width="18" height="18" x="3" y="3" rx="2" />
        <path d="M9 3v18" />
      </svg>
    </button>
  </div>

  <!-- Navigasi (tanpa overflow supaya flyout tidak terpotong) -->
  <nav class="flex flex-col gap-2.5 p-2">

    <!-- Dashboard dan Akun Pengguna -->
    @foreach ($links as $link)
      <div class="relative w-full shrink-0" x-data="{ tooltip: false }">
        <a href="{{ route($link['route']) }}"
           @mouseenter="if (collapsed) tooltip = true"
           @mouseleave="tooltip = false"
           :class="collapsed ? 'justify-center px-0' : 'justify-between px-3'"
           class="{{ $btnBase }} {{ $link['active'] ? $btnActive : $btnIdle }}">
          <div class="flex items-center truncate" :class="collapsed ? 'w-full justify-center' : ''">
            <svg class="h-5 w-5 shrink-0 {{ $link['active'] ? 'text-white' : 'text-gray-700' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}" />
            </svg>
            <span x-show="!collapsed" class="ml-2.5 truncate text-sm font-medium">{{ $link['label'] }}</span>
          </div>
        </a>

        <div x-show="collapsed && tooltip" x-cloak class="{{ $tooltip }}">
          {{ $link['label'] }}
        </div>
      </div>
    @endforeach

    <!-- Polling -->
    <div class="relative w-full shrink-0"
         x-data="{ isHovered: false }"
         @mouseenter="isHovered = true"
         @mouseleave.debounce.150ms="isHovered = false">

      <!-- Tombol induk -->
      <button :class="collapsed ? 'justify-center px-0' : 'justify-between px-3'"
              class="{{ $btnBase }} {{ $isPollingActive ? $btnActive : $btnIdle }}">
        <div class="flex items-center truncate" :class="collapsed ? 'w-full justify-center' : ''">
          <svg class="h-5 w-5 shrink-0 {{ $isPollingActive ? 'text-white' : 'text-gray-700' }}"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>

          <div x-show="!collapsed" class="ml-2.5 flex items-center truncate text-sm font-medium">
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
      </button>

      <!-- A. Submenu terbuka: accordion ke bawah -->
      <div x-show="!collapsed"
           class="grid transition-all duration-300 ease-in-out"
           :class="isHovered ? 'mt-2 grid-rows-[1fr] opacity-100' : 'mt-0 grid-rows-[0fr] opacity-0'">
        <div class="flex flex-col gap-2 overflow-hidden">
          @foreach ($pollingMenu as $sub)
            <a href="{{ route($sub['route']) }}"
               class="{{ $subBase }} {{ request()->routeIs($sub['route']) ? $subActive : $subIdle }}">
              <span class="truncate">{{ $sub['label'] }}</span>
            </a>
          @endforeach
        </div>
      </div>

      <!-- B. Submenu collapsed: flyout di samping, tanpa kotak pembungkus -->
      <div x-show="collapsed && isHovered" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 translate-x-1"
           x-transition:enter-end="opacity-100 translate-x-0"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-x-0"
           x-transition:leave-end="opacity-0 translate-x-1"
           class="absolute left-full top-0 z-50 w-[204px] pl-3">
        <div class="flex flex-col gap-2">
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