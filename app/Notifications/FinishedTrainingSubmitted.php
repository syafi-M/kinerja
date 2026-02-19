<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FinishedTrainingSubmitted extends Notification
{
    use Queueable;

    public function __construct(public $finishedTraining) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'finished_training',
            'title' => 'Pengajuan Lepas Training',
            'message' => 'Ada pengajuan lepas training baru',
            'id_ref' => $this->finishedTraining->id,
            'kerjasama_id' => (int) $this->finishedTraining->user?->kerjasama_id,
        ];
    }
}

