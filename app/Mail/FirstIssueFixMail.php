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

class FirstIssueFixMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        protected Scan $scan,
        protected array $topIssues = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How to Fix Your Top Accessibility Issues',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.first-issue-fix',
            with: [
                'user' => $this->user,
                'scan' => $this->scan,
                'topIssues' => $this->topIssues,
            ],
        );
    }
}
