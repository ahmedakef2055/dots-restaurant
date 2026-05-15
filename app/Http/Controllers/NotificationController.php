<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function markAllRead(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('notifications')) {
            return back();
        }

        $user = $request->user();

        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return back()->with('success', __('messages.success.notifications_marked_as_read'));
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        if (! Schema::hasTable('notifications')) {
            return back();
        }

        $user = $request->user();

        if (! $user) {
            return back();
        }

        $record = $user->unreadNotifications()->whereKey($notification)->first();

        if (! $record) {
            return back();
        }

        $record->markAsRead();

        $targetUrl = trim((string) data_get($record->data, 'url'));

        if ($targetUrl !== '') {
            return redirect()->to($targetUrl);
        }

        return back();
    }
}
