<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Mail\TrialExpiringMail;
use App\Models\User;

class TrialExpiringMailTest extends TestCase
{
    /**
     * Create a User model with attributes set.
     */
    protected function createUser(array $attributes): User
    {
        $user = new User();
        $user->forceFill($attributes);
        return $user;
    }

    /** @test */
    public function mailable_can_be_created_with_3_days()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 3);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function mailable_can_be_created_with_1_day()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 1);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function envelope_has_correct_subject_for_3_days()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 3);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('3 days', $envelope->subject);
    }

    /** @test */
    public function envelope_has_correct_subject_for_1_day()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 1);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('tomorrow', strtolower($envelope->subject));
    }

    /** @test */
    public function content_uses_trial_expiring_view()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 3);

        $content = $mail->content();

        $this->assertStringContainsString('emails.trial-expiring', $content->markdown);
    }

    /** @test */
    public function content_passes_user_and_days_left()
    {
        $user = $this->createUser([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 3);

        $content = $mail->content();

        $this->assertEquals($user, $content->with['user']);
        $this->assertEquals(3, $content->with['daysLeft']);
    }

    /** @test */
    public function content_sets_urgency_for_last_day()
    {
        $user = $this->createUser([
            'name' => 'Urgent User',
            'email' => 'urgent@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 1);

        $content = $mail->content();

        $this->assertEquals('urgent', $content->with['urgency']);
    }

    /** @test */
    public function content_sets_friendly_urgency_for_multiple_days()
    {
        $user = $this->createUser([
            'name' => 'Friendly User',
            'email' => 'friendly@example.com',
        ]);

        $mail = new TrialExpiringMail($user, 3);

        $content = $mail->content();

        $this->assertEquals('friendly', $content->with['urgency']);
    }
}
