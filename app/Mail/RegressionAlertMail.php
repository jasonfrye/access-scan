<?php

namespace App\Mail;

use App\Models\Scan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegressionAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Scan $currentScan,
        protected Scan $previousScan,
        protected int $scoreDrop
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Accessibility Score Dropped - '.$this->currentScan->domain,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.regression-alert',
            with: [
                'domain' => $this->currentScan->domain,
                'currentScore' => $this->currentScan->score,
                'previousScore' => $this->previousScan->score,
                'scoreDrop' => $this->scoreDrop,
                'currentGrade' => $this->currentScan->grade ?? 'N/A',
                'previousGrade' => $this->previousScan->grade ?? 'N/A',
            ],
        );
    }
}
