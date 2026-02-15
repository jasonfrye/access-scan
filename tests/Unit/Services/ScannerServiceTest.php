<?php

namespace Tests\Unit\Services;

use App\Services\ScannerService;
use Tests\TestCase;

class ScannerServiceTest extends TestCase
{
    protected ScannerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ScannerService;
    }

    /** @test */
    public function it_can_calculate_score_with_no_issues()
    {
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [0, 0, 0]);

        $this->assertEquals(100, $score);
    }

    /** @test */
    public function it_can_calculate_score_with_only_minor_issues()
    {
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [0, 0, 10]);

        // Notices don't impact score
        $this->assertEquals(100, $score);
    }

    /** @test */
    public function it_can_calculate_score_with_warnings()
    {
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [0, 5, 0]);

        // 5 warnings * 3 = 15 weighted, 100 * e^(-0.003 * 15) = ~95.60
        $this->assertEqualsWithDelta(95.60, $score, 0.5);
    }

    /** @test */
    public function it_can_calculate_score_with_errors()
    {
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [3, 0, 0]);

        // 3 errors * 10 = 30 weighted, 100 * e^(-0.003 * 30) = ~91.39
        $this->assertEqualsWithDelta(91.39, $score, 0.5);
    }

    /** @test */
    public function it_can_calculate_score_with_mixed_severity()
    {
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [2, 3, 5]);

        // (2*10) + (3*3) = 20 + 9 = 29 weighted (notices excluded)
        // 100 * e^(-0.003 * 29) = ~91.66
        $this->assertEqualsWithDelta(91.66, $score, 0.5);
    }

    /** @test */
    public function it_clamps_score_to_zero_for_critical_issues()
    {
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [100, 100, 100]);

        $this->assertLessThanOrEqual(5, $score);
    }

    /** @test */
    public function it_returns_100_for_no_positive_issues()
    {
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [0, 0, 0]);

        $this->assertEquals(100, $score);
    }

    /** @test */
    public function it_produces_realistic_scores_for_typical_sites()
    {
        // A mediocre site: 25 errors
        // (25*10) = 250 weighted, 100 * e^(-0.003 * 250) = ~47.24
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [25, 0, 0]);
        $this->assertGreaterThan(40, $score);
        $this->assertLessThan(55, $score);

        // A decent site: 5 errors
        // (5*10) = 50 weighted, 100 * e^(-0.003 * 50) = ~86.07
        $score = $this->invokeProtectedMethod($this->service, 'calculateScore', [5, 0, 0]);
        $this->assertGreaterThan(80, $score);
        $this->assertLessThan(95, $score);
    }

    /** @test */
    public function it_validates_localhost_urls_in_production()
    {
        app()->detectEnvironment(fn () => 'production');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot scan localhost URLs');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['http://localhost/page']);
    }

    /** @test */
    public function it_validates_localhost_with_127_0_0_1_in_production()
    {
        app()->detectEnvironment(fn () => 'production');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot scan localhost URLs');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['http://127.0.0.1/page']);
    }

    /** @test */
    public function it_validates_localhost_with_dot_local_in_production()
    {
        app()->detectEnvironment(fn () => 'production');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot scan localhost URLs');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['http://myserver.local/page']);
    }

    /** @test */
    public function it_validates_ip_addresses_in_production()
    {
        app()->detectEnvironment(fn () => 'production');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot scan IP addresses');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['http://192.168.1.1/page']);
    }

    /** @test */
    public function it_validates_private_ip_ranges_in_production()
    {
        app()->detectEnvironment(fn () => 'production');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot scan IP addresses');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['http://10.0.0.1/page']);
    }

    /** @test */
    public function it_allows_localhost_urls_in_local_environment()
    {
        app()->detectEnvironment(fn () => 'local');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['http://localhost/page']);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_validates_invalid_url_format()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL format');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['not-a-url']);
    }

    /** @test */
    public function it_validates_urls_without_protocol()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL format');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['example.com/page']);
    }

    /** @test */
    public function it_validates_ftp_urls()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('URL must use HTTP or HTTPS protocol');

        $this->invokeProtectedMethod($this->service, 'validateUrl', ['ftp://example.com/page']);
    }

    /** @test */
    public function it_accepts_valid_https_url()
    {
        $this->invokeProtectedMethod($this->service, 'validateUrl', ['https://example.com/page']);

        $this->assertTrue(true); // No exception thrown
    }

    /** @test */
    public function it_accepts_valid_http_url()
    {
        $this->invokeProtectedMethod($this->service, 'validateUrl', ['http://example.com/page']);

        $this->assertTrue(true); // No exception thrown
    }

    /** @test */
    public function it_extracts_wcag_principle_from_code()
    {
        $principle = $this->invokeProtectedMethod($this->service, 'extractWcagPrinciple', ['WCAG2AA.Principle1.Guideline1_1.1_1_1']);

        $this->assertEquals('1', $principle);
    }

    /** @test */
    public function it_returns_empty_for_missing_principle()
    {
        $principle = $this->invokeProtectedMethod($this->service, 'extractWcagPrinciple', ['some-random-code']);

        $this->assertEquals('', $principle);
    }

    /** @test */
    public function it_extracts_wcag_guideline_from_code()
    {
        $guideline = $this->invokeProtectedMethod($this->service, 'extractWcagGuideline', ['WCAG2AA.Principle1.Guideline1_1.1_1_1']);

        $this->assertEquals('1_1', $guideline);
    }

    /** @test */
    public function it_extracts_wcag_criterion_from_code()
    {
        $criterion = $this->invokeProtectedMethod($this->service, 'extractWcagCriterion', ['WCAG2AA.Principle1.Guideline1_1.1_1_1']);

        $this->assertEquals('1_1_1', $criterion);
    }

    /** @test */
    public function it_determines_wcag_level_aa()
    {
        $level = $this->invokeProtectedMethod($this->service, 'determineWcagLevel', ['WCAG2AA.Principle1.Guideline1_1.1_1_1']);

        $this->assertEquals('AA', $level);
    }

    /** @test */
    public function it_determines_wcag_level_a()
    {
        $level = $this->invokeProtectedMethod($this->service, 'determineWcagLevel', ['WCAG2A.Principle1.Guideline1_1.1_1_1']);

        $this->assertEquals('A', $level);
    }

    /** @test */
    public function it_determines_wcag_level_aaa()
    {
        $level = $this->invokeProtectedMethod($this->service, 'determineWcagLevel', ['WCAG2AAA.Principle1.Guideline1_1.1_1_1']);

        $this->assertEquals('AAA', $level);
    }

    /** @test */
    public function it_defaults_to_wcag_level_a_for_unknown()
    {
        $level = $this->invokeProtectedMethod($this->service, 'determineWcagLevel', ['unknown-code']);

        $this->assertEquals('A', $level);
    }

    /** @test */
    public function it_determines_impact_as_critical_for_errors()
    {
        $impact = $this->invokeProtectedMethod($this->service, 'determineImpact', ['error']);

        $this->assertEquals('critical', $impact);
    }

    /** @test */
    public function it_determines_impact_as_moderate_for_warnings()
    {
        $impact = $this->invokeProtectedMethod($this->service, 'determineImpact', ['warning']);

        $this->assertEquals('moderate', $impact);
    }

    /** @test */
    public function it_determines_impact_as_minor_for_notices()
    {
        $impact = $this->invokeProtectedMethod($this->service, 'determineImpact', ['notice']);

        $this->assertEquals('minor', $impact);
    }

    /** @test */
    public function it_parses_pa11y_json_output_with_issues()
    {
        $pa11yOutput = json_encode([
            [
                'type' => 'error',
                'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37',
                'message' => 'Images must have alternate text',
                'context' => '<img src="test.jpg">',
                'selector' => 'html > body > img',
                'page' => ['title' => 'Test Page'],
            ],
            [
                'type' => 'warning',
                'code' => 'WCAG2AA.Principle2.Guideline2_4.2_4_4.H77',
                'message' => 'Links must have discernible text',
                'context' => '<a href="#"><span></span></a>',
                'selector' => 'html > body > a',
                'page' => ['title' => 'Test Page'],
            ],
        ]);

        $result = $this->invokeProtectedMethod($this->service, 'parsePa11yOutput', [$pa11yOutput]);

        $this->assertArrayHasKey('issues', $result);
        $this->assertArrayHasKey('counts', $result);
        $this->assertCount(2, $result['issues']);
        $this->assertEquals(1, $result['counts']['error']);
        $this->assertEquals(1, $result['counts']['warning']);
        $this->assertEquals(0, $result['counts']['notice']);
    }

    /** @test */
    public function it_parses_pa11y_output_with_wrapped_issues()
    {
        $pa11yOutput = json_encode([
            'issues' => [
                [
                    'type' => 'error',
                    'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37',
                    'message' => 'Images must have alternate text',
                ],
            ],
        ]);

        $result = $this->invokeProtectedMethod($this->service, 'parsePa11yOutput', [$pa11yOutput]);

        $this->assertCount(1, $result['issues']);
    }

    /** @test */
    public function it_handles_empty_pa11y_output()
    {
        $pa11yOutput = json_encode([]);

        $result = $this->invokeProtectedMethod($this->service, 'parsePa11yOutput', [$pa11yOutput]);

        $this->assertEmpty($result['issues']);
        $this->assertEquals(0, $result['counts']['error']);
        $this->assertEquals(0, $result['counts']['warning']);
        $this->assertEquals(0, $result['counts']['notice']);
    }

    /** @test */
    public function it_throws_exception_for_invalid_json()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON output from Pa11y');

        $this->invokeProtectedMethod($this->service, 'parsePa11yOutput', ['not valid json{']);
    }

    /** @test */
    public function it_generates_help_url_for_issue_code()
    {
        $url = $this->invokeProtectedMethod($this->service, 'generateHelpUrl', ['WCAG2AA.Principle1.Guideline1_1.1_1_1.H37']);

        $this->assertStringContainsString('dequeuniversity.com/rules/axe', $url);
        $this->assertStringContainsString('h37', $url);
    }

    /** @test */
    public function it_generates_recommendation_for_alt_text_issue()
    {
        $issue = [
            'type' => 'error',
            'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.ImgAltIsTooLong',
            'message' => 'Img alt is too long',
        ];

        $recommendation = $this->invokeProtectedMethod($this->service, 'generateRecommendation', [$issue]);

        // The code checks for 'ImgAltIsTooLong' in the code field
        $this->assertStringContainsString('Shorten the alt text', $recommendation);
    }

    /** @test */
    public function it_generates_recommendation_for_link_text_issue()
    {
        $issue = [
            'type' => 'warning',
            'code' => 'WCAG2AA.Principle2.Guideline2_4.2_4_4.LinkHasText',
            'message' => 'Link has no text',
        ];

        $recommendation = $this->invokeProtectedMethod($this->service, 'generateRecommendation', [$issue]);

        // The code checks for 'LinkHasText' in the code field
        $this->assertStringContainsString('descriptive text', $recommendation);
    }

    /** @test */
    public function it_determines_max_pages_as_one_for_quick_scans()
    {
        $scan = new \App\Models\Scan(['scan_type' => \App\Models\Scan::TYPE_QUICK]);

        $maxPages = $this->invokeProtectedMethod($this->service, 'determineMaxPages', [$scan]);

        $this->assertEquals(1, $maxPages);
    }

    /** @test */
    public function it_determines_max_pages_from_user_plan_for_full_scans()
    {
        $user = new \App\Models\User(['plan' => 'monthly']);
        $scan = new \App\Models\Scan(['scan_type' => \App\Models\Scan::TYPE_FULL]);
        $scan->setRelation('user', $user);

        $maxPages = $this->invokeProtectedMethod($this->service, 'determineMaxPages', [$scan]);

        $this->assertEquals($user->getMaxPagesPerScan(), $maxPages);
    }

    /** @test */
    public function it_determines_max_pages_for_guest_scans()
    {
        $scan = new \App\Models\Scan(['scan_type' => \App\Models\Scan::TYPE_FULL]);
        $scan->setRelation('user', null);

        $maxPages = $this->invokeProtectedMethod($this->service, 'determineMaxPages', [$scan]);

        $this->assertEquals(5, $maxPages);
    }

    /** @test */
    public function it_can_set_and_get_timeout()
    {
        $this->service->setTimeout(600);

        $this->assertEquals(600, $this->service->getTimeout());
    }

    /** @test */
    public function it_can_set_and_get_max_pages()
    {
        $this->service->setMaxPages(50);

        $this->assertEquals(50, $this->service->getMaxPages());
    }

    /**
     * Helper method to invoke protected/private methods.
     */
    private function invokeProtectedMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
