<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Mail\TrialExpiredMail;
use App\Models\User;

class TrialExpiredMailTest extends TestCase
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
    public function mailable_can_be_created()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiredMail($user);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function envelope_has_correct_subject()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiredMail($user);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('expired', strtolower($envelope->subject));
    }

    /** @test */
    public function content_uses_trial_expired_view()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new TrialExpiredMail($user);

        $content = $mail->content();

        $this->assertStringContainsString('emails.trial-expired', $content->markdown);
    }

    /** @test */
    public function content_passes_user_to_view()
    {
        $user = $this->createUser([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $mail = new TrialExpiredMail($user);

        $content = $mail->content();

        $this->assertEquals($user, $content->with['user']);
    }
}
