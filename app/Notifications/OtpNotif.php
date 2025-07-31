<?php

namespace App\Notifications;

// use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtpNotif extends Notification
{
    // use Queueable;

    protected string $otp;
    protected string $expiresAt;

    /**
     * @param string $otp         Kode OTP
     * @param string $expiresAt   Format jam, misal "14:30"
     */
    public function __construct(string $otp, string $expiresAt)
    {
        $this->otp = $otp;
        $this->expiresAt = $expiresAt;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Kode OTP Kamu Siap Digunakan!')
            ->greeting('Hai, Sobat!')
            ->line('Yuk, gunakan kode OTP di bawah ini untuk melanjutkan:')
            ->line("**{$this->otp}**")
            ->line("Kode ini hanya berlaku sampai pukul: **{$this->expiresAt}**")
            ->line('Jangan berikan kode ini ke siapa pun ya — bahkan ke yang ngaku-ngaku tim kami! 😎')
            ->line('Kalau kamu merasa nggak minta kode ini, cukup abaikan email ini.')
            ->line('')
            ->line('📩 *Email ini dikirim otomatis, mohon untuk tidak membalas.*')
            ->salutation('Semangat terus 💪 — Tim Kami')
            ->success();
    }
}
