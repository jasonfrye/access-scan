{{-- Test page: "Poor" site - many errors (no lang, no title, missing alts, empty links/buttons, no labels, autoplay, tables without headers, skipped headings, color-only indicators) --}}
<!DOCTYPE html>
<html>{{-- Intentional: missing lang --}}
<head>
    <meta charset="utf-8">
    {{-- Intentional: missing title tag --}}
    {{-- Intentional: missing viewport --}}
    <style>
        body { font-family: sans-serif; margin: 0; }
        .top-bar { background: #000; color: #222; padding: 10px 20px; font-size: 0.8rem; } {{-- Intentional: black on near-black --}}
        .nav { background: #1a1a1a; padding: 16px 20px; display: flex; justify-content: space-between; }
        .nav a { color: #333; text-decoration: none; margin-right: 12px; font-size: 0.95rem; } {{-- Intentional: terrible contrast --}}
        .hero { position: relative; }
        .hero img { width: 100%; height: 400px; object-fit: cover; } {{-- Missing alt --}}
        .hero-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; flex-direction: column; color: white; }
        .hero-overlay h1 { font-size: 3rem; margin-bottom: 8px; }
        .hero-overlay p { color: #999; } {{-- Low contrast on dark overlay --}}
        .content { max-width: 1000px; margin: 0 auto; padding: 40px 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        td, th { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .form-section { background: #f5f5f5; padding: 40px; margin: 40px 0; }
        .status-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; }
        .status-green { background: #22c55e; }
        .status-red { background: #ef4444; }
        .status-yellow { background: #eab308; }
        .footer { background: #111; color: #333; padding: 40px 20px; } {{-- Intentional: worst contrast --}}
        .footer a { color: #222; }
        .marquee { background: #ff0; color: #ff6600; padding: 8px; font-weight: bold; overflow: hidden; }
        .blink { animation: blinker 0.5s linear infinite; } {{-- Intentional: blinking content --}}
        @keyframes blinker { 50% { opacity: 0; } }
        .tiny { font-size: 9px; color: #ccc; } {{-- Too small + low contrast --}}
    </style>
</head>
<body>
    {{-- Intentional: no skip link, no landmarks anywhere --}}

    <div class="top-bar">
        Free shipping on orders over $50 | <a href="/sale" style="color: #333;">Shop Sale</a>
    </div>

    <div class="nav">
        {{-- Intentional: logo is image without alt, wrapped in link with no text --}}
        <a href="/"><img src="/logo.png" width="140"></a>
        <div>
            <a href="/products">Products</a>
            <a href="/deals">Deals</a>
            <a href="/reviews">Reviews</a>
            {{-- Intentional: empty links --}}
            <a href="/cart"><img src="/icons/cart.png"></a>
            <a href="/account"><img src="/icons/user.png"></a>
            {{-- Intentional: icon button with no accessible name --}}
            <a href="#" onclick="openMenu()"><img src="/icons/menu.png"></a>
        </div>
    </div>

    <div class="hero">
        {{-- Intentional: missing alt on large hero image --}}
        <img src="/banners/summer-sale.jpg">
        <div class="hero-overlay">
            {{-- Intentional: skipped heading level (h1 -> h4) --}}
            <h4>Summer Clearance</h4>
            <h1>Up to 70% Off Everything</h1>
            <p>Limited time only. While supplies last.</p>
            {{-- Intentional: button with no accessible name, uses only an icon --}}
            <button onclick="window.location='/shop'" style="background: #e11d48; border: none; color: white; padding: 14px 28px; cursor: pointer; font-size: 1rem; border-radius: 6px; margin-top: 16px;">
                <img src="/icons/arrow-right.png" width="20">
            </button>
        </div>
    </div>

    {{-- Intentional: marquee-like scrolling text --}}
    <div class="marquee">
        &#x26A0; FLASH SALE: Use code SUMMER70 for an extra 10% off! Limited time! Act now! Don't miss out! &#x26A0;
    </div>

    <div class="content">
        {{-- Intentional: heading levels skip from h1 (above) to h3 --}}
        <h3>Top Selling Products</h3>

        {{-- Intentional: table without proper th/scope, used for layout --}}
        <table>
            <tr>
                <td><b>Product</b></td>
                <td><b>Price</b></td>
                <td><b>Status</b></td>
                <td><b>Rating</b></td>
            </tr>
            <tr>
                <td>
                    {{-- Intentional: image without alt --}}
                    <img src="/products/headphones.jpg" width="50"> Wireless Headphones
                </td>
                <td>$49.99 <span style="text-decoration: line-through; color: #ccc;">$129.99</span></td>
                <td>
                    {{-- Intentional: color-only status indicator, no text alternative --}}
                    <span class="status-dot status-green"></span>
                </td>
                <td>&#9733;&#9733;&#9733;&#9733;&#9734;</td>
            </tr>
            <tr>
                <td>
                    <img src="/products/keyboard.jpg" width="50"> Mechanical Keyboard
                </td>
                <td>$79.99 <span style="text-decoration: line-through; color: #ccc;">$149.99</span></td>
                <td>
                    <span class="status-dot status-yellow"></span>
                </td>
                <td>&#9733;&#9733;&#9733;&#9733;&#9733;</td>
            </tr>
            <tr>
                <td>
                    <img src="/products/monitor.jpg" width="50"> 4K Monitor
                </td>
                <td>$299.99</td>
                <td>
                    <span class="status-dot status-red"></span>
                </td>
                <td>&#9733;&#9733;&#9733;&#9734;&#9734;</td>
            </tr>
        </table>

        {{-- Intentional: blinking text --}}
        <p class="blink" style="color: red; font-weight: bold;">&#128293; HURRY! Only 3 left in stock! &#128293;</p>

        {{-- Intentional: autoplaying video --}}
        <h3>Featured Review</h3>
        <video autoplay loop style="width: 100%; max-width: 600px;">
            <source src="/videos/review.mp4" type="video/mp4">
        </video>

        {{-- Intentional: form with no labels, placeholders only --}}
        <div class="form-section">
            <h3>Sign Up for Deals</h3>
            <p>Get exclusive offers straight to your inbox.</p>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                {{-- Intentional: no labels, no autocomplete, placeholder only --}}
                <input type="text" placeholder="First Name" style="padding: 10px; border: 1px solid #ccc; flex: 1;">
                <input type="text" placeholder="Last Name" style="padding: 10px; border: 1px solid #ccc; flex: 1;">
                <input type="text" placeholder="Email" style="padding: 10px; border: 1px solid #ccc; flex: 1;">
                {{-- Intentional: button with no type attribute --}}
                <button style="background: #e11d48; color: white; border: none; padding: 10px 24px; cursor: pointer;">GO</button>
            </div>
            <p class="tiny">By signing up you agree to our <a href="/terms" style="color: #ccc;">terms</a> and <a href="/privacy" style="color: #ccc;">privacy policy</a>.</p>
        </div>

        {{-- Intentional: using tabindex > 0 --}}
        <h3>Customer Reviews</h3>
        <div tabindex="5" style="padding: 16px; border: 1px solid #eee; margin-bottom: 12px;">
            <p><b>Mike T.</b> - "Great products, fast shipping!"</p>
        </div>
        <div tabindex="3" style="padding: 16px; border: 1px solid #eee; margin-bottom: 12px;">
            <p><b>Lisa R.</b> - "Love the quality. Will order again."</p>
        </div>

        {{-- Intentional: onclick on non-interactive element --}}
        <div onclick="window.location='/deals'" style="background: #fef2f2; padding: 24px; border-radius: 8px; cursor: pointer; text-align: center; margin-top: 24px;">
            <h3 style="color: #e11d48;">Don't Miss Our Daily Deals!</h3>
            <p>New items added every morning at 9 AM.</p>
        </div>
    </div>

    <div class="footer">
        <div style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <b style="color: #444;">QuickShop</b>
                <p>Your one-stop shop for electronics, accessories, and more.</p>
            </div>
            <div>
                <b style="color: #444;">Links</b>
                <p>
                    <a href="/about">About</a><br>
                    <a href="/careers">Careers</a><br>
                    <a href="/press">Press</a><br>
                    {{-- Intentional: javascript:void link --}}
                    <a href="javascript:void(0)" onclick="openChat()">Live Chat</a>
                </p>
            </div>
            <div>
                <b style="color: #444;">Social</b>
                <p>
                    {{-- Intentional: icon links with no text --}}
                    <a href="https://facebook.com" target="_blank"><img src="/icons/fb.png"></a>
                    <a href="https://twitter.com" target="_blank"><img src="/icons/tw.png"></a>
                    <a href="https://instagram.com" target="_blank"><img src="/icons/ig.png"></a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
