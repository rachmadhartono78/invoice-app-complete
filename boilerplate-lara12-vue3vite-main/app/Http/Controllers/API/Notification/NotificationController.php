<?php

namespace App\Http\Controllers\API\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Ambil user yang sedang login
            $user = auth()->guard('api')->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $limit = (int) $request->get('limit', 3);  // Default limit is 3
            $offset = (int) $request->get('offset', 0);  // Default offset is 0

            // Query notifikasi berdasarkan user login
            $notificationsQuery = $user->notifications()
                ->orderByDesc('created_at'); // Urutkan berdasarkan waktu terbaru

            $totalNotifications = $notificationsQuery->count();

            // Filter berdasarkan status notifikasi
            if ($request->input('status') === 'unread') {
                // Hanya notifikasi yang belum dibaca
                $notificationsQuery = $notificationsQuery->whereNull('read_at');
            }

            $notificationsQwr = clone $notificationsQuery; // Clone query untuk menghitung total notifikasi

            // Ambil total jumlah notifikasi tanpa limit untuk pagination

            // Hitung jumlah total notifikasi yang belum dibaca
            $totalUnreadNotifications = $notificationsQwr->whereNull('read_at')->count();

            // Ambil notifikasi dengan pagination
            $notifications = $notificationsQuery
                ->skip($offset) // Terapkan offset untuk pagination
                ->take($limit) // Terapkan limit
                ->with('notifiable') // Eager load relasi 'notifiable' untuk mengambil data user yang terkait
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'message' => $notification->data['message'] ?? null,
                        'created_at' => optional($notification->created_at)->locale('id')->diffForHumans() ?? 'Tidak diketahui',
                        'read_at' => $notification->read_at,
                        'username' => optional($notification->notifiable)->name,
                        'title' => $notification->data['title'] ?? null,
                        'sender' => $notification->data['sender'] ?? null,
                        'avatar_sender' => $notification->data['avatar_sender'] ?? null,
                        'url_action' => $notification->data['url_action'] ?? null,
                        'parameter_action' => $notification->data['parameter_action'] ?? null,
                    ];
                });

            // Mengembalikan response dengan data notifikasi dan total count
            return response()->json([
                'data' => $notifications,
                'count' => $totalNotifications, // Jumlah total notifikasi
                'unread_count' => $totalUnreadNotifications, // Jumlah total notifikasi yang belum dibaca
            ], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        try {
            $user = auth()->guard('api')->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $notification = $user->notifications->find($id);

            if (! $notification) {
                return response()->json(['error' => 'Notification not found'], 404);
            }

            $notification->markAsRead();

            return response()->json(['message' => 'Notification marked as read'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function readAll(Request $request)
    {
        try {
            $user = auth()->guard('api')->user();

            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user->unreadNotifications->markAsRead();

            return response()->json(['message' => 'All notifications marked as read'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
