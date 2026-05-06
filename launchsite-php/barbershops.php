<?php
$page_title = 'Barbershop Templates';
require_once __DIR__ . '/config.php';

$templates = [
    [
        'id'       => 'urban-blade',
        'name'     => 'Urban Blade',
        'style'    => 'Modern',
        'desc'     => 'A clean, modern layout built for the contemporary barbershop. Dark accents, bold typography, and a seamless booking experience.',
        'badge'    => 'popular',
        'features' => ['Online Booking', 'Services Menu', 'Gallery'],
        'color'    => '#0f1a1a',
    ],
    [
        'id'       => 'the-barbery',
        'name'     => 'The Barbery',
        'style'    => 'Classic',
        'desc'     => 'Old-school meets new-school. A rich, heritage-inspired design with barber pole motifs, warm photography, and a polished service menu.',
        'badge'    => 'premium',
        'features' => ['Barber Profiles', 'Pricing', 'Map'],
        'color'    => '#1a1000',
    ],
    [
        'id'       => 'midnight-cuts',
        'name'     => 'Midnight Cuts',
        'style'    => 'Dark & Bold',
        'desc'     => 'A dramatic, all-dark design for shops that want to project exclusivity and style. High contrast, strong imagery, minimal distractions.',
        'badge'    => 'premium',
        'features' => ['Portfolio', 'Booking', 'Reviews'],
        'color'    => '#050505',
    ],
    [
        'id'       => 'razor-sharp',
        'name'     => 'Razor Sharp',
        'style'    => 'Bold',
        'desc'     => 'Punchy, high-energy template with large hero sections and animated feature highlights. Built for shops with personality.',
        'badge'    => 'new',
        'features' => ['Services', 'Team', 'Booking'],
        'color'    => '#1a0a00',
    ],
    [
        'id'       => 'gentlemans-club',
        'name'     => "Gentleman's Club",
        'style'    => 'Luxury',
        'desc'     => 'An upscale, members-club aesthetic for premium barbershops. Deep tones, refined typography, and an experience that conveys prestige.',
        'badge'    => 'premium',
        'features' => ['Membership', 'Booking', 'Gallery'],
        'color'    => '#12080a',
    ],
    [
        'id'       => 'fresh-fades',
        'name'     => 'Fresh Fades',
        'style'    => 'Street',
        'desc'     => 'Urban, street-culture-inspired design for fade specialists and hip-hop influenced shops. Bright accents on dark backgrounds.',
        'badge'    => 'new',
        'features' => ['Gallery', 'Booking', 'Services'],
        'color'    => '#0a0a1a',
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
                <div class="template-card__thumb" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($t['color']); ?>, #0b0d1a);">
                    <?php if (!empty($t['badge'])): ?>
                    <span class="template-card__badge badge--<?php echo htmlspecialchars($t['badge']); ?>">
                        <?php echo ucfirst($t['badge']); ?>
                    </span>
                    <?php endif; ?>
                    <div class="template-card__thumb-placeholder">
                        <div class="thumb-icon">✂️</div>
                        <span><?php echo htmlspecialchars($t['name']); ?></span>
                    </div>
                    <div class="template-card__overlay">
                        <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--primary">Live Preview</a>
                        <a href="<?php echo BASE_PATH; ?>/select.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--orange">Use This Design</a>
                    </div>
                </div>
                <div class="template-card__body">
                    <div class="template-card__meta">
                        <span class="template-card__category">Barbershop</span>
                        <span class="template-card__style"><?php echo htmlspecialchars($t['style']); ?></span>
                    </div>
                    <h3 class="template-card__title"><?php echo htmlspecialchars($t['name']); ?></h3>
                    <p class="template-card__desc"><?php echo htmlspecialchars($t['desc']); ?></p>
                    <div class="template-card__footer">
                        <div class="template-card__features">
                            <?php foreach ($t['features'] as $feature): ?>
                            <span class="feature-pill"><?php echo htmlspecialchars($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($t['id']); ?>" class="template-card__cta">Preview</a>
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
