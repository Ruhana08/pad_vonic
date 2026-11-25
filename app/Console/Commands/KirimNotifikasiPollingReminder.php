<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\PeriodePolling;
use App\Models\User;
use Illuminate\Console\Command;

class KirimNotifikasiPollingReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Jalankan manual: php artisan notif:polling-reminder
     * Dijadwalkan otomatis: setiap hari pukul 07.00 (via scheduler)
     */
    protected $signature = 'notif:polling-reminder';

    protected $description = 'Kirim notifikasi otomatis: H-1 sebelum polling berakhir dan polling yang baru selesai';

    public function handle(): int
    {
        $besok   = now()->addDay()->toDateString();
        $hariIni = now()->toDateString();

        // ── 1. H-1 sebelum polling berakhir ──────────────────────────────────
        $pollingBesokSelesai = PeriodePolling::where('status', 'aktif')
            ->where('tanggal_selesai', $besok)
            ->get();

        foreach ($pollingBesokSelesai as $periode) {
            $users = User::where('role', 'user')->get();
            foreach ($users as $user) {
                Notifikasi::kirim(
                    $user->id_user,
                    'Polling Segera Berakhir!',
                    'Polling "' . $periode->nama_periode . '" akan berakhir besok. Pastikan kamu sudah memberikan suaramu!',
                    '/voting/' . $periode->id_periode
                );
            }
            $this->info("[H-1] Notifikasi dikirim untuk polling: {$periode->nama_periode}");
        }

        // ── 2. Polling yang selesai hari ini → ubah status & notif hasil ─────
        $pollingSelesaiHariIni = PeriodePolling::where('status', 'aktif')
            ->where('tanggal_selesai', '<', $hariIni)
            ->get();

        foreach ($pollingSelesaiHariIni as $periode) {
            // Ubah status menjadi selesai otomatis
            $periode->update(['status' => 'selesai']);

            // Notif ke semua user: lihat hasil
            $users = User::where('role', 'user')->get();
            foreach ($users as $user) {
                Notifikasi::kirim(
                    $user->id_user,
                    'Hasil Polling Tersedia',
                    'Polling "' . $periode->nama_periode . '" telah selesai. Lihat rekapitulasi hasilnya sekarang!',
                    '/hasil-polling/' . $periode->id_periode
                );
            }

            // Notif ke admin: polling berakhir
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notifikasi::kirim(
                    $admin->id_user,
                    'Polling Berakhir',
                    'Polling "' . $periode->nama_periode . '" telah selesai secara otomatis. Silakan lihat rekapitulasinya.',
                    '/admin/polling/' . $periode->id_periode . '/hasil'
                );
            }

            $this->info("[Selesai] Status polling diubah & notifikasi dikirim: {$periode->nama_periode}");
        }

        $this->info('Selesai. Total polling H-1: ' . $pollingBesokSelesai->count() . ', Total polling baru selesai: ' . $pollingSelesaiHariIni->count());

        return self::SUCCESS;
    }
}
