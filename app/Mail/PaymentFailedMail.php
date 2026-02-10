<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        protected string $planName,
        protected string $amount,
        protected ?string $lastFour = null,
        protected ?string $errorMessage = null,
        protected ?string $updatePaymentUrl = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required - Payment Failed for Your AccessScan Subscription',
        );
    }

    public function content(): Content
    {
        $updateUrl = $this->updatePaymentUrl ?? config('app.url').'/billing';

        return new Content(
            markdown: 'emails.payment-failed',
            with: [
                'user' => $this->user,
                'planName' => $this->planName,
                'amount' => $this->amount,
                'lastFour' => $this->lastFour,
                'errorMessage' => $this->errorMessage ?? 'Your payment method was declined.',
                'updatePaymentUrl' => $updateUrl,
            ],
        );
    }
}
