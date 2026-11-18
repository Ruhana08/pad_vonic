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

    <!-- Dropdown Notifikasi -->
    <div x-data="{
           openNotif: false,
           hasUnread: true,
           filter: 'semua', // 'semua' | 'voting' | 'sistem'
           notifications: [
             {
               id: 1,
               type: 'voting',
               icon: 'solar-check-circle-bold',
               iconColor: 'text-success',
               iconBg: 'bg-success-soft',
               title: 'Suara Baru Diterima',
               message: 'Dr. Budi Santoso, M.Kom telah mengirimkan suara untuk Piknik DTEDI 2026.',
               time: '5 menit yang lalu',
               read: false,
               link: '{{ route('admin.polling.aktif') }}#aktivitas-terbaru'
             },
             {
               id: 2,
               type: 'voting',
               icon: 'solar-chart-2-bold',
               iconColor: 'text-primary-1',
               iconBg: 'bg-primary-3',
               title: 'Partisipasi Capai 80%',
               message: 'Pemungutan suara Piknik DTEDI telah mencapai 279 pemilih.',
               time: '1 jam yang lalu',
               read: false,
               link: '{{ route('admin.polling.aktif') }}'
             },
             {
               id: 3,
               type: 'sistem',
               icon: 'solar-clock-circle-bold',
               iconColor: 'text-warning',
               iconBg: 'bg-amber-100',
               title: 'Polling Segera Berakhir',
               message: 'Voting tersisa 3 hari lagi. Ditutup Jumat, 16 Okt 2026 pukul 18.00 WIB.',
               time: '2 jam yang lalu',
               read: true,
               link: '{{ route('admin.polling.aktif') }}'
             },
             {
               id: 4,
               type: 'sistem',
               icon: 'solar-user-check-bold',
               iconColor: 'text-primary-1',
               iconBg: 'bg-primary-3',
               title: 'Login Admin Terdeteksi',
               message: 'Akun Anda berhasil masuk dari Chrome (Windows) pada 08:30 WIB.',
               time: 'Hari ini, 08:30',
               read: true,
               link: '{{ route('admin.profil') }}'
             },
             {
               id: 5,
               type: 'sistem',
               icon: 'solar-file-download-bold',
               iconColor: 'text-secondary-1',
               iconBg: 'bg-blue-100',
               title: 'Import CSV Selesai',
               message: 'Data 60 sivitas akademika berhasil diperbarui ke dalam sistem.',
               time: 'Kemarin, 14:20',
               read: true,
               link: '{{ route('admin.akun.index') }}'
             }
           ],
           get filteredNotifications() {
             if (this.filter === 'semua') return this.notifications;
             return this.notifications.filter(n => n.type === this.filter);
           },
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

      <!-- Tombol Trigger Notifikasi (Radius & shadow identik tombol profil) -->
      <button type="button"
              @click="openNotif = !openNotif"
              :aria-expanded="openNotif"
              aria-label="Notifikasi"
              class="flex h-12 w-12 items-center justify-center rounded-sm bg-white text-primary-1 shadow-control transition-all hover:bg-primary-3/30 cursor-pointer">
        <x-solar-bell-linear class="h-5 w-5" />
      </button>

      <!-- Dropdown Menu Notifikasi -->
      <div x-show="openNotif" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 scale-100"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0 scale-100"
           x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
           class="absolute right-0 top-full z-50 mt-2 w-80 sm:w-96 rounded-xl border border-primary-1/20 bg-white p-3 shadow-2xl">

        <!-- Header Panel Notifikasi -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
          <span class="text-sm font-bold text-black">Notifikasi</span>
          <button type="button"
                  @click="markAllAsRead()"
                  class="text-[11px] font-semibold text-primary-1 hover:underline cursor-pointer">
            Tandai semua dibaca
          </button>
        </div>

        <!-- Filter Tab Notifikasi: Semua | Voting | Sistem -->
        <div class="flex items-center gap-1.5 py-2 border-b border-gray-100">
          <button type="button"
                  @click="filter = 'semua'"
                  :class="filter === 'semua' ? 'bg-primary-1 text-white' : 'bg-gray-100 text-grey-deep hover:bg-gray-200'"
                  class="rounded-full px-3 py-1 text-[11px] font-semibold transition-colors cursor-pointer">
            Semua
          </button>
          <button type="button"
                  @click="filter = 'voting'"
                  :class="filter === 'voting' ? 'bg-primary-1 text-white' : 'bg-gray-100 text-grey-deep hover:bg-gray-200'"
                  class="rounded-full px-3 py-1 text-[11px] font-semibold transition-colors cursor-pointer">
            🗳️ Voting
          </button>
          <button type="button"
                  @click="filter = 'sistem'"
                  :class="filter === 'sistem' ? 'bg-primary-1 text-white' : 'bg-gray-100 text-grey-deep hover:bg-gray-200'"
                  class="rounded-full px-3 py-1 text-[11px] font-semibold transition-colors cursor-pointer">
            ⚙️ Sistem
          </button>
        </div>

        <!-- Daftar Notifikasi -->
        <div class="mt-2 max-h-80 overflow-y-auto space-y-1 divide-y divide-gray-50">
          <template x-for="notif in filteredNotifications" :key="notif.id">
            <a :href="notif.link"
               @click="notif.read = true"
               class="flex items-start gap-3 p-2.5 rounded-lg transition-colors hover:bg-primary-3/30"
               :class="notif.read ? 'bg-white' : 'bg-primary-3/20'">
              
              <!-- Icon Notifikasi -->
              <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full mt-0.5"
                   :class="notif.iconBg">
                <template x-if="notif.type === 'voting'">
                  <x-solar-cup-star-bold class="h-4 w-4 text-primary-1" />
                </template>
                <template x-if="notif.type === 'sistem' && notif.icon.includes('clock')">
                  <x-solar-clock-circle-bold class="h-4 w-4 text-warning" />
                </template>
                <template x-if="notif.type === 'sistem' && notif.icon.includes('user')">
                  <x-solar-user-check-bold class="h-4 w-4 text-primary-1" />
                </template>
                <template x-if="notif.type === 'sistem' && notif.icon.includes('download')">
                  <x-solar-file-download-bold class="h-4 w-4 text-secondary-1" />
                </template>
              </div>

              <!-- Konten Teks -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1">
                  <p class="text-xs font-bold text-black truncate" x-text="notif.title"></p>
                  <span class="text-[10px] text-grey-2 shrink-0" x-text="notif.time"></span>
                </div>
                <p class="text-[11px] text-grey-deep leading-relaxed mt-0.5 line-clamp-2" x-text="notif.message"></p>
              </div>

              <!-- Dot status belum dibaca -->
              <template x-if="!notif.read">
                <span class="h-2 w-2 rounded-full bg-primary-1 shrink-0 mt-1.5"></span>
              </template>
            </a>
          </template>

          <!-- Tampilan Jika Kosong -->
          <template x-if="filteredNotifications.length === 0">
            <div class="py-8 text-center text-xs text-grey-deep">
              <x-solar-bell-linear class="mx-auto h-8 w-8 text-grey-2 mb-2 opacity-50" />
              Tidak ada notifikasi dalam kategori ini.
            </div>
          </template>
        </div>

        <!-- Footer Notifikasi -->
        <div class="mt-3 pt-2.5 border-t border-gray-100 text-center">
          <a href="{{ route('admin.polling.aktif') }}#aktivitas-terbaru"
             class="text-xs font-semibold text-primary-1 hover:underline">
            Lihat Semua Aktivitas Voting &rarr;
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
