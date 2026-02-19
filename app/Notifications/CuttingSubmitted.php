<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CuttingSubmitted extends Notification
{
    use Queueable;

    public function __construct(public $cutting) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'cutting',
            'title' => 'Pengajuan Cutting',
            'message' => 'Ada pengajuan cutting baru',
            'id_ref' => $this->cutting->id,
            'kerjasama_id' => (int) $this->cutting->user?->kerjasama_id,
        ];
    }
}

