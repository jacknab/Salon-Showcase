<?php
$page_title = 'Template Catalog';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-badge">
            <span>✦</span> Website Builder + Hosting
        </div>
        <h1>Premium websites for your salon,<br><em>live in minutes</em></h1>
        <p class="hero-sub">
            Browse our hand-crafted website templates designed specifically for hair salons, barbershops, and nail studios. Pick a design, customize your content, go live today.
        </p>
        <div class="hero-actions">
            <a href="#categories" class="btn btn--orange btn--lg">Browse Templates</a>
            <a href="https://certxa.com/launchsite.php" class="btn btn--ghost btn--lg">See How It Works</a>
        </div>
        <p class="hero-stat">
            <span>39+ templates available</span> — new designs added every month
        </p>
    </div>
</section>

<!-- TRUST BAR -->
<div class="trust-bar">
    <div class="container">
        <p>Trusted by leading salons and beauty brands</p>
        <div class="trust-logos">
            <span>Sophie's Salon</span>
            <span>Blade &amp; Co</span>
            <span>Nail Atelier</span>
            <span>The Hair Studio</span>
            <span>Glamour Bar</span>
            <span>Urban Cuts</span>
        </div>
    </div>
</div>

<!-- CATEGORY CARDS -->
<section class="categories-section" id="categories">
    <div class="container">
        <div class="section-header">
            <div class="section-label">✦ Browse by Category</div>
            <h2>Find the perfect template<br>for your business</h2>
            <p>Every template is mobile-responsive, fully editable, and ready to launch with your domain.</p>
        </div>

        <div class="categories-grid">

            <!-- Hair Salons -->
            <a href="<?php echo BASE_PATH; ?>/hair-salons.php" class="category-card">
                <div class="category-card__image">
                    💇‍♀️
                </div>
                <div class="category-card__body">
                    <div class="category-card__label">Category</div>
                    <h2 class="category-card__title">Hair Salons</h2>
                    <p class="category-card__desc">
                        Elegant, conversion-focused templates for hair salons. Showcase your services, stylists, and booking flow with stunning visual layouts.
                    </p>
                    <div class="category-card__footer">
                        <span class="category-card__count">12 templates available</span>
                        <div class="category-card__arrow">→</div>
                    </div>
                </div>
            </a>

            <!-- Barbershops -->
            <a href="<?php echo BASE_PATH; ?>/barbershops.php" class="category-card">
                <div class="category-card__image">
                    ✂️
                </div>
                <div class="category-card__body">
                    <div class="category-card__label">Category</div>
                    <h2 class="category-card__title">Barbershops</h2>
                    <p class="category-card__desc">
                        Bold, masculine designs built for modern barbershops. From classic to contemporary — find a look that matches your shop's identity.
                    </p>
                    <div class="category-card__footer">
                        <span class="category-card__count">10 templates available</span>
                        <div class="category-card__arrow">→</div>
                    </div>
                </div>
            </a>

            <!-- Nail Salons -->
            <a href="<?php echo BASE_PATH; ?>/nail-salons.php" class="category-card">
                <div class="category-card__image">
                    💅
                </div>
                <div class="category-card__body">
                    <div class="category-card__label">Category</div>
                    <h2 class="category-card__title">Nail Salons</h2>
                    <p class="category-card__desc">
                        Chic, vibrant templates for nail salons and nail artists. Beautifully display your nail art portfolio, services menu, and online booking.
                    </p>
                    <div class="category-card__footer">
                        <span class="category-card__count">9 templates available</span>
                        <div class="category-card__arrow">→</div>
                    </div>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- CTA BANNER -->
<section class="container">
    <div class="cta-banner">
        <h2>Not sure which template to pick?</h2>
        <p>Start your free trial and explore all templates inside the builder. No credit card required.</p>
        <div class="cta-banner-actions">
            <a href="https://certxa.com/signup" class="btn btn--primary btn--lg">Start Free Trial</a>
            <a href="https://certxa.com/pricing" class="btn btn--ghost btn--lg">View Pricing</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
