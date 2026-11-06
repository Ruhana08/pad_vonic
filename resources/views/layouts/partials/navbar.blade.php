<header id="admin-navbar" data-turbo-permanent
        class="fixed inset-x-0 top-0 z-30 flex h-[72px] items-center justify-between bg-white px-8">

  <!-- Kiri: logo dan judul -->
  <div class="flex items-center gap-6">
    <div class="flex items-center gap-3">
      <img src="{{ asset('images/logo-ugm.webp') }}" alt="UGM" class="h-9 w-auto" onerror="this.style.display='none'">
      <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-8 w-auto" onerror="this.style.display='none'">
    </div>
    <span class="font-skranji text-2xl font-bold text-primary-1">Admin Area</span>
  </div>

  <!-- Kanan: notifikasi dan profil -->
  <div class="flex items-center gap-3">

    <!-- Notifikasi -->
    <button type="button" aria-label="Notifikasi"
            class="flex h-11 w-11 items-center justify-center rounded-button border border-grey-1 bg-white text-primary-1 shadow-type-3 transition-colors hover:border-primary-1 hover:bg-primary-3">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
    </button>

    <!-- Dropdown profil -->
    <div x-data="{ open: false }"
         @click.outside="open = false"
         @keydown.escape.window="open = false"
         class="relative">

      <!-- Trigger -->
      <button type="button"
              @click="open = !open"
              :aria-expanded="open"
              :class="open ? 'border-primary-1' : 'border-grey-1 hover:border-primary-1'"
              class="flex h-11 items-center gap-2.5 rounded-button border bg-white pl-2 pr-4 shadow-type-3 transition-colors">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-1 text-white">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </span>
        <span class="whitespace-nowrap text-sm font-semibold text-black">
          {{ Auth::check() ? Auth::user()->name : 'Melisa Danuar, S.Kom' }}
        </span>
        <svg class="h-3.5 w-3.5 shrink-0 text-black transition-transform duration-200"
             :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>

      <!-- Menu: selebar trigger -->
      <div x-show="open" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0"
           x-transition:leave-end="opacity-0 -translate-y-1"
           class="absolute inset-x-0 top-full z-40 pt-2">

        <div class="flex flex-col gap-2">
          <a href="#"
             class="flex h-11 items-center rounded-button border border-primary-1 bg-white px-4 text-sm font-medium text-primary-1 transition-colors hover:bg-primary-3">
            Profile
          </a>

          <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
            @csrf
            <button type="submit"
                    class="flex h-11 w-full items-center rounded-button border border-danger bg-white px-4 text-sm font-medium text-danger transition-colors hover:bg-danger-soft/40">
              Log Out
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</header>