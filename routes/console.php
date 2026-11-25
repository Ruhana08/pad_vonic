<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Kirim notifikasi H-1 sebelum polling berakhir + auto-close polling selesai
// Dijadwalkan setiap hari pukul 07.00 WIB
Schedule::command('notif:polling-reminder')->dailyAt('07:00');
