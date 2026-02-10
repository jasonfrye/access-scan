{{-- Test page: "Mediocre" site - several errors and warnings (missing alts, poor contrast, empty links, missing landmarks) --}}
<!DOCTYPE html>
<html>{{-- Intentional: missing lang attribute --}}
<head>
    <meta charset="utf-8">
    {{-- Intentional: missing viewport meta --}}
    <title>BrightPath Consulting</title>
    <style>
        body { font-family: Helvetica, sans-serif; margin: 0; background: #fff; }
        .header { background: #1a1a2e; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
        .header .logo { color: white; font-size: 1.5rem; font-weight: bold; }
        .header a { color: #555577; text-decoration: none; margin-left: 20px; } {{-- Intentional: poor contrast on dark bg --}}
        .banner { background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; padding: 80px 40px; text-align: center; }
        .banner h1 { font-size: 2.5rem; margin-bottom: 16px; }
        .banner p { font-size: 1.1rem; color: #7a7a9e; max-width: 600px; margin: 0 auto; } {{-- Intentional: low contrast --}}
        .services { padding: 60px 40px; max-width: 1000px; margin: 0 auto; }
        .services h2 { text-align: center; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 30px; }
        .grid-item { text-align: center; padding: 24px; }
        .grid-item img { width: 80px; height: 80px; margin-bottom: 16px; }
        .grid-item h3 { margin-bottom: 8px; }
        .grid-item p { color: #aaa; font-size: 0.9rem; }
        .cta { background: #0f3460; color: white; padding: 60px 40px; text-align: center; }
        .cta a { background: #e94560; color: white; padding: 14px 32px; text-decoration: none; border-radius: 6px; font-size: 1.1rem; }
        .team { padding: 60px 40px; max-width: 1000px; margin: 0 auto; }
        .team-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-top: 24px; }
        .member { text-align: center; }
        .member img { width: 120px; height: 120px; border-radius: 50%; }
        .member p { font-size: 0.85rem; color: #bbb; } {{-- Intentional: low contrast --}}
        .footer { background: #1a1a2e; color: #444466; padding: 30px 40px; text-align: center; font-size: 0.85rem; } {{-- Intentional: very low contrast --}}
        .footer a { color: #444466; }
    </style>
</head>
<body>
    {{-- Intentional: no landmark roles, div-soup navigation --}}
    <div class="header">
        <div class="logo">BrightPath</div>
        <div>
            <a href="/services">Services</a>
            <a href="/about">About</a>
            <a href="/team">Team</a>
            {{-- Intentional: empty link --}}
            <a href="/search"><img src="/icons/search.png"></a>{{-- Missing alt --}}
        </div>
    </div>

    <div class="banner">
        <h1>Transform Your Business with Expert Consulting</h1>
        <p>We help companies scale, optimize operations, and build high-performing teams through data-driven strategies.</p>
    </div>

    <div class="services">
        <h2>Our Services</h2>
        <div class="grid">
            <div class="grid-item">
                {{-- Intentional: missing alt text --}}
                <img src="/icons/strategy.png">
                <h3>Strategy</h3>
                <p>Market analysis, competitive positioning, and growth roadmaps tailored to your industry.</p>
            </div>
            <div class="grid-item">
                <img src="/icons/operations.png">{{-- Missing alt --}}
                <h3>Operations</h3>
                <p>Streamline workflows, reduce waste, and improve throughput across your organization.</p>
            </div>
            <div class="grid-item">
                <img src="/icons/talent.png">{{-- Missing alt --}}
                <h3>Talent</h3>
                <p>Recruit, develop, and retain top talent with our proven frameworks.</p>
            </div>
        </div>
    </div>

    <div class="cta">
        <h2>Ready to Get Started?</h2>
        <p style="margin-bottom: 24px; color: #8888aa;">Book a free 30-minute consultation with one of our senior advisors.</p>
        {{-- Intentional: link styled as button, no clear accessible name --}}
        <a href="/contact">Get in Touch &rarr;</a>
    </div>

    <div class="team">
        <h2>Meet the Team</h2>
        <div class="team-grid">
            <div class="member">
                {{-- Intentional: image without alt --}}
                <img src="/team/sarah.jpg">
                <strong>Sarah Chen</strong>
                <p>Managing Director</p>
            </div>
            <div class="member">
                <img src="/team/james.jpg">{{-- Missing alt --}}
                <strong>James Rivera</strong>
                <p>Head of Strategy</p>
            </div>
            <div class="member">
                <img src="/team/priya.jpg">{{-- Missing alt --}}
                <strong>Priya Patel</strong>
                <p>Operations Lead</p>
            </div>
            <div class="member">
                <img src="/team/marcus.jpg">{{-- Missing alt --}}
                <strong>Marcus Obi</strong>
                <p>Talent Director</p>
            </div>
        </div>
    </div>

    {{-- Intentional: footer with no landmark, extremely low contrast --}}
    <div class="footer">
        <p>&copy; 2026 BrightPath Consulting. All rights reserved.</p>
        <p>
            <a href="/privacy">Privacy</a> |
            <a href="/terms">Terms</a> |
            {{-- Intentional: link opens new window with no warning --}}
            <a href="https://linkedin.com/company/brightpath" target="_blank">LinkedIn</a>
        </p>
    </div>
</body>
</html>
