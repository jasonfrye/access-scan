<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailPreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsubscribe_page_loads_with_valid_signature(): void
    {
        $user = User::factory()->create();
        $url = URL::signedRoute('email.unsubscribe', $user);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee('Email Preferences');
    }

    public function test_unsubscribe_page_returns_403_without_signature(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('email.unsubscribe', $user));

        $response->assertStatus(403);
    }

    public function test_unsubscribe_updates_preferences_with_valid_signature(): void
    {
        $user = User::factory()->create([
            'marketing_emails_enabled' => true,
            'system_emails_enabled' => true,
        ]);

        $url = URL::signedRoute('email.unsubscribe.update', $user);

        $response = $this->post($url, [
            'marketing_emails_enabled' => '0',
            'system_emails_enabled' => '0',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertFalse($user->marketing_emails_enabled);
        $this->assertFalse($user->system_emails_enabled);
    }

    public function test_unsubscribe_update_returns_403_without_signature(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('email.unsubscribe.update', $user), [
            'marketing_emails_enabled' => '0',
        ]);

        $response->assertStatus(403);
    }

    public function test_profile_email_preferences_update(): void
    {
        $user = User::factory()->create([
            'marketing_emails_enabled' => true,
            'system_emails_enabled' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.email-preferences.update'), [
            'marketing_emails_enabled' => '0',
            'system_emails_enabled' => '1',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertFalse($user->marketing_emails_enabled);
        $this->assertTrue($user->system_emails_enabled);
    }

    public function test_wants_email_returns_correct_values(): void
    {
        $user = User::factory()->create([
            'marketing_emails_enabled' => false,
            'system_emails_enabled' => true,
        ]);

        $this->assertFalse($user->wantsEmail('marketing'));
        $this->assertTrue($user->wantsEmail('system'));
        $this->assertTrue($user->wantsEmail('unknown'));
    }

    public function test_notification_service_respects_marketing_preference(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'marketing_emails_enabled' => false,
        ]);

        $service = new NotificationService;
        $service->sendReEngagementEmail($user, 14);

        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    }

    public function test_notification_service_respects_system_preference(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'system_emails_enabled' => false,
        ]);

        $scan = \App\Models\Scan::factory()->create(['user_id' => $user->id]);

        $service = new NotificationService;
        $service->sendScanCompleteNotification($scan);

        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    }

    public function test_notification_service_sends_when_preferences_enabled(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'marketing_emails_enabled' => true,
        ]);

        $service = new NotificationService;
        $service->sendReEngagementEmail($user, 14);

        Mail::assertQueued(\App\Mail\ReEngagementMail::class);
    }
}
