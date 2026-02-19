<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PersonOutSubmitted extends Notification
{
    use Queueable;
    public function __construct(public $personOut) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $user = User::withTrashed()->find($this->personOut->user_id);

        return [
            'type' => 'person_out',
            'title' => 'Pengajuan Personil Keluar',
            'message' => 'Ada pengajuan personil keluar',
            'id_ref' => $this->personOut->id,
            'kerjasama_id' => $user->kerjasama_id
        ];
    }
}
