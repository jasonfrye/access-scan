<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleSocialiteTest extends TestCase
{
    use RefreshDatabase;

    private function mockSocialiteUser(string $id = '123456', string $email = 'google@example.com', string $name = 'Google User'): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getName')->andReturn($name);

        $driver = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $driver->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);
    }

    public function test_redirect_to_google(): void
    {
        $response = $this->get(route('auth.google'));

        $response->assertRedirectContains('accounts.google.com');
    }

    public function test_callback_creates_new_user(): void
    {
        $this->mockSocialiteUser();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'google@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('123456', $user->google_id);
        $this->assertEquals('Google User', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->password);
    }

    public function test_callback_links_existing_user_by_email(): void
    {
        $user = User::factory()->create(['email' => 'google@example.com']);

        $this->mockSocialiteUser();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertEquals('123456', $user->fresh()->google_id);
    }

    public function test_callback_logs_in_existing_user_by_google_id(): void
    {
        $user = User::factory()->create([
            'email' => 'google@example.com',
            'google_id' => '123456',
        ]);

        $this->mockSocialiteUser();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_callback_handles_google_failure_gracefully(): void
    {
        $driver = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $driver->shouldReceive('user')->andThrow(new \Exception('OAuth failed'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
