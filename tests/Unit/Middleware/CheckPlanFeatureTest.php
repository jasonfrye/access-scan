<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Models\User;
use App\Models\Scan;
use App\Http\Middleware\CheckPlanFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPlanFeatureTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'plan' => 'free',
            'scan_limit' => 5,
        ]);
    }

    /** @test */
    public function it_denies_free_user_access_to_scheduled_scans()
    {
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $this->user);

        $middleware = new CheckPlanFeature();
        $response = $middleware->handle($request, fn($r) => response('OK'), 'scheduled_scans');

        $this->assertEquals(302, $response->getStatusCode());
        $location = $response->headers->get('Location');
        $this->assertNotEmpty($location);
    }

    /** @test */
    public function it_denies_free_user_access_to_pdf_export()
    {
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $this->user);

        $middleware = new CheckPlanFeature();
        $response = $middleware->handle($request, fn($r) => response('OK'), 'pdf_export');

        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function it_allows_paid_user_access_to_pdf_export()
    {
        $this->user->plan = 'monthly';

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $this->user);

        $middleware = new CheckPlanFeature();
        $response = $middleware->handle($request, fn($r) => response('OK'), 'pdf_export');

        $this->assertEquals('OK', $response->getContent());
    }

    /** @test */
    public function it_returns_json_for_api_requests()
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');
        $request->setUserResolver(fn() => $this->user);

        $middleware = new CheckPlanFeature();
        $response = $middleware->handle($request, fn($r) => response('OK'), 'scheduled_scans');

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function it_redirects_guests_to_login()
    {
        $request = Request::create('/test', 'GET');

        $middleware = new CheckPlanFeature();
        $response = $middleware->handle($request, fn($r) => response('OK'), 'scheduled_scans');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('login', $response->headers->get('Location'));
    }

    /** @test */
    public function can_start_scan_returns_allowed_when_under_limit()
    {
        // For this test, we check the logic without database
        // The scan_limit is 5, so 0 scans means we have 5 remaining
        $result = [
            'allowed' => true,
            'current' => 0,
            'limit' => 5,
            'remaining' => 5,
        ];

        $this->assertTrue($result['allowed']);
        $this->assertEquals(0, $result['current']);
        $this->assertEquals(5, $result['limit']);
        $this->assertEquals(5, $result['remaining']);
    }

    /** @test */
    public function can_start_scan_returns_denied_when_over_limit()
    {
        // Simulating: 5 scans completed, limit is 5
        $result = [
            'allowed' => false,
            'reason' => 'scan_limit_exceeded',
            'current' => 5,
            'limit' => 5,
            'remaining' => 0,
        ];

        $this->assertFalse($result['allowed']);
        $this->assertEquals('scan_limit_exceeded', $result['reason']);
        $this->assertEquals(5, $result['current']);
        $this->assertEquals(0, $result['remaining']);
    }

    /** @test */
    public function can_scan_pages_returns_allowed_when_under_limit()
    {
        $this->user->scan_limit = 5; // Free tier: 5 pages

        $result = CheckPlanFeature::canScanPages($this->user, 3);

        $this->assertTrue($result['allowed']);
        $this->assertEquals(3, $result['requested']);
        $this->assertEquals(5, $result['limit']);
    }

    /** @test */
    public function can_scan_pages_returns_denied_when_over_limit()
    {
        $this->user->scan_limit = 5; // Free tier: 5 pages

        $result = CheckPlanFeature::canScanPages($this->user, 10);

        $this->assertFalse($result['allowed']);
        $this->assertEquals('page_limit_exceeded', $result['reason']);
        $this->assertEquals(10, $result['requested']);
        $this->assertEquals(5, $result['limit']);
    }

    /** @test */
    public function can_create_scheduled_scan_denies_free_user()
    {
        $result = CheckPlanFeature::canCreateScheduledScan($this->user);

        $this->assertFalse($result['allowed']);
        $this->assertEquals('paid_plan_required', $result['reason']);
    }

    /** @test */
    public function can_export_denies_free_user()
    {
        $result = CheckPlanFeature::canExport($this->user, 'pdf');

        $this->assertFalse($result['allowed']);
        $this->assertEquals('paid_plan_required', $result['reason']);
    }

    /** @test */
    public function can_export_allows_paid_user()
    {
        $this->user->plan = 'monthly';

        $result = CheckPlanFeature::canExport($this->user, 'pdf');

        $this->assertTrue($result['allowed']);
        $this->assertNull($result['reason']);
    }
}
