<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification implements ShouldQueue
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
    $email = $notifiable->routeNotificationFor('mail'); // Dapatkan email tujuan notifikasi

    return (new MailMessage)
        ->subject('Verifikasi Email Anda')
        ->line("Silakan klik tautan berikut untuk memverifikasi email: $email")
        ->action('Verifikasi Email', url('/alumni/password/' . $this->token))
        ->line('Terima kasih telah menggunakan aplikasi kami!');
}
    
    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
