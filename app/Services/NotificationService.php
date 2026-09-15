<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /** @param array<string, mixed> $data */
    public function createForUser(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return $user->appNotifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ?: null,
        ]);
    }
}
