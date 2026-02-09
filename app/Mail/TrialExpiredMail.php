<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your AccessScan trial has expired',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trial-expired',
            with: [
                'user' => $this->user,
            ],
        );
    }
}
