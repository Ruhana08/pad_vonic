@php
  $navUser = Auth::user();
  $navName = $navUser?->nama ?? 'Admin';
  $navFoto = $navUser?->foto_url ?? '';
@endphp
<header id="admin-navbar"
        x-data="{
            scrolled: false,
            userName: @js($navName),
            userPhoto: @js($navFoto),
            photoErr: false,
            getInitials(name) {
                const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
                if (!parts.length) return 'A';
                if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
                return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
            },
            init() {
                this.scrolled = (window.scrollY > 16);
                window.addEventListener('scroll', () => { this.scrolled = (window.scrollY > 16) }, { passive: true });
                window.addEventListener('vonic-profile-updated', (e) => {
                    if (e.detail?.nama) this.userName = e.detail.nama;
                    if (e.detail?.foto !== undefined) {
                        this.userPhoto = e.detail.foto || '';
                        this.photoErr = false;
                    }
                });
            }
        }"
        :class="scrolled
          ? 'fixed inset-x-0 top-0 z-30 flex h-[72px] items-center justify-between bg-white/95 backdrop-blur-md px-4 sm:px-8 shadow-nav border-b border-gray-100 transition-all duration-300'
          : 'fixed left-3 right-3 sm:left-4 sm:right-4 top-3 sm:top-4 z-30 flex h-[72px] sm:h-[80px] items-center justify-between rounded-2xl bg-white px-4 sm:px-6 shadow-nav transition-all duration-300'">

  <!-- Kiri: Hamburger Mobile + Logo UGM, Divider Biru, Logo Vonic, dan Judul Admin Area -->
  <div class="flex items-center gap-2.5 sm:gap-4">
    <button type="button"
            @click="sidebarOpen = !sidebarOpen"
            aria-label="Buka Menu"
            class="btn-secondary flex h-10 w-10 items-center justify-center lg:hidden">
      <x-solar-hamburger-menu-linear class="h-5 w-5" />
    </button>

    <div class="flex items-center gap-2.5 sm:gap-4">
      <img src="{{ asset('images/logo-ugm.webp') }}" alt="UGM" class="h-8 sm:h-10 w-auto" onerror="this.style.display='none'">
      <div class="h-7 sm:h-9 w-px bg-primary-1"></div>
      <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-6 sm:h-8 w-auto" onerror="this.style.display='none'">
    </div>

    <div class="hidden sm:flex flex-col font-sans text-lg font-bold leading-tight text-primary-1">
      <span>Admin</span>
      <span>Area</span>
    </div>
  </div>

  <!-- Kanan: Notifikasi dan Dropdown Profil (Identik User) -->
  <div class="flex items-center gap-3">

    <!-- Dropdown Notifikasi (Model disamakan dengan User + wired API) -->
    <div x-data="{
           openNotif: false,
           loadingNotif: false,
           notifications: [],
           unreadCount: 0,
           init() {
             this.fetchNotifications();
             window.addEventListener('vonic-admin-notif-updated', () => this.fetchNotifications());
           },
           async fetchNotifications() {
             this.loadingNotif = true;
             try {
               const res = await fetch('{{ route('admin.notifikasi.data') }}?limit=5', {
                 headers: { 'Accept': 'application/json' },
                 credentials: 'same-origin'
               });
               if (res.ok) {
                 const json = await res.json();
                 if (json?.success) {
                   this.notifications = Array.isArray(json.data) ? json.data : [];
                   this.unreadCount = Number(json.unread_count ?? this.notifications.filter(n => !n.read).length);
                 }
               }
             } catch (e) {
               this.notifications = [];
               this.unreadCount = 0;
             } finally {
               this.loadingNotif = false;
             }
           },
           async markAsRead(notif) {
             if (!notif.read) {
               notif.read = true;
               this.unreadCount = Math.max(0, this.unreadCount - 1);
               await fetch('/admin/notifikasi/' + notif.id + '/read', {
                 method: 'POST',
                 headers: {
                   'Accept': 'application/json',
                   'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 },
                 credentials: 'same-origin'
               }).catch(() => {});
             }
             if (notif.link) {
               window.location.href = notif.link;
             }
           },
           async markAllAsRead() {
             this.notifications.forEach(n => n.read = true);
             this.unreadCount = 0;
             await fetch('{{ route('admin.notifikasi.read-all') }}', {
               method: 'POST',
               headers: {
                 'Accept': 'application/json',
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
               },
               credentials: 'same-origin'
             }).catch(() => {});
           }
         }"
         @click.outside="openNotif = false"
         @keydown.escape.window="openNotif = false"
         class="relative">

      <!-- Tombol Trigger Notifikasi (Identik User) -->
      <button type="button"
              @click="openNotif = !openNotif; if (openNotif) fetchNotifications();"
              :aria-expanded="openNotif"
              aria-label="Notifikasi"
              class="btn-secondary relative flex h-11 w-11 items-center justify-center">
        <x-solar-bell-linear class="h-5 w-5" />
        <span x-show="unreadCount > 0" x-cloak
              class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-primary-1"></span>
      </button>

      <!-- Dropdown Menu Notifikasi (Clean & Simplified seperti User) -->
      <div x-show="openNotif" x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 scale-100"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0 scale-100"
           x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
           class="absolute right-0 top-full z-50 mt-2 w-[calc(100vw-2rem)] sm:w-88 rounded-2xl border border-grey-1 bg-white p-4 shadow-xl">

        <!-- Header Panel Notifikasi -->
        <div class="flex items-center justify-between pb-3 border-b border-grey-1">
          <span class="text-sm font-bold text-black">Notifikasi</span>
          <button type="button"
                  x-show="unreadCount > 0"
                  @click="markAllAsRead()"
                  class="text-[11px] font-medium text-grey-2 hover:text-primary-1 transition-colors cursor-pointer">
            Tandai dibaca
          </button>
        </div>

        <!-- Daftar Notifikasi Sederhana -->
        <div class="mt-2 max-h-80 overflow-y-auto divide-y divide-gray-100">
          <div x-show="loadingNotif && notifications.length === 0" x-cloak class="py-8 text-center text-xs text-grey-2">
            Memuat notifikasi...
          </div>

          <div x-show="!loadingNotif && notifications.length === 0" x-cloak class="py-8 text-center text-xs text-grey-deep">
            <x-solar-bell-linear class="mx-auto h-7 w-7 text-grey-2 mb-2 opacity-60" />
            Belum ada notifikasi saat ini.
          </div>

          <template x-for="notif in notifications" :key="notif.id">
            <a :href="notif.link || '#'"
               @click.prevent="markAsRead(notif)"
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
          <a href="{{ route('admin.notifikasi') }}"
             class="inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-primary-1 hover:underline">
            <x-solar-arrow-right-linear class="h-3.5 w-3.5" />
            <span>Lihat semua notifikasi</span>
          </a>
        </div>

      </div>
    </div>

    <!-- Dropdown Profil (Identik User — hover trigger) -->
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
        <template x-if="userPhoto && !photoErr">
          <img :src="userPhoto" :alt="userName" x-on:error="photoErr = true" class="h-6 w-6 shrink-0 rounded-full object-cover border border-primary-1/30" />
        </template>
        <template x-if="!userPhoto || photoErr">
          <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-1 text-[10px] font-bold text-white select-none" x-text="getInitials(userName)">
          </span>
        </template>
        <span class="hidden sm:inline whitespace-nowrap text-xs font-semibold text-black"
              x-text="userName">
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
           class="absolute right-0 top-full z-40 min-w-[160px] pt-2">

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
