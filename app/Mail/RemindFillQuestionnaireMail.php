<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class RemindFillQuestionnaireMail extends Mailable
{
    use Queueable, SerializesModels;

    public $periode;

    public function __construct($periode)
    {
        $this->periode = $periode;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: 'Pengingat Pengisian Kuesioner',
        );
    }

    public function build()
    {
        return $this->view('emails.remind-fill-questionnaire')
            ->with(['periode' => $this->periode]);
    }
}
