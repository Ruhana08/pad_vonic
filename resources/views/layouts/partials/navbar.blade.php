<header id="admin-navbar" data-turbo-permanent
        x-data="{ scrolled: false }"
        x-init="scrolled = (window.scrollY > 16);
                window.addEventListener('scroll', () => { scrolled = (window.scrollY > 16) }, { passive: true })"
        :class="scrolled
          ? 'fixed inset-x-0 top-0 z-30 flex h-[72px] items-center justify-between bg-white/95 backdrop-blur-md px-8 shadow-nav border-b border-gray-100 transition-all duration-300'
          : 'fixed left-4 right-4 top-4 z-30 flex h-[80px] items-center justify-between rounded-2xl bg-white px-6 shadow-nav transition-all duration-300'">

  <!-- Kiri: Logo UGM, Divider Biru, Logo Vonic, dan Judul Admin Area -->
  <div class="flex items-center gap-4">
    <div class="flex items-center gap-4">
      <img src="{{ asset('images/logo-ugm.webp') }}" alt="UGM" class="h-10 w-auto" onerror="this.style.display='none'">
      <div class="h-9 w-px bg-primary-1"></div>
      <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-8 w-auto" onerror="this.style.display='none'">
    </div>

    <div class="flex flex-col font-sans text-lg font-bold leading-tight text-primary-1">
      <span>Admin</span>
      <span>Area</span>
    </div>
  </div>

  <!-- Kanan: Notifikasi dan Dropdown Profil -->
  <div class="flex items-center gap-3">

    <!-- Tombol Notifikasi (Shadow & radius sesuai Figma) -->
    <button type="button" aria-label="Notifikasi"
            class="flex h-12 w-12 items-center justify-center rounded-sm bg-white text-primary-1 shadow-control transition-all hover:bg-primary-3">
      <x-solar-bell-linear class="h-5 w-5" />
    </button>

    <!-- Dropdown Profil (Aktif Saat Hover) -->
    <div x-data="{ open: false }"
         @mouseenter="open = true"
         @mouseleave="open = false"
         @keydown.escape.window="open = false"
         class="relative">

      <!-- Trigger Profil -->
      <button type="button"
              @click="open = !open"
              :aria-expanded="open"
              class="flex h-12 items-center gap-3 rounded-sm bg-white px-3.5 shadow-control transition-all hover:bg-primary-3/30">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-1 text-white">
          <x-solar-user-bold class="h-4 w-4" />
        </span>
        <span class="whitespace-nowrap text-sm font-medium tracking-wide text-black">
          {{ Auth::check() ? Auth::user()->name : 'Melisa D' }}
        </span>
        <x-solar-alt-arrow-down-linear
          class="h-4 w-4 shrink-0 text-gray-700 transition-transform duration-200"
          ::class="open ? 'rotate-180' : ''" />
      </button>

      <!-- Menu Dropdown -->
      <div x-show="open" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0"
           x-transition:leave-end="opacity-0 -translate-y-1"
           class="absolute inset-x-0 top-full z-40 pt-2">

        <div class="flex flex-col gap-2 rounded-xl border border-gray-100 bg-white p-2 shadow-lg">
          <a href="{{ route('admin.profil') }}"
             class="flex h-10 items-center rounded-lg px-3 text-sm font-medium text-gray-700 transition-colors hover:bg-primary-3 hover:text-primary-1">
            Profile
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex h-10 w-full items-center rounded-lg px-3 text-sm font-medium text-danger transition-colors hover:bg-rose-50 cursor-pointer">
              Log Out
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</header>
