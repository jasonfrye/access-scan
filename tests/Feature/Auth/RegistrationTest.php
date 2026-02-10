<?php

namespace Tests\Feature\Auth;

use App\Models\GuestScan;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_guest_scan_is_attached_via_session_on_registration(): void
    {
        $scan = Scan::create([
            'user_id' => null,
            'url' => 'https://example.com',
            'status' => Scan::STATUS_PENDING,
            'scan_type' => Scan::TYPE_QUICK,
        ]);

        $response = $this->withSession(['guest_scan_id' => $scan->id])
            ->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertEquals($user->id, $scan->fresh()->user_id);
    }

    public function test_guest_scan_is_attached_via_email_on_registration(): void
    {
        $scan = Scan::create([
            'user_id' => null,
            'url' => 'https://example.com',
            'status' => Scan::STATUS_PENDING,
            'scan_type' => Scan::TYPE_QUICK,
        ]);

        GuestScan::create([
            'ip_address' => '127.0.0.1',
            'email' => 'test@example.com',
            'scan_id' => $scan->id,
        ]);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertEquals($user->id, $scan->fresh()->user_id);
    }

    public function test_guest_scan_already_owned_is_not_stolen(): void
    {
        $existingUser = User::factory()->create();
        $scan = Scan::create([
            'user_id' => $existingUser->id,
            'url' => 'https://example.com',
            'status' => Scan::STATUS_PENDING,
            'scan_type' => Scan::TYPE_QUICK,
        ]);

        $this->withSession(['guest_scan_id' => $scan->id])
            ->post('/register', [
                'name' => 'Test User',
                'email' => 'new@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $this->assertEquals($existingUser->id, $scan->fresh()->user_id);
    }
}
