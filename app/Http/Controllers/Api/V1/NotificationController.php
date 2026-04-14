<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    /**
     * List the authenticated user's notifications (paginated, newest first).
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return $this->successResponse('Notifications retrieved.', $notifications);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return $this->successResponse('Notification marked as read.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return $this->successResponse('All notifications marked as read.');
    }

    /**
     * Return the count of unread notifications.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return $this->successResponse('Unread count retrieved.', [
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
