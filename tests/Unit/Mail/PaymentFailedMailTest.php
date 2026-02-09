<?php

namespace Tests\Unit\Mail;

use App\Mail\PaymentFailedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFailedMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_failed_mail_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new PaymentFailedMail(
            user: $user,
            planName: 'Monthly Pro',
            amount: '$29.00',
            lastFour: '4242',
            errorMessage: 'Your card was declined.'
        );

        $this->assertNotNull($mail);
    }

    public function test_payment_failed_mail_has_correct_subject(): void
    {
        $user = User::factory()->create();

        $mail = new PaymentFailedMail(
            user: $user,
            planName: 'Monthly Pro',
            amount: '$29.00'
        );

        $this->assertStringContainsString(
            'Payment Failed',
            $mail->envelope()->subject
        );
    }

    public function test_payment_failed_mail_contains_user_data(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $mail = new PaymentFailedMail(
            user: $user,
            planName: 'Monthly Pro',
            amount: '$29.00',
            lastFour: '4242',
            errorMessage: 'Card declined'
        );

        $envelope = $mail->envelope();
        $content = $mail->content();

        $this->assertNotNull($content);
        $this->assertNotNull($envelope);
    }

    public function test_payment_failed_mail_uses_correct_view(): void
    {
        $user = User::factory()->create();

        $mail = new PaymentFailedMail(
            user: $user,
            planName: 'Monthly Pro',
            amount: '$29.00'
        );

        $content = $mail->content();

        $this->assertEquals('emails.payment-failed', $content->markdown);
    }

    public function test_payment_failed_mail_passes_data_to_view(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $mail = new PaymentFailedMail(
            user: $user,
            planName: 'Yearly Pro',
            amount: '$197.00',
            lastFour: '1234',
            errorMessage: 'Insufficient funds'
        );

        $content = $mail->content();
        $viewData = $content->with;

        $this->assertArrayHasKey('user', $viewData);
        $this->assertArrayHasKey('planName', $viewData);
        $this->assertArrayHasKey('amount', $viewData);
        $this->assertEquals($user->name, $viewData['user']->name);
        $this->assertEquals('Yearly Pro', $viewData['planName']);
        $this->assertEquals('$197.00', $viewData['amount']);
    }
}
