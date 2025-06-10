<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RemindFillQuestionnaireNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $periode;

    public function __construct($periode)
    {
        $this->periode = $periode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pengingat Pengisian Kuesioner')
            ->greeting('Halo!')
            ->line('Kami mengingatkan Anda untuk segera mengisi kuesioner periode: ' . $this->periode->periode_name)
            ->action('Isi Kuesioner', url('/login'))
            ->line('Terima kasih atas partisipasi Anda.');
    }
}
