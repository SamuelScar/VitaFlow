<?php

namespace App\Mail;

use App\Models\ConviteAdmin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConviteAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ConviteAdmin $convite,
        public string $urlAceite,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Convite para administrar o VitaFlow',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.convite-admin',
        );
    }
}
