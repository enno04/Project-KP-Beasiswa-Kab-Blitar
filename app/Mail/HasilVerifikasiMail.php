<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HasilVerifikasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public Pendaftaran $pendaftaran;
    public string $hasilVerifikasi;
    public int $totalSkor;
    public ?string $catatan;

    /**
     * Create a new message instance.
     */
    public function __construct(Pendaftaran $pendaftaran, string $hasilVerifikasi, int $totalSkor, ?string $catatan = null)
    {
        $this->pendaftaran = $pendaftaran;
        $this->hasilVerifikasi = $hasilVerifikasi;
        $this->totalSkor = $totalSkor;
        $this->catatan = $catatan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = $this->hasilVerifikasi === 'lulus' ? 'DITERIMA' : 'DITOLAK';
        return new Envelope(
            subject: "Hasil Verifikasi Beasiswa Blitar Mengabdi — {$status}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.hasil_verifikasi',
        );
    }
}
