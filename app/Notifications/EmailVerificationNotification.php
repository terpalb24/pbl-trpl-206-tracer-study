<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;;
class EmailVerificationNotification extends Notification
{
    use Queueable;
    /**
     * The alumni instance.
     *
     * @var \App\Models\Tb_Alumni
     */
    public $alumni;

    /**
     * Create a new notification instance.
     */
    public function __construct($alumni)
    {
        $this->alumni = $alumni;
    }
        //
    

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->line('Hallo ' . $this->alumni->name . ',')
        ->line('Silakan klik tombol di bawah ini untuk memverifikasi email Anda.')
        ->action('Verifikasi Email', route('alumni.password.form', ['token' => $this->alumni->email_verification_token]))
        ->line('Terima kasih telah menggunakan aplikasi tracer study polibatam');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
