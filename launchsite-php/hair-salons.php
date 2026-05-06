<?php
$page_title = 'Hair Salon Templates';
require_once __DIR__ . '/config.php';

$templates = [
    [
        'id'       => 'luxe-atelier',
        'name'     => 'Luxe Atelier',
        'style'    => 'Luxury',
        'desc'     => 'A high-end editorial feel with large hero imagery, elegant serif typography, and a sleek booking flow — made for premium salons.',
        'badge'    => 'premium',
        'features' => ['Online Booking', 'Gallery', 'Services Menu'],
        'color'    => '#1a0f2e',
    ],
    [
        'id'       => 'studio-bloom',
        'name'     => 'Studio Bloom',
        'style'    => 'Modern',
        'desc'     => 'Fresh, airy design with soft colour accents. Spotlights your stylists and portfolio with a clean grid layout and smooth animations.',
        'badge'    => 'popular',
        'features' => ['Stylist Profiles', 'Portfolio', 'Reviews'],
        'color'    => '#0f1a2e',
    ],
    [
        'id'       => 'the-salon-co',
        'name'     => 'The Salon Co.',
        'style'    => 'Classic',
        'desc'     => 'Timeless and professional. A structured layout that clearly communicates services, pricing, and appointment booking.',
        'badge'    => '',
        'features' => ['Pricing Tables', 'Booking', 'Map'],
        'color'    => '#1a1a2e',
    ],
    [
        'id'       => 'maison-beaute',
        'name'     => 'Maison Beauté',
        'style'    => 'Boutique',
        'desc'     => 'French-inspired boutique aesthetic with warm tones and flowing editorial sections. Perfect for colour specialists and balayage experts.',
        'badge'    => 'new',
        'features' => ['Before & After', 'Gallery', 'Online Booking'],
        'color'    => '#2e1a0f',
    ],
    [
        'id'       => 'noir-studio',
        'name'     => 'Noir Studio',
        'style'    => 'Dark & Bold',
        'desc'     => 'A dramatic, dark-themed design with strong typography and spotlight photography. Designed for avant-garde stylists who want to stand out.',
        'badge'    => 'premium',
        'features' => ['Portfolio', 'Services', 'Booking'],
        'color'    => '#0a0a0a',
    ],
    [
        'id'       => 'sage-collective',
        'name'     => 'Sage Collective',
        'style'    => 'Organic',
        'desc'     => 'Natural, earthy design for eco-conscious salons and organic hair care studios. Warm greens, flowing layouts, and sustainability messaging built in.',
        'badge'    => 'new',
        'features' => ['Services', 'Team', 'Booking'],
        'color'    => '#0f1a0f',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="section-label">💇‍♀️ Hair Salons</div>
        <h1>Hair Salon <em>Templates</em></h1>
        <p>Elegant, conversion-focused designs built for hair salons of every style — from boutique studios to high-end ateliers.</p>
    </div>
</section>

<nav class="category-nav">
    <div class="category-nav-inner">
        <a href="<?php echo BASE_PATH; ?>/" class="category-tab">All Templates</a>
        <a href="<?php echo BASE_PATH; ?>/hair-salons.php" class="category-tab is-active">
            <span class="tab-icon">💇‍♀️</span> Hair Salons <span class="tab-count"><?php echo count($templates); ?></span>
        </a>
        <a href="<?php echo BASE_PATH; ?>/barbershops.php" class="category-tab">
            <span class="tab-icon">✂️</span> Barbershops
        </a>
        <a href="<?php echo BASE_PATH; ?>/nail-salons.php" class="category-tab">
            <span class="tab-icon">💅</span> Nail Salons
        </a>
    </div>
</nav>

<section class="catalog-section">
    <div class="container">
        <div class="catalog-header">
            <h2>Hair Salon Templates</h2>
            <span class="catalog-meta"><?php echo count($templates); ?> designs available</span>
        </div>
        <div class="template-grid">
            <?php foreach ($templates as $index => $t): ?>
            <div class="template-card" data-category="hair-salon" style="transition-delay: <?php echo $index * 60; ?>ms;">
                <div class="template-card__thumb" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($t['color']); ?>, #0b0d1a);">
                    <?php if (!empty($t['badge'])): ?>
                    <span class="template-card__badge badge--<?php echo htmlspecialchars($t['badge']); ?>">
                        <?php echo ucfirst($t['badge']); ?>
                    </span>
                    <?php endif; ?>
                    <div class="template-card__thumb-placeholder">
                        <div class="thumb-icon">💇‍♀️</div>
                        <span><?php echo htmlspecialchars($t['name']); ?></span>
                    </div>
                    <div class="template-card__overlay">
                        <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--primary">Live Preview</a>
                        <a href="<?php echo BASE_PATH; ?>/select.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--orange">Use This Design</a>
                    </div>
                </div>
                <div class="template-card__body">
                    <div class="template-card__meta">
                        <span class="template-card__category">Hair Salon</span>
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
        <h2>Ready to launch your salon website?</h2>
        <p>Start your free trial, pick a template, and go live with your own domain today.</p>
        <div class="cta-banner-actions">
            <a href="https://certxa.com/signup" class="btn btn--primary btn--lg">Start Free Trial</a>
            <a href="<?php echo BASE_PATH; ?>/" class="btn btn--ghost btn--lg">Browse All Templates</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
