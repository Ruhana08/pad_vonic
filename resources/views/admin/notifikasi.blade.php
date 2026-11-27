@extends('layouts.admin')

@section('title', 'Notifikasi Admin - Vonic')

@section('content')
<div class="space-y-5"
     x-data="{
       loading: true,
       filter: 'semua',
       page: 1,
       perPage: 10,
       notifications: [],
       unreadCount: 0,
       init() {
         this.fetchAll();
       },
       async fetchAll() {
         this.loading = true;
         try {
           const res = await fetch('{{ route('admin.notifikasi.data') }}', {
             headers: { 'Accept': 'application/json' },
             credentials: 'same-origin'
           });
           if (res.ok) {
             const json = await res.json();
             if (json?.success) {
               const raw = json.data;
               this.notifications = Array.isArray(raw) ? raw : (Array.isArray(raw?.data) ? raw.data : []);
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
       async markAsRead(notif, redirect = false) {
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
           window.dispatchEvent(new CustomEvent('vonic-admin-notif-updated'));
         }
         if (redirect && notif.link) {
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
         window.dispatchEvent(new CustomEvent('vonic-admin-notif-updated'));
       }
     }">

  {{-- Top Bar --}}
  <div class="space-y-3">
    <div>
      <a href="{{ route('admin.dashboard') }}" class="btn-secondary inline-flex h-10 px-4 text-xs font-semibold">
        <x-solar-alt-arrow-left-linear class="h-4 w-4" />
        <span>Kembali</span>
      </a>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-black">Notifikasi Admin</h1>
        <p class="text-xs text-grey-deep mt-0.5"
           x-text="unreadCount > 0 ? unreadCount + ' notifikasi admin belum dibaca' : 'Semua notifikasi admin sudah dibaca'"></p>
      </div>

      <div class="flex items-center gap-2">
        <button type="button"
                @click="markAllAsRead()"
                :disabled="unreadCount === 0"
                class="btn-secondary h-10 px-4 text-xs font-semibold disabled:opacity-40 cursor-pointer">
          <x-solar-check-circle-linear class="h-4 w-4" />
          <span>Tandai semua dibaca</span>
        </button>
      </div>
    </div>
  </div>

  {{-- Filter Tabs --}}
  <div class="flex items-center gap-2">
    <button type="button"
            @click="setFilter('semua')"
            :class="filter === 'semua' ? 'bg-primary-1 text-white' : 'bg-white text-grey-deep border border-grey-1 hover:border-primary-1'"
            class="rounded-full px-4 py-1.5 text-xs font-semibold transition-colors cursor-pointer">
      Semua (<span x-text="notifications.length"></span>)
    </button>
    <button type="button"
            @click="setFilter('unread')"
            :class="filter === 'unread' ? 'bg-primary-1 text-white' : 'bg-white text-grey-deep border border-grey-1 hover:border-primary-1'"
            class="rounded-full px-4 py-1.5 text-xs font-semibold transition-colors cursor-pointer">
      Belum Dibaca (<span x-text="unreadCount"></span>)
    </button>
  </div>

  {{-- Tabel Sistem Notifikasi Admin --}}
  <x-card class="p-6 shadow-sm">
    <div x-show="loading" class="py-12 text-center text-xs text-grey-2">
      Memuat tabel notifikasi admin...
    </div>

    <div x-show="!loading && filtered.length === 0" x-cloak class="py-12 text-center">
      <x-solar-bell-linear class="mx-auto h-10 w-10 text-grey-2 mb-3 opacity-60" />
      <p class="text-sm font-bold text-black">Belum ada notifikasi admin</p>
      <p class="text-xs text-grey-deep mt-1"
         x-text="filter === 'unread' ? 'Tidak ada notifikasi admin yang belum dibaca.' : 'Notifikasi aktivitas voting dan sistem admin akan tampil di sini.'"></p>
    </div>

    <div x-show="!loading && filtered.length > 0" x-cloak class="overflow-x-auto">
      <table class="w-full min-w-[760px] border-separate border-spacing-y-2 text-xs">
        <thead>
          <tr class="bg-secondary-1 text-white text-xs font-bold tracking-wider text-center">
            <th class="rounded-l-sm py-3 px-3 w-12">NO.</th>
            <th class="py-3 px-4 text-left min-w-[180px]">JUDUL</th>
            <th class="py-3 px-4 text-left min-w-[260px]">PESAN</th>
            <th class="py-3 px-3 whitespace-nowrap">WAKTU</th>
            <th class="py-3 px-3 whitespace-nowrap">STATUS</th>
            <th class="rounded-r-sm py-3 px-3 whitespace-nowrap">AKSI</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="(notif, idx) in paginated" :key="notif.id">
            <tr class="transition-colors duration-150 hover:bg-[#F3F7FF]"
                :class="notif.read ? 'bg-white opacity-80' : 'bg-primary-3/20'">
              <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs"
                      x-text="String((page - 1) * perPage + idx + 1).padStart(2, '0')"></span>
              </td>
              <td class="border-b border-gray-100 py-3.5 px-4 font-bold text-black">
                <div class="flex items-center gap-2">
                  <span x-show="!notif.read" class="h-2 w-2 shrink-0 rounded-full bg-primary-1"></span>
                  <span x-text="notif.title"></span>
                </div>
              </td>
              <td class="border-b border-gray-100 py-3.5 px-4 text-grey-deep leading-relaxed" x-text="notif.message"></td>
              <td class="border-b border-gray-100 py-3.5 px-3 text-center whitespace-nowrap text-grey-2"
                  x-text="notif.created_at || notif.time || '—'"></td>
              <td class="border-b border-gray-100 py-3.5 px-3 text-center whitespace-nowrap">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold"
                      :class="notif.read ? 'bg-gray-100 text-grey-deep' : 'bg-primary-3 text-primary-1'"
                      x-text="notif.read ? 'Dibaca' : 'Belum Dibaca'"></span>
              </td>
              <td class="border-b border-gray-100 py-3.5 px-3 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button"
                          x-show="!notif.read"
                          @click="markAsRead(notif, false)"
                          class="rounded-lg border border-grey-1 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-grey-deep hover:border-primary-1 hover:text-primary-1 transition-colors cursor-pointer">
                    Tandai Dibaca
                  </button>
                  <template x-if="notif.link">
                    <button type="button"
                            @click="markAsRead(notif, true)"
                            class="rounded-lg bg-primary-1 px-2.5 py-1.5 text-[11px] font-semibold text-white hover:bg-primary-4 transition-colors cursor-pointer">
                      Buka
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
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
                class="btn-secondary h-9 px-3 text-xs font-semibold disabled:opacity-40 cursor-pointer">
          <x-solar-alt-arrow-left-linear class="h-4 w-4" />
          <span>Sebelumnya</span>
        </button>
        <button type="button"
                @click="if (page < totalPages) page++"
                :disabled="page >= totalPages"
                class="btn-secondary h-9 px-3 text-xs font-semibold disabled:opacity-40 cursor-pointer">
          <x-solar-alt-arrow-right-linear class="h-4 w-4" />
          <span>Berikutnya</span>
        </button>
      </div>
    </div>
  </x-card>
</div>
@endsection
