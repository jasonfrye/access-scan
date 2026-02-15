<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_branding_section_visible_for_agency_users(): void
    {
        $user = User::factory()->create(['plan' => 'agency']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('White-Label Branding');
    }

    public function test_branding_section_hidden_for_non_agency_users(): void
    {
        $user = User::factory()->create(['plan' => 'monthly']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertDontSee('White-Label Branding');
    }

    public function test_branding_section_hidden_for_free_users(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertDontSee('White-Label Branding');
    }

    public function test_agency_user_can_update_company_name(): void
    {
        $user = User::factory()->create(['plan' => 'agency']);

        $response = $this->actingAs($user)->put('/profile/branding', [
            'company_name' => 'My Agency',
        ]);

        $response->assertRedirect('/profile');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'company_name' => 'My Agency',
        ]);
    }

    public function test_agency_user_can_upload_logo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['plan' => 'agency']);
        $file = UploadedFile::fake()->image('logo.png', 200, 60);

        $response = $this->actingAs($user)->put('/profile/branding', [
            'company_name' => 'My Agency',
            'company_logo' => $file,
        ]);

        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->company_logo_path);
        Storage::disk('public')->assertExists($user->company_logo_path);
    }

    public function test_old_logo_deleted_on_new_upload(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'plan' => 'agency',
            'company_logo_path' => 'logos/old-logo.png',
        ]);
        Storage::disk('public')->put('logos/old-logo.png', 'old');

        $file = UploadedFile::fake()->image('new-logo.png', 200, 60);

        $this->actingAs($user)->put('/profile/branding', [
            'company_logo' => $file,
        ]);

        Storage::disk('public')->assertMissing('logos/old-logo.png');
    }

    public function test_agency_user_can_remove_logo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'plan' => 'agency',
            'company_logo_path' => 'logos/test-logo.png',
        ]);
        Storage::disk('public')->put('logos/test-logo.png', 'content');

        $response = $this->actingAs($user)->put('/profile/branding', [
            'remove_logo' => '1',
        ]);

        $response->assertRedirect('/profile');
        $user->refresh();
        $this->assertNull($user->company_logo_path);
        Storage::disk('public')->assertMissing('logos/test-logo.png');
    }

    public function test_non_agency_user_cannot_update_branding(): void
    {
        $user = User::factory()->create(['plan' => 'monthly']);

        $response = $this->actingAs($user)->put('/profile/branding', [
            'company_name' => 'Hack',
        ]);

        $response->assertStatus(403);
    }

    public function test_logo_validation_rejects_oversized_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['plan' => 'agency']);
        $file = UploadedFile::fake()->image('logo.png')->size(2048);

        $response = $this->actingAs($user)->put('/profile/branding', [
            'company_logo' => $file,
        ]);

        $response->assertSessionHasErrors('company_logo');
    }

    public function test_agency_user_can_preview_branded_pdf(): void
    {
        $user = User::factory()->create(['plan' => 'agency', 'company_name' => 'Test Agency']);

        $response = $this->actingAs($user)->get('/profile/branding/preview');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_non_agency_user_cannot_preview_branded_pdf(): void
    {
        $user = User::factory()->create(['plan' => 'monthly']);

        $response = $this->actingAs($user)->get('/profile/branding/preview');

        $response->assertStatus(403);
    }
}
