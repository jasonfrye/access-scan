<?php

namespace App\Mail;

use App\Models\Scan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScanCompleteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Scan $scan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Accessibility Scan is Complete - '.$this->scan->domain,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.scan-complete',
            with: [
                'scan' => $this->scan,
                'score' => $this->scan->score,
                'grade' => $this->scan->grade ?? 'N/A',
                'issuesCount' => $this->scan->pages->flatMap->issues->count(),
                'errorsCount' => $this->scan->errors_count,
                'warningsCount' => $this->scan->warnings_count,
                'topIssues' => $this->getTopIssues(),
            ],
        );
    }

    protected function getTopIssues(): array
    {
        return $this->scan->pages
            ->flatMap->issues
            ->where('type', 'error')
            ->take(5)
            ->map(fn ($issue) => [
                'message' => $issue->message,
                'wcag' => $issue->wcag_reference,
                'level' => $issue->wcag_level,
            ])
            ->toArray();
    }
}
