<?php

namespace App\Mail;

use App\Models\Scan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScoreImproveMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        protected Scan $currentScan,
        protected Scan $previousScan,
        protected int $improvement
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 Your accessibility score improved by {$this->improvement} points!",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.score-improve',
            with: [
                'user' => $this->user,
                'currentScan' => $this->currentScan,
                'previousScan' => $this->previousScan,
                'improvement' => $this->improvement,
            ],
        );
    }
}
