<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Mail\WeeklyDigestMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class WeeklyDigestMailTest extends TestCase
{
    /** @test */
    public function mailable_can_be_created()
    {
        $user = User::factory()->make([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new WeeklyDigestMail($user, [
            'scans' => 5,
            'pages' => 25,
            'issues' => 12,
            'avg_score' => 85.5,
        ]);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function envelope_has_correct_subject()
    {
        $user = User::factory()->make();
        $mail = new WeeklyDigestMail($user, []);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('Weekly', $envelope->subject);
        $this->assertStringContainsString('Digest', $envelope->subject);
    }

    /** @test */
    public function content_uses_weekly_digest_view()
    {
        $user = User::factory()->make();
        $mail = new WeeklyDigestMail($user, []);

        $content = $mail->content();

        $this->assertStringContainsString('emails.weekly-digest', $content->markdown);
    }

    /** @test */
    public function content_passes_user_and_stats_to_view()
    {
        $user = User::factory()->make([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $stats = [
            'scans' => 3,
            'pages' => 15,
            'issues' => 8,
            'avg_score' => 92.0,
        ];

        $mail = new WeeklyDigestMail($user, $stats);

        $content = $mail->content();

        $this->assertEquals($user, $content->with['user']);
        $this->assertEquals($stats, $content->with['stats']);
    }

    /** @test */
    public function renders_correctly_without_stats()
    {
        $user = User::factory()->make([
            'id' => 1,
            'name' => 'Empty User',
            'email' => 'empty@example.com',
        ]);

        $mail = new WeeklyDigestMail($user, []);

        $rendered = $mail->render();

        $this->assertStringContainsString('Weekly Accessibility Digest', $rendered);
        $this->assertStringContainsString($user->name, $rendered);
    }
}
