<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * GET /api/notifikasi
     *
     * Query params opsional:
     *   ?limit=5          → untuk dropdown navbar (5 notif terbaru)
     *   ?filter=unread    → hanya yang belum dibaca
     *   ?page=1           → paginasi (halaman lihat semua)
     */
    public function index(Request $request)
    {
        $query = Notifikasi::where('id_user', $request->user()->id_user)
            ->orderBy('created_at', 'desc');

        // Filter hanya yang belum dibaca
        if ($request->query('filter') === 'unread') {
            $query->where('is_read', false);
        }

        // Limit untuk dropdown navbar (tanpa paginasi)
        if ($request->has('limit')) {
            $limit = (int) $request->query('limit', 5);
            $notifikasi = $query->limit($limit)->get();

            return response()->json([
                'success'      => true,
                'unread_count' => Notifikasi::where('id_user', $request->user()->id_user)
                    ->where('is_read', false)
                    ->count(),
                'data'         => $notifikasi->map(fn($n) => $this->format($n)),
            ]);
        }

        // Paginasi untuk halaman lihat semua
        $perPage = (int) $request->query('per_page', 15);
        $notifikasi = $query->paginate($perPage);

        return response()->json([
            'success'      => true,
            'unread_count' => Notifikasi::where('id_user', $request->user()->id_user)
                ->where('is_read', false)
                ->count(),
            'data'         => $notifikasi->through(fn($n) => $this->format($n)),
        ]);
    }

    /**
     * POST /api/notifikasi/{id}/read
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead(Request $request, $id)
    {
        $notif = Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', $request->user()->id_user)
            ->firstOrFail();

        $notif->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    /**
     * POST /api/notifikasi/read-all
     * Menandai semua notifikasi user sebagai sudah dibaca.
     */
    public function markReadAll(Request $request)
    {
        Notifikasi::where('id_user', $request->user()->id_user)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }

    /**
     * Format satu notifikasi untuk response FE.
     */
    private function format(Notifikasi $n): array
    {
        return [
            'id'         => $n->id_notifikasi,
            'title'      => $n->judul,
            'message'    => $n->pesan,
            'link'       => $n->link,
            'read'       => $n->is_read,
            'time'       => $n->created_at->diffForHumans(),
            'created_at' => $n->created_at->toDateTimeString(),
        ];
    }
}
