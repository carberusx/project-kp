<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranDitolak extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pendaftaran $pendaftaran
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informasi Pendaftaran Magang — MagangDPMPTSP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pendaftaran-ditolak',
        );
    }
}
