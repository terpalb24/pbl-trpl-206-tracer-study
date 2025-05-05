<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * The verification token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = url('/alumni/password?token=' . $this->token);

        return (new MailMessage)
            ->subject('Verifikasi Email Alumni')
            ->greeting('Halo!')
            ->line('Terima kasih telah menggunakan aplikasi tracer study polibatam .')
            ->line('Silakan klik tombol di bawah untuk verifikasi dan ubah password Anda.')
            ->action('Verifikasi Email', $url);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
