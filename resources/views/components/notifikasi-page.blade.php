@props([
  'backUrl' => '/',
])

<div x-data="{
       loading: true,
       filter: 'semua',
       page: 1,
       perPage: 15,
       notifications: [],
       unreadCount: 0,
       init() {
         this.fetchAll();
       },
       async fetchAll() {
         const token = localStorage.getItem('vonic_token');
         if (!token) {
           this.loading = false;
           return;
         }
         this.loading = true;
         try {
           const res = await fetch('/api/notifikasi', {
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
           this.loading = false;
         }
       },
       get filtered() {
         if (this.filter === 'unread') {
           return this.notifications.filter(n => !n.read);
         }
         return this.notifications;
       },
       get totalPages() {
         return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
       },
       get paginated() {
         const start = (this.page - 1) * this.perPage;
         return this.filtered.slice(start, start + this.perPage);
       },
       setFilter(val) {
         this.filter = val;
         this.page = 1;
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
     class="space-y-5">

  {{-- Top Bar: Kembali + Judul + Aksi --}}
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ $backUrl }}" class="btn-secondary h-10 px-4 text-xs font-semibold">
        <x-solar-alt-arrow-left-linear class="h-4 w-4" />
        <span>Kembali</span>
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-black">Semua Notifikasi</h1>
        <p class="text-xs text-grey-deep mt-0.5"
           x-text="unreadCount > 0 ? unreadCount + ' notifikasi belum dibaca' : 'Semua notifikasi sudah dibaca'"></p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <button type="button"
              @click="markAllAsRead()"
              :disabled="unreadCount === 0"
              class="btn-secondary h-10 px-4 text-xs font-semibold disabled:opacity-40">
        <x-solar-check-circle-linear class="h-4 w-4" />
        <span>Tandai semua dibaca</span>
      </button>
    </div>
  </div>

  {{-- Filter Pills --}}
  <div class="flex items-center gap-2">
    <button type="button"
            @click="setFilter('semua')"
            :class="filter === 'semua' ? 'bg-primary-1 text-white' : 'bg-white text-grey-deep border border-grey-1 hover:border-primary-1'"
            class="rounded-full px-4 py-1.5 text-xs font-semibold transition-colors cursor-pointer">
      Semua
    </button>
    <button type="button"
            @click="setFilter('unread')"
            :class="filter === 'unread' ? 'bg-primary-1 text-white' : 'bg-white text-grey-deep border border-grey-1 hover:border-primary-1'"
            class="rounded-full px-4 py-1.5 text-xs font-semibold transition-colors cursor-pointer">
      Belum Dibaca
    </button>
  </div>

  {{-- Daftar Notifikasi --}}
  <x-card variant="panel">
    {{-- Loading State --}}
    <div x-show="loading" class="py-12 text-center text-xs text-grey-2">
      Memuat daftar notifikasi...
    </div>

    {{-- Empty State --}}
    <div x-show="!loading && filtered.length === 0" x-cloak class="py-12 text-center">
      <x-solar-bell-linear class="mx-auto h-10 w-10 text-grey-2 mb-3 opacity-60" />
      <p class="text-sm font-bold text-black">Belum ada notifikasi</p>
      <p class="text-xs text-grey-deep mt-1"
         x-text="filter === 'unread' ? 'Tidak ada notifikasi yang belum dibaca.' : 'Notifikasi aktivitas terbaru akan muncul di sini.'"></p>
    </div>

    {{-- List --}}
    <div x-show="!loading && filtered.length > 0" x-cloak class="divide-y divide-gray-100">
      <template x-for="notif in paginated" :key="notif.id">
        <a :href="notif.link || '#'"
           @click.prevent="markAsRead(notif)"
           class="flex items-start gap-3 p-4 rounded-xl transition-colors hover:bg-gray-50"
           :class="notif.read ? 'opacity-75' : 'bg-primary-3/20'">

          {{-- Dot biru polos tanpa animasi untuk status belum dibaca --}}
          <div class="pt-1.5 w-2.5 shrink-0 flex justify-center">
            <span x-show="!notif.read" class="h-2 w-2 rounded-full bg-primary-1"></span>
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <p class="text-sm font-bold text-black" x-text="notif.title"></p>
              <span class="text-[11px] text-grey-2 shrink-0" x-text="notif.time || notif.created_at || ''"></span>
            </div>
            <p class="text-xs text-grey-deep leading-relaxed mt-1" x-text="notif.message"></p>
          </div>
        </a>
      </template>
    </div>

    {{-- Pagination --}}
    <div x-show="!loading && totalPages > 1" x-cloak
         class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
      <span class="text-xs text-grey-deep"
            x-text="'Halaman ' + page + ' dari ' + totalPages"></span>
      <div class="flex items-center gap-2">
        <button type="button"
                @click="if (page > 1) page--"
                :disabled="page <= 1"
                class="btn-secondary h-9 px-3 text-xs font-semibold disabled:opacity-40">
          <x-solar-alt-arrow-left-linear class="h-4 w-4" />
          <span>Sebelumnya</span>
        </button>
        <button type="button"
                @click="if (page < totalPages) page++"
                :disabled="page >= totalPages"
                class="btn-secondary h-9 px-3 text-xs font-semibold disabled:opacity-40">
          <x-solar-alt-arrow-right-linear class="h-4 w-4" />
          <span>Berikutnya</span>
        </button>
      </div>
    </div>
  </x-card>
</div>
