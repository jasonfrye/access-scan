<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ScanIssue;
use App\Models\ScanPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScanIssueModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $issue = new ScanIssue();
        $fillable = $issue->getFillable();

        $this->assertContains('scan_page_id', $fillable);
        $this->assertContains('type', $fillable);
        $this->assertContains('code', $fillable);
        $this->assertContains('message', $fillable);
        $this->assertContains('context', $fillable);
        $this->assertContains('selector', $fillable);
        $this->assertContains('wcag_principle', $fillable);
        $this->assertContains('wcag_guideline', $fillable);
        $this->assertContains('wcag_criterion', $fillable);
        $this->assertContains('wcag_level', $fillable);
        $this->assertContains('impact', $fillable);
        $this->assertContains('recommendation', $fillable);
        $this->assertContains('help_url', $fillable);
    }

    /** @test */
    public function it_has_type_constants()
    {
        $this->assertEquals('error', ScanIssue::TYPE_ERROR);
        $this->assertEquals('warning', ScanIssue::TYPE_WARNING);
        $this->assertEquals('notice', ScanIssue::TYPE_NOTICE);
    }

    /** @test */
    public function it_has_wcag_level_constants()
    {
        $this->assertEquals('A', ScanIssue::LEVEL_A);
        $this->assertEquals('AA', ScanIssue::LEVEL_AA);
        $this->assertEquals('AAA', ScanIssue::LEVEL_AAA);
    }

    /** @test */
    public function it_has_impact_constants()
    {
        $this->assertEquals('critical', ScanIssue::IMPACT_CRITICAL);
        $this->assertEquals('serious', ScanIssue::IMPACT_SERIOUS);
        $this->assertEquals('moderate', ScanIssue::IMPACT_MODERATE);
        $this->assertEquals('minor', ScanIssue::IMPACT_MINOR);
    }

    /** @test */
    public function it_belongs_to_a_page()
    {
        $page = ScanPage::factory()->create();
        $issue = ScanIssue::factory()->for($page)->create();

        $this->assertInstanceOf(ScanPage::class, $issue->page);
        $this->assertEquals($page->id, $issue->page->id);
    }

    /** @test */
    public function it_checks_error_type()
    {
        $issue = ScanIssue::factory()->create(['type' => ScanIssue::TYPE_ERROR]);

        $this->assertTrue($issue->isError());
        $this->assertFalse($issue->isWarning());
        $this->assertFalse($issue->isNotice());
    }

    /** @test */
    public function it_checks_warning_type()
    {
        $issue = ScanIssue::factory()->create(['type' => ScanIssue::TYPE_WARNING]);

        $this->assertFalse($issue->isError());
        $this->assertTrue($issue->isWarning());
        $this->assertFalse($issue->isNotice());
    }

    /** @test */
    public function it_checks_notice_type()
    {
        $issue = ScanIssue::factory()->create(['type' => ScanIssue::TYPE_NOTICE]);

        $this->assertFalse($issue->isError());
        $this->assertFalse($issue->isWarning());
        $this->assertTrue($issue->isNotice());
    }

    /** @test */
    public function it_scopes_errors()
    {
        $page = ScanPage::factory()->create();
        ScanIssue::factory()->for($page)->count(3)->create(['type' => ScanIssue::TYPE_ERROR]);
        ScanIssue::factory()->for($page)->count(2)->create(['type' => ScanIssue::TYPE_WARNING]);

        $errors = ScanIssue::errors()->get();

        $this->assertCount(3, $errors);
        $this->assertTrue($errors->every(fn ($i) => $i->type === ScanIssue::TYPE_ERROR));
    }

    /** @test */
    public function it_scopes_warnings()
    {
        $page = ScanPage::factory()->create();
        ScanIssue::factory()->for($page)->count(2)->create(['type' => ScanIssue::TYPE_WARNING]);
        ScanIssue::factory()->for($page)->count(3)->create(['type' => ScanIssue::TYPE_NOTICE]);

        $warnings = ScanIssue::warnings()->get();

        $this->assertCount(2, $warnings);
        $this->assertTrue($warnings->every(fn ($i) => $i->type === ScanIssue::TYPE_WARNING));
    }

    /** @test */
    public function it_scopes_notices()
    {
        $page = ScanPage::factory()->create();
        ScanIssue::factory()->for($page)->count(4)->create(['type' => ScanIssue::TYPE_NOTICE]);
        ScanIssue::factory()->for($page)->count(1)->create(['type' => ScanIssue::TYPE_ERROR]);

        $notices = ScanIssue::notices()->get();

        $this->assertCount(4, $notices);
        $this->assertTrue($notices->every(fn ($i) => $i->type === ScanIssue::TYPE_NOTICE));
    }

    /** @test */
    public function it_scopes_wcag_level_a()
    {
        $page = ScanPage::factory()->create();
        ScanIssue::factory()->for($page)->count(2)->create(['wcag_level' => ScanIssue::LEVEL_A]);
        ScanIssue::factory()->for($page)->count(3)->create(['wcag_level' => ScanIssue::LEVEL_AA]);

        $levelA = ScanIssue::levelA()->get();

        $this->assertCount(2, $levelA);
        $this->assertTrue($levelA->every(fn ($i) => $i->wcag_level === ScanIssue::LEVEL_A));
    }

    /** @test */
    public function it_scopes_wcag_level_aa()
    {
        $page = ScanPage::factory()->create();
        ScanIssue::factory()->for($page)->count(3)->create(['wcag_level' => ScanIssue::LEVEL_AA]);
        ScanIssue::factory()->for($page)->count(1)->create(['wcag_level' => ScanIssue::LEVEL_AAA]);

        $levelAA = ScanIssue::levelAA()->get();

        $this->assertCount(3, $levelAA);
        $this->assertTrue($levelAA->every(fn ($i) => $i->wcag_level === ScanIssue::LEVEL_AA));
    }

    /** @test */
    public function it_generates_wcag_reference()
    {
        $issue = ScanIssue::factory()->create([
            'wcag_level' => 'AA',
            'wcag_principle' => '1',
            'wcag_guideline' => '1_1',
            'wcag_criterion' => '1_1_1',
        ]);

        $reference = $issue->wcag_reference;

        $this->assertEquals('WCAG 2.1 Level AA - 1.1.1', $reference);
    }

    /** @test */
    public function it_handles_missing_wcag_data_in_reference()
    {
        $issue = ScanIssue::factory()->create([
            'wcag_level' => null,
            'wcag_principle' => null,
            'wcag_guideline' => null,
            'wcag_criterion' => null,
        ]);

        $reference = $issue->wcag_reference;

        $this->assertEquals('WCAG 2.1 Level A', $reference);
    }

    /** @test */
    public function it_returns_full_criterion_code()
    {
        $issue = ScanIssue::factory()->create([
            'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37',
        ]);

        $code = $issue->full_criterion_code;

        $this->assertEquals('WCAG2AA.Principle1.Guideline1_1.1_1_1.H37', $code);
    }

    /** @test */
    public function it_returns_unknown_for_missing_code()
    {
        $issue = ScanIssue::factory()->create(['code' => null]);

        $code = $issue->full_criterion_code;

        $this->assertEquals('Unknown', $code);
    }

    /** @test */
    public function it_casts_type_to_string()
    {
        $issue = ScanIssue::factory()->create(['type' => ScanIssue::TYPE_ERROR]);

        $this->assertIsString($issue->type);
    }

    /** @test */
    public function it_casts_wcag_level_to_string()
    {
        $issue = ScanIssue::factory()->create(['wcag_level' => ScanIssue::LEVEL_AA]);

        $this->assertIsString($issue->wcag_level);
    }

    /** @test */
    public function it_casts_impact_to_string()
    {
        $issue = ScanIssue::factory()->create(['impact' => ScanIssue::IMPACT_CRITICAL]);

        $this->assertIsString($issue->impact);
    }
}
