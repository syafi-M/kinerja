<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PersonInSubmitted extends Notification
{
    use Queueable;

    public function __construct(public $personIn, public int $kerjasamaId) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'person_in',
            'title' => 'Pengajuan Personil Masuk',
            'message' => 'Ada pengajuan personil masuk baru',
            'id_ref' => $this->personIn->id,
            'kerjasama_id' => $this->kerjasamaId,
        ];
    }
}
