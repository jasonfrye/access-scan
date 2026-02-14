<?php

namespace Tests\Unit\Mail;

use App\Mail\ReEngagementMail;
use App\Models\User;
use Tests\TestCase;

class ReEngagementMailTest extends TestCase
{
    /**
     * Create a User model with attributes set.
     */
    protected function createUser(array $attributes): User
    {
        $user = new User;
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

        $mail = new ReEngagementMail($user, 30);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function envelope_has_correct_subject()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new ReEngagementMail($user, 45);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('miss', strtolower($envelope->subject));
        $this->assertStringContainsString('Access Report Card', $envelope->subject);
    }

    /** @test */
    public function content_uses_re_engagement_view()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new ReEngagementMail($user, 60);

        $content = $mail->content();

        $this->assertStringContainsString('emails.re-engagement', $content->markdown);
    }

    /** @test */
    public function content_passes_user_and_days_inactive()
    {
        $user = $this->createUser([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $mail = new ReEngagementMail($user, 90);

        $content = $mail->content();

        $this->assertEquals($user, $content->with['user']);
        $this->assertEquals(90, $content->with['daysInactive']);
    }

    /** @test */
    public function works_with_different_inactivity_periods()
    {
        $user = $this->createUser([
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
        ]);

        // Just verify mailables can be created with different values
        $mail30 = new ReEngagementMail($user, 30);
        $this->assertNotNull($mail30);

        $mail60 = new ReEngagementMail($user, 60);
        $this->assertNotNull($mail60);

        $mail90 = new ReEngagementMail($user, 90);
        $this->assertNotNull($mail90);
    }
}
