<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Mail\FirstIssueFixMail;
use App\Models\Scan;
use App\Models\User;

class FirstIssueFixMailTest extends TestCase
{
    /**
     * Create a Scan model with attributes set.
     */
    protected function createScan(array $attributes): Scan
    {
        $scan = new Scan();
        $scan->forceFill($attributes);
        return $scan;
    }

    /** @test */
    public function mailable_can_be_created()
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $scan = $this->createScan([
            'url' => 'https://example.com',
            'domain' => 'example.com',
        ]);

        $mail = new FirstIssueFixMail($user, $scan, []);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function envelope_has_correct_subject()
    {
        $user = new User();
        $user->forceFill(['name' => 'Test', 'email' => 'test@test.com']);

        $scan = $this->createScan(['domain' => 'example.com']);

        $mail = new FirstIssueFixMail($user, $scan, []);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('Fix', $envelope->subject);
        $this->assertStringContainsString('Accessibility', $envelope->subject);
    }

    /** @test */
    public function content_uses_first_issue_fix_view()
    {
        $user = new User();
        $user->forceFill(['name' => 'Test', 'email' => 'test@test.com']);

        $scan = $this->createScan(['domain' => 'example.com']);

        $mail = new FirstIssueFixMail($user, $scan, []);

        $content = $mail->content();

        $this->assertStringContainsString('emails.first-issue-fix', $content->markdown);
    }

    /** @test */
    public function content_passes_all_required_data()
    {
        $user = new User();
        $user->forceFill(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $scan = $this->createScan([
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'score' => 72,
        ]);

        $topIssues = [
            ['type' => 'Alt Text', 'message' => 'Missing alt text', 'code' => 'ImgAltIsTooLong'],
            ['type' => 'Contrast', 'message' => 'Low contrast', 'code' => 'ColorContrast'],
        ];

        $mail = new FirstIssueFixMail($user, $scan, $topIssues);

        $content = $mail->content();

        $this->assertEquals($user, $content->with['user']);
        $this->assertEquals($scan, $content->with['scan']);
        $this->assertEquals($topIssues, $content->with['topIssues']);
    }

    /** @test */
    public function works_with_empty_issues_array()
    {
        $user = new User();
        $user->forceFill(['name' => 'Empty User', 'email' => 'empty@example.com']);

        $scan = $this->createScan(['domain' => 'example.com']);

        $mail = new FirstIssueFixMail($user, $scan, []);

        $content = $mail->content();

        // Should still pass with empty issues
        $this->assertEquals([], $content->with['topIssues']);
    }
}
