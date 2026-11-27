<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        return view('admin.notifikasi');
    }

    public function data(Request $request)
    {
        $userId = Auth::id();

        $query = Notifikasi::where('id_user', $userId)
            ->orderBy('created_at', 'desc');

        if ($request->query('filter') === 'unread') {
            $query->where('is_read', false);
        }

        if ($request->has('limit')) {
            $query->limit((int) $request->query('limit', 5));
        }

        $items = $query->get()->map(fn (Notifikasi $n) => [
            'id'         => $n->id_notifikasi,
            'title'      => $n->judul,
            'message'    => $n->pesan,
            'link'       => $n->link,
            'read'       => (bool) $n->is_read,
            'time'       => $n->created_at ? $n->created_at->diffForHumans() : '',
            'created_at' => $n->created_at ? $n->created_at->format('d M Y, H:i') : '',
        ]);

        $unreadCount = Notifikasi::where('id_user', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $unreadCount,
            'data'         => $items,
        ]);
    }

    public function markRead($id)
    {
        $notif = Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $notif->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    public function markReadAll()
    {
        Notifikasi::where('id_user', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }
}
