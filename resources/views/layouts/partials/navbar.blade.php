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

    <!-- Dropdown Notifikasi (Simplified, Bersih, Anti-Slop) -->
    <div x-data="{
           openNotif: false,
           hasUnread: true,
           notifications: [
             {
               id: 1,
               title: 'Suara Baru Diterima',
               message: 'Dr. Budi Santoso, M.Kom telah mengirimkan suara untuk Piknik DTEDI 2026.',
               time: '5 mnt lalu',
               read: false,
               link: '{{ route('admin.polling.aktif') }}#aktivitas-terbaru'
             },
             {
               id: 2,
               title: 'Partisipasi Capai 80%',
               message: 'Pemungutan suara Piknik DTEDI telah mencapai 279 pemilih.',
               time: '1 jam lalu',
               read: false,
               link: '{{ route('admin.polling.aktif') }}'
             },
             {
               id: 3,
               title: 'Polling Segera Berakhir',
               message: 'Voting tersisa 3 hari lagi. Ditutup Jumat, 16 Okt 2026 pukul 18.00 WIB.',
               time: '2 jam lalu',
               read: true,
               link: '{{ route('admin.polling.aktif') }}'
             }
           ],
           get unreadCount() {
             return this.notifications.filter(n => !n.read).length;
           },
           markAllAsRead() {
             this.notifications.forEach(n => n.read = true);
             this.hasUnread = false;
           }
         }"
         @click.outside="openNotif = false"
         @keydown.escape.window="openNotif = false"
         class="relative">

      <!-- Tombol Trigger Notifikasi (Tanpa dot merah) -->
      <button type="button"
              @click="openNotif = !openNotif"
              :aria-expanded="openNotif"
              aria-label="Notifikasi"
              class="btn-secondary relative flex h-11 w-11 items-center justify-center">
        <x-solar-bell-linear class="h-5 w-5" />
      </button>

      <!-- Dropdown Menu Notifikasi (Clean & Simplified, Anti-Slop) -->
      <div x-show="openNotif" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 scale-100"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0 scale-100"
           x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
           class="absolute right-0 top-full z-50 mt-2 w-80 sm:w-88 rounded-2xl border border-grey-1 bg-white p-4 shadow-xl">

        <!-- Header Notifikasi -->
        <div class="flex items-center justify-between pb-3 border-b border-grey-1">
          <span class="text-sm font-bold text-black">Notifikasi</span>
          <button type="button"
                  @click="markAllAsRead()"
                  class="text-[11px] font-medium text-grey-2 hover:text-primary-1 transition-colors cursor-pointer">
            Tandai dibaca
          </button>
        </div>

        <!-- Daftar Notifikasi Sederhana -->
        <div class="mt-2 divide-y divide-gray-100">
          <template x-for="notif in notifications" :key="notif.id">
            <a :href="notif.link"
               @click="notif.read = true"
               class="block py-2.5 px-3 rounded-xl transition-colors hover:bg-gray-50"
               :class="notif.read ? 'opacity-70' : 'bg-primary-3/20'">
              
              <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-bold text-black truncate" x-text="notif.title"></p>
                <span class="text-[10px] text-grey-2 shrink-0" x-text="notif.time"></span>
              </div>
              <p class="text-[11px] text-grey-deep leading-relaxed mt-1 line-clamp-2" x-text="notif.message"></p>
            </a>
          </template>
        </div>

        <!-- Footer Notifikasi -->
        <div class="mt-2 pt-2.5 border-t border-grey-1 text-center">
          <a href="{{ route('admin.polling.aktif') }}#aktivitas-terbaru"
             class="text-xs font-semibold text-primary-1 hover:underline">
            Lihat semua aktivitas &rarr;
          </a>
        </div>

      </div>
    </div>

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
              class="btn-secondary flex h-11 items-center gap-2.5 px-3">
        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-1 text-white">
          <x-solar-user-bold class="h-3.5 w-3.5" />
        </span>
        <span class="whitespace-nowrap text-xs font-semibold text-black">
          {{ Auth::check() ? Auth::user()->name : 'Melisa D' }}
        </span>
        <x-solar-alt-arrow-down-linear
          class="h-3.5 w-3.5 shrink-0 text-grey-dark transition-transform duration-200"
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

        <div class="flex flex-col gap-1 rounded-2xl border border-grey-1 bg-white p-2 shadow-lg">
          <a href="{{ route('admin.profil') }}"
             class="flex h-10 items-center rounded-xl px-3 text-xs font-semibold text-gray-700 transition-colors hover:bg-primary-3/40 hover:text-primary-1">
            <x-solar-user-linear class="mr-2 h-4 w-4" />
            <span>Profile</span>
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex h-10 w-full items-center rounded-xl px-3 text-xs font-semibold text-danger transition-colors hover:bg-rose-50 cursor-pointer">
              <x-solar-logout-2-linear class="mr-2 h-4 w-4" />
              <span>Log Out</span>
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</header>
