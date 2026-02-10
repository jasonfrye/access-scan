<?php

namespace App\Services;

use App\Models\ScanIssue;
use Illuminate\Support\Collection;

class IssueCategory
{
    /**
     * Category definitions with matching logic.
     *
     * @var array<string, array{keywords: string[], techniques: string[], criteria: string[]}>
     */
    protected static array $categories = [
        'Headings' => [
            'techniques' => ['H42', 'H69'],
            'criteria' => ['2_4_6', '2_4_10', '1_3_1'],
            'messageKeywords' => ['heading'],
        ],
        'Links' => [
            'techniques' => ['H30', 'H91'],
            'criteria' => ['2_4_4', '2_4_9'],
            'messageKeywords' => ['link'],
        ],
        'Graphics' => [
            'techniques' => ['H37', 'H67', 'H36'],
            'criteria' => ['1_1_1', '1_4_5'],
            'messageKeywords' => ['image', 'img', 'alt text', 'alt attribute'],
        ],
        'Forms' => [
            'techniques' => ['H44', 'H65', 'H71', 'H32'],
            'criteria' => ['3_3_1', '3_3_2', '3_3_3', '3_3_4'],
            'messageKeywords' => ['form', 'label', 'input', 'select', 'textarea', 'fieldset'],
        ],
        'ARIA' => [
            'techniques' => [],
            'criteria' => ['4_1_1', '4_1_2'],
            'messageKeywords' => ['aria'],
            'codeKeywords' => ['Aria'],
        ],
        'Landmarks' => [
            'techniques' => [],
            'criteria' => [],
            'messageKeywords' => ['landmark', 'banner', 'navigation', 'main', 'contentinfo'],
            'codeKeywords' => ['landmark'],
        ],
        'Tables' => [
            'techniques' => ['H43', 'H51', 'H63', 'H73'],
            'criteria' => [],
            'messageKeywords' => ['table', 'caption', 'header cell'],
        ],
        'Lists' => [
            'techniques' => ['H48'],
            'criteria' => [],
            'messageKeywords' => ['list'],
        ],
        'Metadata' => [
            'techniques' => ['H25', 'H57'],
            'criteria' => ['2_4_2', '3_1_1', '3_1_2'],
            'messageKeywords' => ['title', 'lang', 'language'],
        ],
        'Interactive Content' => [
            'techniques' => ['SCR20', 'G90', 'G202'],
            'criteria' => ['2_1_1', '2_1_2', '2_4_3', '2_4_7'],
            'messageKeywords' => ['keyboard', 'focus', 'tab order', 'tabindex'],
        ],
        'Tabs' => [
            'techniques' => [],
            'criteria' => [],
            'messageKeywords' => ['tablist', 'tabpanel', 'tab role'],
            'codeKeywords' => ['tablist', 'tabpanel'],
        ],
    ];

    /**
     * Categorize a single issue by its Pa11y code and message.
     */
    public static function categorize(ScanIssue $issue): string
    {
        $code = $issue->code ?? '';
        $message = strtolower($issue->message ?? '');

        foreach (static::$categories as $category => $rules) {
            // Check technique codes (e.g., H37, H44)
            foreach ($rules['techniques'] as $technique) {
                if (str_contains($code, $technique)) {
                    return $category;
                }
            }

            // Check WCAG criteria in the code (e.g., 1_1_1)
            foreach ($rules['criteria'] as $criterion) {
                if (str_contains($code, $criterion)) {
                    // For criteria that match multiple categories, check message keywords to disambiguate
                    if ($criterion === '1_3_1') {
                        // 1.3.1 is broad — check message to pick the right category
                        foreach ($rules['messageKeywords'] as $keyword) {
                            if (str_contains($message, $keyword)) {
                                return $category;
                            }
                        }

                        continue;
                    }

                    return $category;
                }
            }

            // Check code-level keywords
            foreach ($rules['codeKeywords'] ?? [] as $keyword) {
                if (str_contains($code, $keyword)) {
                    return $category;
                }
            }

            // Check message keywords
            foreach ($rules['messageKeywords'] as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $category;
                }
            }
        }

        return 'General';
    }

    /**
     * Group issues by category with metadata.
     *
     * @return array<int, array{name: string, issues: Collection, errors: int, warnings: int, notices: int, score_label: string, score_class: string}>
     */
    public static function groupByCategory(Collection $issues): array
    {
        $grouped = [];

        foreach ($issues as $issue) {
            $category = static::categorize($issue);
            if (! isset($grouped[$category])) {
                $grouped[$category] = collect();
            }
            $grouped[$category]->push($issue);
        }

        // Sort categories: ones with errors first, then by issue count
        $results = [];
        foreach ($grouped as $name => $categoryIssues) {
            $errors = $categoryIssues->where('type', 'error')->count();
            $warnings = $categoryIssues->where('type', 'warning')->count();
            $notices = $categoryIssues->where('type', 'notice')->count();

            if ($errors > 0) {
                $scoreLabel = $errors.' '.str('error')->plural($errors);
                $scoreClass = 'bg-red-100 text-red-700';
            } elseif ($warnings > 0) {
                $scoreLabel = $warnings.' '.str('warning')->plural($warnings);
                $scoreClass = 'bg-yellow-100 text-yellow-700';
            } elseif ($notices > 0) {
                $scoreLabel = $notices.' '.str('notice')->plural($notices);
                $scoreClass = 'bg-blue-100 text-blue-700';
            } else {
                $scoreLabel = 'Passed';
                $scoreClass = 'bg-green-100 text-green-700';
            }

            $results[] = [
                'name' => $name,
                'issues' => $categoryIssues,
                'errors' => $errors,
                'warnings' => $warnings,
                'notices' => $notices,
                'score_label' => $scoreLabel,
                'score_class' => $scoreClass,
            ];
        }

        // Sort: errors first, then warnings, then by count
        usort($results, function ($a, $b) {
            if ($a['errors'] !== $b['errors']) {
                return $b['errors'] - $a['errors'];
            }
            if ($a['warnings'] !== $b['warnings']) {
                return $b['warnings'] - $a['warnings'];
            }

            return $b['notices'] - $a['notices'];
        });

        return $results;
    }
}
