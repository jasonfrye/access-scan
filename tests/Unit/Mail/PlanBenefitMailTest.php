<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Mail\PlanBenefitMail;
use App\Models\User;

class PlanBenefitMailTest extends TestCase
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

        $mail = new PlanBenefitMail($user, []);

        $this->assertNotNull($mail);
    }

    /** @test */
    public function envelope_has_correct_subject()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new PlanBenefitMail($user, []);

        $envelope = $mail->envelope();

        $this->assertStringContainsString("You're", $envelope->subject);
        $this->assertStringContainsString('Not', $envelope->subject);
        $this->assertStringContainsString('Using', $envelope->subject);
    }

    /** @test */
    public function content_uses_plan_benefit_view()
    {
        $user = $this->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $mail = new PlanBenefitMail($user, []);

        $content = $mail->content();

        $this->assertStringContainsString('emails.plan-benefit', $content->markdown);
    }

    /** @test */
    public function content_passes_user_and_unused_features()
    {
        $user = $this->createUser([
            'name' => 'Pro User',
            'email' => 'pro@example.com',
        ]);

        $features = [
            ['name' => 'Scheduled Scans', 'description' => 'Auto weekly scans'],
            ['name' => 'PDF Export', 'description' => 'Download reports'],
        ];

        $mail = new PlanBenefitMail($user, $features);

        $content = $mail->content();

        $this->assertEquals($user, $content->with['user']);
        $this->assertEquals($features, $content->with['unusedFeatures']);
    }

    /** @test */
    public function works_with_empty_features_array()
    {
        $user = $this->createUser([
            'name' => 'Empty User',
            'email' => 'empty@example.com',
        ]);

        $mail = new PlanBenefitMail($user, []);

        $content = $mail->content();

        // Should work with empty features
        $this->assertEquals([], $content->with['unusedFeatures']);
    }
}
