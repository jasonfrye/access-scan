<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Scan;
use App\Models\User;
use App\Models\ScanPage;
use App\Models\ScanIssue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScanModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $scan = new Scan();
        $fillable = $scan->getFillable();

        $this->assertContains('user_id', $fillable);
        $this->assertContains('url', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('scan_type', $fillable);
        $this->assertContains('pages_scanned', $fillable);
        $this->assertContains('issues_found', $fillable);
        $this->assertContains('score', $fillable);
        $this->assertContains('grade', $fillable);
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $scan = new Scan();
        $casts = $scan->getCasts();

        $this->assertEquals('integer', $casts['pages_scanned']);
        $this->assertEquals('float', $casts['score']); // float for JSON serialization
        $this->assertEquals('datetime', $casts['started_at']);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $scan->user);
        $this->assertEquals($user->id, $scan->user->id);
    }

    /** @test */
    public function it_has_many_pages()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();
        ScanPage::factory()->count(3)->for($scan)->create();

        $this->assertCount(3, $scan->pages);
        $this->assertInstanceOf(ScanPage::class, $scan->pages->first());
    }

    /** @test */
    public function it_has_many_issues()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();
        $page = ScanPage::factory()->for($scan)->create();
        ScanIssue::factory()->count(5)->for($page)->create();

        $this->assertCount(5, $scan->issues);
        $this->assertInstanceOf(ScanIssue::class, $scan->issues->first());
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals('pending', Scan::STATUS_PENDING);
        $this->assertEquals('running', Scan::STATUS_RUNNING);
        $this->assertEquals('completed', Scan::STATUS_COMPLETED);
        $this->assertEquals('failed', Scan::STATUS_FAILED);
    }

    /** @test */
    public function it_has_scan_type_constants()
    {
        $this->assertEquals('quick', Scan::TYPE_QUICK);
        $this->assertEquals('full', Scan::TYPE_FULL);
        $this->assertEquals('scheduled', Scan::TYPE_SCHEDULED);
    }

    /** @test */
    public function it_checks_pending_status()
    {
        $scan = Scan::factory()->create(['status' => Scan::STATUS_PENDING]);

        $this->assertTrue($scan->isPending());
        $this->assertFalse($scan->isRunning());
        $this->assertFalse($scan->isCompleted());
        $this->assertFalse($scan->isFailed());
    }

    /** @test */
    public function it_checks_running_status()
    {
        $scan = Scan::factory()->create(['status' => Scan::STATUS_RUNNING]);

        $this->assertFalse($scan->isPending());
        $this->assertTrue($scan->isRunning());
        $this->assertFalse($scan->isCompleted());
        $this->assertFalse($scan->isFailed());
    }

    /** @test */
    public function it_checks_completed_status()
    {
        $scan = Scan::factory()->create(['status' => Scan::STATUS_COMPLETED]);

        $this->assertFalse($scan->isPending());
        $this->assertFalse($scan->isRunning());
        $this->assertTrue($scan->isCompleted());
        $this->assertFalse($scan->isFailed());
    }

    /** @test */
    public function it_checks_failed_status()
    {
        $scan = Scan::factory()->create(['status' => Scan::STATUS_FAILED]);

        $this->assertFalse($scan->isPending());
        $this->assertFalse($scan->isRunning());
        $this->assertFalse($scan->isCompleted());
        $this->assertTrue($scan->isFailed());
    }

    /** @test */
    public function it_marks_as_running()
    {
        $scan = Scan::factory()->create(['status' => Scan::STATUS_PENDING]);

        $scan->markAsRunning();

        $this->assertEquals(Scan::STATUS_RUNNING, $scan->fresh()->status);
        $this->assertNotNull($scan->fresh()->started_at);
    }

    /** @test */
    public function it_marks_as_completed_with_results()
    {
        $scan = Scan::factory()->create(['status' => Scan::STATUS_RUNNING]);

        $scan->markAsCompleted([
            'pages_scanned' => 5,
            'issues_found' => 10,
            'errors_count' => 3,
            'warnings_count' => 4,
            'notices_count' => 3,
            'score' => 85.50,
        ]);

        $fresh = $scan->fresh();
        $this->assertEquals(Scan::STATUS_COMPLETED, $fresh->status);
        $this->assertEquals(5, $fresh->pages_scanned);
        $this->assertEquals(10, $fresh->issues_found);
        $this->assertEquals(3, $fresh->errors_count);
        $this->assertEquals(4, $fresh->warnings_count);
        $this->assertEquals(3, $fresh->notices_count);
        $this->assertEquals(85.50, $fresh->score);
        $this->assertNotNull($fresh->completed_at);
    }

    /** @test */
    public function it_marks_as_failed_with_error_message()
    {
        $scan = Scan::factory()->create(['status' => Scan::STATUS_RUNNING]);

        $scan->markAsFailed('Connection timeout');

        $fresh = $scan->fresh();
        $this->assertEquals(Scan::STATUS_FAILED, $fresh->status);
        $this->assertEquals('Connection timeout', $fresh->error_message);
        $this->assertNotNull($fresh->completed_at);
    }

    /** @test */
    public function it_calculates_grade_a_for_high_score()
    {
        $scan = Scan::factory()->create(['score' => 95]);

        $scan->calculateGrade();

        $this->assertEquals('A', $scan->grade);
    }

    /** @test */
    public function it_calculates_grade_b_for_good_score()
    {
        $scan = Scan::factory()->create(['score' => 85]);

        $scan->calculateGrade();

        $this->assertEquals('B', $scan->grade);
    }

    /** @test */
    public function it_calculates_grade_c_for_average_score()
    {
        $scan = Scan::factory()->create(['score' => 75]);

        $scan->calculateGrade();

        $this->assertEquals('C', $scan->grade);
    }

    /** @test */
    public function it_calculates_grade_d_for_low_score()
    {
        $scan = Scan::factory()->create(['score' => 65]);

        $scan->calculateGrade();

        $this->assertEquals('D', $scan->grade);
    }

    /** @test */
    public function it_calculates_grade_f_for_fail_score()
    {
        $scan = Scan::factory()->create(['score' => 55]);

        $scan->calculateGrade();

        $this->assertEquals('F', $scan->grade);
    }

    /** @test */
    public function it_displays_formatted_score()
    {
        $scan = Scan::factory()->create(['score' => 87.5]);

        $display = $scan->score_display;

        $this->assertEquals('88/100', $display);
    }

    /** @test */
    public function it_displays_na_for_missing_score()
    {
        $scan = Scan::factory()->create(['score' => null]);

        $display = $scan->score_display;

        $this->assertEquals('N/A', $display);
    }

    /** @test */
    public function it_extracts_domain_from_url()
    {
        $scan = Scan::factory()->create(['url' => 'https://example.com/path/to/page']);

        $domain = $scan->domain;

        $this->assertEquals('example.com', $domain);
    }

    /** @test */
    public function scope_pending_returns_only_pending_scans()
    {
        Scan::factory()->create(['status' => Scan::STATUS_PENDING]);
        Scan::factory()->create(['status' => Scan::STATUS_PENDING]);
        Scan::factory()->create(['status' => Scan::STATUS_COMPLETED]);

        $pending = Scan::pending()->get();

        $this->assertCount(2, $pending);
        $this->assertTrue($pending->every(fn ($s) => $s->isPending()));
    }

    /** @test */
    public function scope_running_returns_only_running_scans()
    {
        Scan::factory()->create(['status' => Scan::STATUS_RUNNING]);
        Scan::factory()->create(['status' => Scan::STATUS_PENDING]);
        Scan::factory()->create(['status' => Scan::STATUS_COMPLETED]);

        $running = Scan::running()->get();

        $this->assertCount(1, $running);
        $this->assertTrue($running->every(fn ($s) => $s->isRunning()));
    }

    /** @test */
    public function scope_completed_returns_only_completed_scans()
    {
        Scan::factory()->create(['status' => Scan::STATUS_COMPLETED]);
        Scan::factory()->create(['status' => Scan::STATUS_PENDING]);
        Scan::factory()->create(['status' => Scan::STATUS_FAILED]);

        $completed = Scan::completed()->get();

        $this->assertCount(1, $completed);
        $this->assertTrue($completed->every(fn ($s) => $s->isCompleted()));
    }

    /** @test */
    public function scope_failed_returns_only_failed_scans()
    {
        Scan::factory()->create(['status' => Scan::STATUS_FAILED]);
        Scan::factory()->create(['status' => Scan::STATUS_PENDING]);
        Scan::factory()->create(['status' => Scan::STATUS_COMPLETED]);

        $failed = Scan::failed()->get();

        $this->assertCount(1, $failed);
        $this->assertTrue($failed->every(fn ($s) => $s->isFailed()));
    }

    /** @test */
    public function scope_for_user_returns_scans_for_specific_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Scan::factory()->for($user1)->count(3)->create();
        Scan::factory()->for($user2)->count(2)->create();

        $user1Scans = Scan::forUser($user1->id)->get();

        $this->assertCount(3, $user1Scans);
        $this->assertTrue($user1Scans->every(fn ($s) => $s->user_id === $user1->id));
    }
}
