<?php

namespace App\Notifications;

// use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegSukses extends Notification
{
    // use Queueable;
    
    protected string $username;
    protected string $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // \Log::info("Notif RegSukses triggered: " . $this->username . $this->password);
        
        return (new MailMessage)
            ->subject('Detail Akun Kinerja SAC Kamu')
            ->greeting('Hai, Sobat!')
            ->line('Ini dia detail akun Kinerja SAC kamu:')
            ->line("Username:")
            ->line("**{$this->username}**")
            ->line("Password:")
            ->line("**{$this->password}**")
            ->line('Saat ini akun kamu masih belum aktif, ya. Mohon tunggu informasi selanjutnya dari tim kami.')
            ->line('')
            ->line('*Email ini dikirim otomatis, mohon untuk tidak membalas.*')
            ->salutation('Tetap semangat dan sukses selalu — Tim Kami')
            ->success();
    }
}
