<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Mail\WelcomeMail;
use App\Models\User;

class WelcomeMailTest extends TestCase
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

        $mail = new WelcomeMail($user);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function envelope_has_correct_subject()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new WelcomeMail($user);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('Welcome', $envelope->subject);
    }

    /** @test */
    public function content_uses_welcome_view()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new WelcomeMail($user);

        $content = $mail->content();

        $this->assertStringContainsString('emails.welcome', $content->markdown);
    }

    /** @test */
    public function content_passes_user_to_view()
    {
        $user = $this->createUser([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $mail = new WelcomeMail($user);

        $content = $mail->content();

        $this->assertEquals($user, $content->with['user']);
    }
}
