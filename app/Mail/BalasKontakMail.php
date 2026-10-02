<?php

namespace App\Mail;

use App\Models\Kontak;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BalasKontakMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Kontak $kontak, public string $balasan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->kontak->subjek . ' | SMA Negeri 8 Yogyakarta',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'email.balas-kontak');
    }
}