<?php

namespace Tests\Unit;

use App\Models\ScanIssue;
use App\Services\IssueCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_categorizes_image_issue_as_graphics(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37',
            'message' => 'Img element missing an alt attribute.',
        ]);

        $this->assertEquals('Graphics', IssueCategory::categorize($issue));
    }

    public function test_categorizes_heading_issue(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle1.Guideline1_3.1_3_1_A.H42',
            'message' => 'Heading tag found with no content.',
        ]);

        $this->assertEquals('Headings', IssueCategory::categorize($issue));
    }

    public function test_categorizes_form_issue(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle1.Guideline1_3.1_3_1.H44',
            'message' => 'Check that the label element is associated with a form control.',
        ]);

        $this->assertEquals('Forms', IssueCategory::categorize($issue));
    }

    public function test_categorizes_aria_issue(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle4.Guideline4_1.4_1_2.Aria2',
            'message' => 'Aria attribute is not valid.',
        ]);

        $this->assertEquals('ARIA', IssueCategory::categorize($issue));
    }

    public function test_categorizes_link_issue(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle2.Guideline2_4.2_4_4.H91',
            'message' => 'Link text is empty.',
        ]);

        $this->assertEquals('Links', IssueCategory::categorize($issue));
    }

    public function test_categorizes_metadata_issue(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle2.Guideline2_4.2_4_2.H25',
            'message' => 'The title element is empty.',
        ]);

        $this->assertEquals('Metadata', IssueCategory::categorize($issue));
    }

    public function test_categorizes_table_issue(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle1.Guideline1_3.1_3_1.H43',
            'message' => 'Table header has no associated cells.',
        ]);

        $this->assertEquals('Tables', IssueCategory::categorize($issue));
    }

    public function test_falls_back_to_general(): void
    {
        $issue = ScanIssue::factory()->make([
            'code' => 'WCAG2AA.Principle1.Guideline1_4.1_4_3.G18',
            'message' => 'Contrast ratio is insufficient.',
        ]);

        $this->assertEquals('General', IssueCategory::categorize($issue));
    }

    public function test_group_by_category_returns_sorted_array(): void
    {
        $issues = collect([
            ScanIssue::factory()->make([
                'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37',
                'message' => 'Img element missing an alt attribute.',
                'type' => 'error',
            ]),
            ScanIssue::factory()->make([
                'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H67',
                'message' => 'Img element has empty alt but no title.',
                'type' => 'warning',
            ]),
            ScanIssue::factory()->make([
                'code' => 'WCAG2AA.Principle1.Guideline1_4.1_4_3.G18',
                'message' => 'Contrast ratio is insufficient.',
                'type' => 'notice',
            ]),
        ]);

        $categories = IssueCategory::groupByCategory($issues);

        $this->assertCount(2, $categories);
        // Graphics has errors so should be first
        $this->assertEquals('Graphics', $categories[0]['name']);
        $this->assertEquals(1, $categories[0]['errors']);
        $this->assertEquals(1, $categories[0]['warnings']);
        $this->assertStringContains('error', $categories[0]['score_label']);

        $this->assertEquals('General', $categories[1]['name']);
    }

    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(str_contains($haystack, $needle), "Failed asserting that '$haystack' contains '$needle'.");
    }
}
