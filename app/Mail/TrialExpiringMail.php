<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        protected int $daysLeft
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->daysLeft) {
            3 => 'Your AccessScan trial ends in 3 days',
            1 => 'Your AccessScan trial ends tomorrow',
            default => 'Your AccessScan trial ends in '.$this->daysLeft.' days',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trial-expiring',
            with: [
                'user' => $this->user,
                'daysLeft' => $this->daysLeft,
                'urgency' => $this->daysLeft <= 1 ? 'urgent' : 'friendly',
            ],
        );
    }
}
