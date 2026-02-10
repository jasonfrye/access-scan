{{-- Test page: "Good" site - minor issues only (missing skip link, some contrast, decorative images not marked) --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GreenLeaf Organic Market - Fresh Produce Delivered</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; color: #333; }
        header { background: #2d6a4f; color: white; padding: 16px 32px; }
        nav a { color: #b7e4c7; margin-right: 16px; text-decoration: none; }
        nav a:hover { color: white; }
        main { max-width: 960px; margin: 0 auto; padding: 32px 16px; }
        h1 { font-size: 2rem; margin-bottom: 8px; }
        .hero { background: #f0fdf4; padding: 48px 32px; border-radius: 12px; margin-bottom: 32px; }
        .hero p { font-size: 1.1rem; color: #6b7280; max-width: 600px; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 48px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; }
        .card h3 { margin-top: 0; }
        .card p { color: #6b7280; font-size: 0.9rem; }
        .btn { display: inline-block; background: #2d6a4f; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; }
        .btn:hover { background: #1b4332; }
        footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 32px; text-align: center; color: #9ca3af; font-size: 0.85rem; }
        /* Intentional: light gray text on white - minor contrast issue */
        .subtle-text { color: #c0c0c0; }
    </style>
</head>
<body>
    {{-- Missing: skip-to-content link --}}
    <header>
        <nav>
            <a href="/">Home</a>
            <a href="/shop">Shop</a>
            <a href="/about">About Us</a>
            <a href="/contact">Contact</a>
        </nav>
    </header>

    <main>
        <div class="hero">
            <h1>Fresh Organic Produce, Delivered to Your Door</h1>
            <p>Supporting local farms while bringing you the freshest fruits, vegetables, and pantry staples. Order by noon for next-day delivery.</p>
            <br>
            <a href="/shop" class="btn">Browse Our Selection</a>
        </div>

        <h2>Why Choose GreenLeaf?</h2>
        <div class="cards">
            <div class="card">
                {{-- Intentional: decorative image without role="presentation" or empty alt --}}
                <img src="/images/leaf-icon.svg" alt="leaf icon image graphic">
                <h3>100% Organic</h3>
                <p>Every item in our store is certified organic. No pesticides, no GMOs, just pure food.</p>
            </div>
            <div class="card">
                <img src="/images/truck-icon.svg" alt="delivery truck icon image">
                <h3>Fast Delivery</h3>
                <p>Order by noon and receive your groceries the very next day, packed fresh.</p>
            </div>
            <div class="card">
                <img src="/images/farm-icon.svg" alt="farm icon">
                <h3>Local Farms</h3>
                <p>We partner with over 50 local farms within 100 miles of your location.</p>
            </div>
        </div>

        <h2>Customer Testimonials</h2>
        {{-- Intentional: blockquote without cite, not a WCAG issue but contributes to notice count --}}
        <blockquote>
            <p>"GreenLeaf changed how my family eats. The produce is amazing!"</p>
            <p class="subtle-text">- Sarah M., Portland</p>
        </blockquote>
        <blockquote>
            <p>"Best delivery service I've used. Always on time and super fresh."</p>
            <p class="subtle-text">- James K., Seattle</p>
        </blockquote>

        {{-- Intentional: form label exists but input has no autocomplete attribute --}}
        <h2>Stay Updated</h2>
        <form action="/subscribe" method="post">
            <label for="email">Email Address</label><br>
            <input type="email" id="email" name="email" placeholder="you@example.com" style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
            <button type="submit" class="btn" style="border: none; cursor: pointer;">Subscribe</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2026 GreenLeaf Organic Market. All rights reserved.</p>
        {{-- Intentional: link with vague text --}}
        <p><a href="/privacy">Click here</a> to read our privacy policy.</p>
    </footer>
</body>
</html>
