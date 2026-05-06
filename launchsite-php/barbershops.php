<?php
$page_title = 'Barbershop Templates';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/template-preview.php';

$templates = [
    [
        'id'       => 'urban-blade',
        'name'     => 'Urban Blade',
        'style'    => 'Modern',
        'desc'     => 'A clean, modern layout built for the contemporary barbershop. Dark accents, bold typography, and a seamless booking experience.',
        'badge'    => 'popular',
        'features' => ['Online Booking', 'Services Menu', 'Gallery'],
        'accent'   => '#00bcd4',
        'dark'     => '#081515',
        'light'    => '#0f2020',
        'url_slug' => 'urban-blade',
    ],
    [
        'id'       => 'the-barbery',
        'name'     => 'The Barbery',
        'style'    => 'Classic',
        'desc'     => 'Old-school meets new-school. A rich, heritage-inspired design with barber pole motifs, warm photography, and a polished service menu.',
        'badge'    => 'premium',
        'features' => ['Barber Profiles', 'Pricing', 'Map'],
        'accent'   => '#c8a86b',
        'dark'     => '#1a1000',
        'light'    => '#2a1c08',
        'url_slug' => 'the-barbery',
    ],
    [
        'id'       => 'midnight-cuts',
        'name'     => 'Midnight Cuts',
        'style'    => 'Dark & Bold',
        'desc'     => 'A dramatic, all-dark design for shops that want to project exclusivity and style. High contrast, strong imagery, minimal distractions.',
        'badge'    => 'premium',
        'features' => ['Portfolio', 'Booking', 'Reviews'],
        'accent'   => '#e53935',
        'dark'     => '#050505',
        'light'    => '#150a0a',
        'url_slug' => 'midnight-cuts',
    ],
    [
        'id'       => 'razor-sharp',
        'name'     => 'Razor Sharp',
        'style'    => 'Bold',
        'desc'     => 'Punchy, high-energy template with large hero sections and animated feature highlights. Built for shops with personality.',
        'badge'    => 'new',
        'features' => ['Services', 'Team', 'Booking'],
        'accent'   => '#ff6d00',
        'dark'     => '#1a0a00',
        'light'    => '#281000',
        'url_slug' => 'razor-sharp',
    ],
    [
        'id'       => 'gentlemans-club',
        'name'     => "Gentleman's Club",
        'style'    => 'Luxury',
        'desc'     => 'An upscale, members-club aesthetic for premium barbershops. Deep tones, refined typography, and an experience that conveys prestige.',
        'badge'    => 'premium',
        'features' => ['Membership', 'Booking', 'Gallery'],
        'accent'   => '#c9a227',
        'dark'     => '#0d0608',
        'light'    => '#1a0d10',
        'url_slug' => 'gentlemans-club',
    ],
    [
        'id'       => 'fresh-fades',
        'name'     => 'Fresh Fades',
        'style'    => 'Street',
        'desc'     => 'Urban, street-culture-inspired design for fade specialists and hip-hop influenced shops. Bright accents on dark backgrounds.',
        'badge'    => 'new',
        'features' => ['Gallery', 'Booking', 'Services'],
        'accent'   => '#ffd600',
        'dark'     => '#0a0a14',
        'light'    => '#151520',
        'url_slug' => 'fresh-fades',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="section-label">✂️ Barbershops</div>
        <h1>Barbershop <em>Templates</em></h1>
        <p>Bold, masculine, and built to convert. Find the right look for your shop — from classic heritage to modern street culture.</p>
    </div>
</section>

<nav class="category-nav">
    <div class="category-nav-inner">
        <a href="<?php echo BASE_PATH; ?>/" class="category-tab">All Templates</a>
        <a href="<?php echo BASE_PATH; ?>/hair-salons.php" class="category-tab">
            <span class="tab-icon">💇‍♀️</span> Hair Salons
        </a>
        <a href="<?php echo BASE_PATH; ?>/barbershops.php" class="category-tab is-active">
            <span class="tab-icon">✂️</span> Barbershops <span class="tab-count"><?php echo count($templates); ?></span>
        </a>
        <a href="<?php echo BASE_PATH; ?>/nail-salons.php" class="category-tab">
            <span class="tab-icon">💅</span> Nail Salons
        </a>
    </div>
</nav>

<section class="catalog-section">
    <div class="container">
        <div class="catalog-header">
            <h2>Barbershop Templates</h2>
            <span class="catalog-meta"><?php echo count($templates); ?> designs available</span>
        </div>
        <div class="template-grid">
            <?php foreach ($templates as $index => $t): ?>
            <div class="template-card" data-category="barbershop" style="transition-delay: <?php echo $index * 60; ?>ms;">
                <div class="template-card__thumb">
                    <?php if (!empty($t['badge'])): ?>
                    <span class="template-card__badge badge--<?php echo htmlspecialchars($t['badge']); ?>">
                        <?php echo ucfirst($t['badge']); ?>
                    </span>
                    <?php endif; ?>
                    <?php render_template_preview($t); ?>
                    <div class="preview-progress"></div>
                </div>
                <div class="template-card__body">
                    <h3 class="template-card__title"><?php echo htmlspecialchars($t['name']); ?></h3>
                    <div class="template-card__actions">
                        <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($t['id']); ?>" class="tc-btn tc-btn--preview">Preview</a>
                        <a href="<?php echo BASE_PATH; ?>/select.php?id=<?php echo urlencode($t['id']); ?>" class="tc-btn tc-btn--start">Start</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="container">
    <div class="cta-banner">
        <h2>Ready to put your barbershop online?</h2>
        <p>Start your free trial, pick a template, and go live with your own domain today.</p>
        <div class="cta-banner-actions">
            <a href="https://certxa.com/signup" class="btn btn--primary btn--lg">Start Free Trial</a>
            <a href="<?php echo BASE_PATH; ?>/" class="btn btn--ghost btn--lg">Browse All Templates</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
