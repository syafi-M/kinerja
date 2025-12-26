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
            ->from('kinerja@sac-po.com', 'Sac-Po Admin')
            ->subject("[{$this->otp}] Kode Verifikasi OTP Kamu")
            ->markdown('emails.otp-notif', [
                'otp' => $this->otp,
                'expiresAt' => $this->expiresAt,
            ]);
    }
}
