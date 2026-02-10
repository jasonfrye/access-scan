<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanBenefitMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        protected array $unusedFeatures = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re Not Using All Your Pro Features!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.plan-benefit',
            with: [
                'user' => $this->user,
                'unusedFeatures' => $this->unusedFeatures,
            ],
        );
    }
}
