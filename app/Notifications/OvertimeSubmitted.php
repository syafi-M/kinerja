<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OvertimeSubmitted extends Notification
{
    use Queueable;
    public function __construct(public $overtime) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'overtime',
            'title' => 'Pengajuan Lembur Baru',
            'message' => 'Ada pengajuan lembur baru',
            'id_ref' => $this->overtime->id,
            'kerjasama_id' => $this->overtime->user->kerjasama_id
        ];
    }
}
