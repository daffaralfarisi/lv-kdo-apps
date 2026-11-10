<?php

namespace App\Mail;

use App\Enums\ModeEmail;
use App\Models\Kendaraan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Dikirim sinkron agar admin langsung tahu hasil pengiriman (tanpa queue worker).
 */
class KendaraanLayakServiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  list<array{kode: string, label: string}>  $alasan
     */
    public function __construct(
        public Kendaraan $kendaraan,
        public array $alasan,
        public ModeEmail $mode,
    ) {}

    public function envelope(): Envelope
    {
        $awalan = $this->mode === ModeEmail::Dev ? '[DEV] ' : '';

        return new Envelope(
            subject: $awalan.'Kendaraan Dinas Layak Service - '.$this->kendaraan->kdo_nomor_polisi,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.kendaraan-layak-service');
    }
}
