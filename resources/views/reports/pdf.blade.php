<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility Report - {{ $scan->domain }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1f2937;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            color: #4f46e5;
            margin-bottom: 8px;
        }
        .header .subtitle {
            color: #6b7280;
            font-size: 14px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .score-card {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .score-box {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background: #f9fafb;
        }
        .score-box .value {
            font-size: 32px;
            font-weight: 700;
        }
        .score-box .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .score-box.grade .value {
            color: {{ $scan->score >= 90 ? '#059669' : ($scan->score >= 70 ? '#d97706' : ($scan->score >= 50 ? '#ea580c' : '#dc2626')) }};
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .stat-item {
            text-align: center;
            padding: 12px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .stat-item .value {
            font-size: 20px;
            font-weight: 700;
        }
        .stat-item .label {
            font-size: 10px;
            color: #6b7280;
        }
        .stat-item.errors .value { color: #dc2626; }
        .stat-item.warnings .value { color: #d97706; }
        .stat-item.notices .value { color: #2563eb; }
        
        .issues-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .issues-table th {
            background: #4f46e5;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 600;
        }
        .issues-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .issues-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge.error { background: #fee2e2; color: #dc2626; }
        .badge.warning { background: #fef3c7; color: #d97706; }
        .badge.notice { background: #dbeafe; color: #2563eb; }
        
        .recommendations {
            margin-top: 15px;
        }
        .recommendation {
            padding: 12px;
            margin-bottom: 10px;
            background: #f0fdf4;
            border-left: 3px solid #22c55e;
            border-radius: 0 6px 6px 0;
        }
        .recommendation.high {
            background: #fef2f2;
            border-left-color: #dc2626;
        }
        .recommendation .title {
            font-weight: 700;
            margin-bottom: 5px;
        }
        .recommendation .fix {
            color: #374151;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .two-column {
            column-count: 2;
            column-gap: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @if($whiteLabel ?? false)
            <h1>Accessibility Report</h1>
        @else
            <h1>AccessScan Accessibility Report</h1>
        @endif
        <p class="subtitle">{{ $scan->domain }}</p>
        <p style="margin-top: 5px; color: #9ca3af; font-size: 11px;">
            Scanned {{ $scan->completed_at->format('F j, Y \a\t g:i A') }}
        </p>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <h2 class="section-title">Executive Summary</h2>
        
        <div class="score-card">
            <div class="score-box grade">
                <div class="value">{{ $scan->grade ?? 'N/A' }}</div>
                <div class="label">Overall Grade</div>
            </div>
            <div class="score-box">
                <div class="value">{{ number_format($scan->score, 0) }}</div>
                <div class="label">Score (0-100)</div>
            </div>
            <div class="score-box">
                <div class="value">{{ $stats['total_pages'] }}</div>
                <div class="label">Pages Scanned</div>
            </div>
            <div class="score-box">
                <div class="value">{{ $stats['total_issues'] }}</div>
                <div class="label">Total Issues</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-item errors">
                <div class="value">{{ $stats['errors'] }}</div>
                <div class="label">Errors</div>
            </div>
            <div class="stat-item warnings">
                <div class="value">{{ $stats['warnings'] }}</div>
                <div class="label">Warnings</div>
            </div>
            <div class="stat-item notices">
                <div class="value">{{ $stats['notices'] }}</div>
                <div class="label">Notices</div>
            </div>
            <div class="stat-item">
                <div class="value">{{ $stats['critical_issues'] }}</div>
                <div class="label">Critical (WCAG A/AA)</div>
            </div>
        </div>
    </div>

    <!-- WCAG Compliance Overview -->
    <div class="section">
        <h2 class="section-title">WCAG Compliance Overview</h2>
        
        <table class="issues-table">
            <thead>
                <tr>
                    <th>Principle</th>
                    <th>Issues</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $principles = [
                        'Perceivable' => $scan->pages->flatMap->issues->where('wcag_principle', 'perceivable')->count(),
                        'Operable' => $scan->pages->flatMap->issues->where('wcag_principle', 'operable')->count(),
                        'Understandable' => $scan->pages->flatMap->issues->where('wcag_principle', 'understandable')->count(),
                        'Robust' => $scan->pages->flatMap->issues->where('wcag_principle', 'robust')->count(),
                    ];
                @endphp
                @foreach($principles as $principle => $count)
                <tr>
                    <td>{{ $principle }}</td>
                    <td>{{ $count }}</td>
                    <td>
                        @if($count === 0)
                            <span class="badge" style="background: #dcfce7; color: #16a34a;">Pass</span>
                        @elseif($count < 5)
                            <span class="badge warning">Minor</span>
                        @else
                            <span class="badge error">Needs Work</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top Issues by Page -->
    <div class="section">
        <h2 class="section-title">Issues by Page</h2>
        
        <table class="issues-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Score</th>
                    <th>Errors</th>
                    <th>Warnings</th>
                    <th>Notices</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scan->pages->sortByDesc(fn($p) => $p->issues_count) as $page)
                <tr>
                    <td>{{ parse_url($page->url, PHP_URL_PATH) ?: '/' }}</td>
                    <td>
                        <strong style="color: {{ $page->score >= 90 ? '#059669' : ($page->score >= 70 ? '#d97706' : '#dc2626') }}">
                            {{ number_format($page->score, 0) }}
                        </strong>
                    </td>
                    <td>{{ $page->issues->where('type', 'error')->count() }}</td>
                    <td>{{ $page->issues->where('type', 'warning')->count() }}</td>
                    <td>{{ $page->issues->where('type', 'notice')->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Priority Recommendations -->
    <div class="section">
        <h2 class="section-title">Priority Recommendations</h2>
        
        <div class="recommendations two-column">
            @foreach($scan->pages->flatMap->issues->where('type', 'error')->take(10) as $issue)
            <div class="recommendation {{ in_array($issue->wcag_level, ['A']) ? 'high' : '' }}">
                <div class="title">
                    <span class="badge {{ $issue->type }}">{{ $issue->wcag_reference }}</span>
                    {{ $issue->message }}
                </div>
                <div class="fix">
                    <strong>Fix:</strong> {{ $issue->recommendation ?? 'Review the affected element and ensure it meets WCAG guidelines.' }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Detailed Issues List -->
    <div class="page-break"></div>
    
    <div class="section">
        <h2 class="section-title">All Issues Detail</h2>
        
        @foreach($scan->pages as $page)
        @if($page->issues->count() > 0)
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 12px; color: #4f46e5; margin-bottom: 8px;">
                {{ parse_url($page->url, PHP_URL_PATH) ?: '/' }}
            </h3>
            <table class="issues-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>WCAG</th>
                        <th>Issue</th>
                        <th>Recommendation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page->issues->sortBy(fn($i) => $i->type === 'error' ? 0 : 1) as $issue)
                    <tr>
                        <td><span class="badge {{ $issue->type }}">{{ $issue->type }}</span></td>
                        <td>{{ $issue->wcag_reference }}</td>
                        <td>{{ $issue->message }}</td>
                        <td>{{ Str::limit($issue->recommendation, 80) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @endforeach
    </div>

    <!-- Footer -->
    <div class="footer">
        @if($whiteLabel ?? false)
            <p>{{ $scan->url }}</p>
        @else
            <p>Generated by <strong>AccessScan</strong> — {{ $scan->url }}</p>
        @endif
        <p style="margin-top: 5px;">This report is for informational purposes only. Please review all issues for complete accessibility compliance.</p>
    </div>
</body>
</html>
