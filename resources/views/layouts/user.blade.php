<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') | Vonic</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="{{ asset('vendor/alpine.min.js') }}" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="flex min-h-screen flex-col bg-gray-50 font-sans text-black antialiased">

    <x-flash-message />

    <!-- ============================================================ -->
    <!-- NAVBAR USER (Komponen identik Admin, isi diganti nav pills)  -->
    <!-- ============================================================ -->
    <header id="user-navbar"
            x-data="{
                scrolled: false,
                userName: 'Sivitas',
                init() {
                    this.scrolled = (window.scrollY > 16);
                    window.addEventListener('scroll', () => { this.scrolled = (window.scrollY > 16) }, { passive: true });
                    // Baca nama user dari localStorage
                    try {
                        const u = JSON.parse(localStorage.getItem('vonic_user') || '{}');
                        if (u.nama) this.userName = u.nama;
                    } catch(e) {}
                    // Guard dinonaktifkan sementara untuk preview tampilan
                    // if (!localStorage.getItem('vonic_token')) {
                    //     window.location.href = '{{ route('login') }}';
                    // }
                }
            }"
            :class="scrolled
              ? 'fixed inset-x-0 top-0 z-30 flex h-[72px] items-center justify-between bg-white/95 backdrop-blur-md px-4 sm:px-8 shadow-nav border-b border-gray-100 transition-all duration-300'
              : 'fixed left-3 right-3 sm:left-4 sm:right-4 top-3 sm:top-4 z-30 flex h-[72px] sm:h-[80px] items-center justify-between rounded-2xl bg-white px-4 sm:px-6 shadow-nav transition-all duration-300'">

      <!-- Kiri: Logo Vonic + Nav Pills -->
      <div class="flex items-center gap-3 sm:gap-6">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3">
          <img src="{{ asset('images/logo-vonic-b.webp') }}" alt="Vonic" class="h-7 sm:h-8 w-auto" onerror="this.style.display='none'">
          <span class="font-sans text-xl sm:text-2xl font-bold tracking-tight text-primary-1">Vonic</span>
        </a>

        <!-- Nav Pills Desktop: Beranda & Riwayat Pemilihan -->
        <nav class="hidden md:flex items-center gap-2.5">
          <a href="{{ route('home') }}"
             class="btn-secondary h-11 px-4 text-xs font-semibold {{ request()->routeIs('home') ? '!border-primary-1 !text-primary-1 !bg-primary-3/20' : '' }}">
            <x-solar-home-2-linear class="h-4 w-4" />
            <span>Beranda</span>
          </a>

          <a href="{{ route('riwayat') }}"
             class="btn-secondary h-11 px-4 text-xs font-semibold {{ request()->routeIs('riwayat') ? '!border-primary-1 !text-primary-1 !bg-primary-3/20' : '' }}">
            <x-solar-folder-with-files-linear class="h-4 w-4" />
            <span>Riwayat Pemilihan</span>
          </a>
        </nav>

        <!-- Nav Compact Mobile (< md): Icon-only pills -->
        <nav class="flex md:hidden items-center gap-1.5">
          <a href="{{ route('home') }}" aria-label="Beranda"
             class="btn-secondary h-10 w-10 px-0 {{ request()->routeIs('home') ? '!border-primary-1 !text-primary-1 !bg-primary-3/20' : '' }}">
            <x-solar-home-2-linear class="h-4 w-4" />
          </a>
          <a href="{{ route('riwayat') }}" aria-label="Riwayat Pemilihan"
             class="btn-secondary h-10 w-10 px-0 {{ request()->routeIs('riwayat') ? '!border-primary-1 !text-primary-1 !bg-primary-3/20' : '' }}">
            <x-solar-folder-with-files-linear class="h-4 w-4" />
          </a>
        </nav>
      </div>

      <!-- Kanan: Notifikasi & Dropdown Profil (Identik Admin) -->
      <div class="flex items-center gap-3">

        <!-- Tombol Notifikasi Bell -->
        <div x-data="{
               openNotif: false,
               loadingNotif: false,
               notifications: [],
               unreadCount: 0,
               init() {
                 this.fetchNotifications();
               },
               async fetchNotifications() {
                 const token = localStorage.getItem('vonic_token');
                 if (!token) return;
                 this.loadingNotif = true;
                 try {
                   const res = await fetch('/api/notifikasi?limit=5', {
                     headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
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
                   const token = localStorage.getItem('vonic_token');
                   if (token) {
                     fetch('/api/notifikasi/' + notif.id + '/read', {
                       method: 'POST',
                       headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                     }).catch(() => {});
                   }
                 }
                 if (notif.link) {
                   window.location.href = notif.link;
                 }
               },
               async markAllAsRead() {
                 this.notifications.forEach(n => n.read = true);
                 this.unreadCount = 0;
                 const token = localStorage.getItem('vonic_token');
                 if (token) {
                   fetch('/api/notifikasi/read-all', {
                     method: 'POST',
                     headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                   }).catch(() => {});
                 }
               }
             }"
             @click.outside="openNotif = false"
             @keydown.escape.window="openNotif = false"
             class="relative">
          
          <!-- Trigger Button Bell (Dot biru polos tanpa animasi) -->
          <button type="button"
                  @click="openNotif = !openNotif; if (openNotif) fetchNotifications();"
                  :aria-expanded="openNotif"
                  aria-label="Notifikasi"
                  class="btn-secondary relative flex h-11 w-11 items-center justify-center">
            <x-solar-bell-linear class="h-5 w-5" />
            <span x-show="unreadCount > 0" x-cloak
                  class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-primary-1"></span>
          </button>

          <!-- Dropdown Menu Notifikasi (Clean & Simplified) -->
          <div x-show="openNotif" x-cloak
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
               x-transition:enter-end="opacity-100 translate-y-0 scale-100"
               x-transition:leave="transition ease-in duration-100"
               x-transition:leave-start="opacity-100 translate-y-0 scale-100"
               x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
               class="absolute right-0 top-full z-50 mt-2 w-[calc(100vw-2rem)] sm:w-88 rounded-2xl border border-grey-1 bg-white p-4 shadow-xl">

            <!-- Header Notifikasi -->
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
              <a href="{{ route('notifikasi') }}"
                 class="inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-primary-1 hover:underline">
                <x-solar-arrow-right-linear class="h-3.5 w-3.5" />
                <span>Lihat semua notifikasi</span>
              </a>
            </div>

          </div>
        </div>

        <!-- Dropdown Profil (Identik Admin — hover trigger) -->
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
               class="absolute right-0 top-full z-40 pt-2 min-w-[160px]">

            <div class="flex flex-col gap-1 rounded-2xl border border-grey-1 bg-white p-2 shadow-lg">
              <a href="{{ route('profile') }}"
                 class="flex h-10 items-center rounded-xl px-3 text-xs font-semibold text-gray-700 transition-colors hover:bg-primary-3/40 hover:text-primary-1">
                <x-solar-user-linear class="mr-2 h-4 w-4" />
                <span>Profile</span>
              </a>

              <form method="POST" action="{{ route('logout') }}" x-ref="logoutForm">
                @csrf
                <button type="submit"
                        @click.prevent="
                            const token = localStorage.getItem('vonic_token');
                            const doLogout = () => {
                                localStorage.removeItem('vonic_token');
                                localStorage.removeItem('vonic_user');
                                $refs.logoutForm.submit();
                            };
                            if (token) {
                                fetch('/api/logout', {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'Authorization': 'Bearer ' + token
                                    }
                                }).finally(doLogout);
                            } else {
                                doLogout();
                            }
                        "
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

    <!-- Konten Utama -->
    <main class="mx-auto w-full flex-1 max-w-[1392px] px-4 sm:px-6 pb-16 pt-[104px] sm:pt-[112px]">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

</body>

</html>
