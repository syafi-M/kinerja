<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UseAbsenNotification;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function sendNotificationToUser(User $user): void
    {
        if (! $user->hasReceivedNotificationToday()) {
            Notification::send($user, new UseAbsenNotification());
            $user->markNotificationAsSent();
        }
    }
}
