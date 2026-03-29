<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $nombre = $notifiable->nombres ?? 'Usuario';
        $expireMinutes = 3;

        return (new MailMessage)
            ->subject('Código de Verificación - PresupuestoBO')
            ->view('emails.reset-password', [
                'code' => $this->code,
                'nombre' => $nombre,
                'expireMinutes' => $expireMinutes,
            ]);
    }
}
