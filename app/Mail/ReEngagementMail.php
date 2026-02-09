<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReEngagementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        protected int $daysInactive
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We miss you! Come back to AccessScan',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.re-engagement',
            with: [
                'user' => $this->user,
                'daysInactive' => $this->daysInactive,
            ],
        );
    }
}
