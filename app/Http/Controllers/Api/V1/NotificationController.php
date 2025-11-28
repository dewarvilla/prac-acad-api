<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $perPage    = (int) $request->query('per_page', 10);
        $onlyUnread = filter_var($request->query('unread', 'true'), FILTER_VALIDATE_BOOLEAN);

        $q = $onlyUnread
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $q->orderByDesc('created_at')->paginate($perPage);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, string $notificationId)
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

}
