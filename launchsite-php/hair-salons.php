<?php
$page_title = 'Hair Salon Templates';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/template-preview.php';

$templates = [
    [
        'id'       => 'luxe-atelier',
        'name'     => 'Luxe Atelier',
        'style'    => 'Luxury',
        'desc'     => 'A high-end editorial feel with large hero imagery, elegant serif typography, and a sleek booking flow — made for premium salons.',
        'badge'    => 'premium',
        'features' => ['Online Booking', 'Gallery', 'Services Menu'],
        'accent'   => '#d4a853',
        'dark'     => '#1a0a00',
        'light'    => '#2a1800',
        'url_slug' => 'luxe-atelier',
    ],
    [
        'id'       => 'studio-bloom',
        'name'     => 'Studio Bloom',
        'style'    => 'Modern',
        'desc'     => 'Fresh, airy design with soft colour accents. Spotlights your stylists and portfolio with a clean grid layout and smooth animations.',
        'badge'    => 'popular',
        'features' => ['Stylist Profiles', 'Portfolio', 'Reviews'],
        'accent'   => '#7ea58a',
        'dark'     => '#0a150f',
        'light'    => '#152015',
        'url_slug' => 'studio-bloom',
    ],
    [
        'id'       => 'the-salon-co',
        'name'     => 'The Salon Co.',
        'style'    => 'Classic',
        'desc'     => 'Timeless and professional. A structured layout that clearly communicates services, pricing, and appointment booking.',
        'badge'    => '',
        'features' => ['Pricing Tables', 'Booking', 'Map'],
        'accent'   => '#5b8ace',
        'dark'     => '#0a0f1a',
        'light'    => '#141c30',
        'url_slug' => 'the-salon-co',
    ],
    [
        'id'       => 'maison-beaute',
        'name'     => 'Maison Beauté',
        'style'    => 'Boutique',
        'desc'     => 'French-inspired boutique aesthetic with warm tones and flowing editorial sections. Perfect for colour specialists and balayage experts.',
        'badge'    => 'new',
        'features' => ['Before & After', 'Gallery', 'Online Booking'],
        'accent'   => '#c47a8a',
        'dark'     => '#1a0a0f',
        'light'    => '#2a101a',
        'url_slug' => 'maison-beaute',
    ],
    [
        'id'       => 'noir-studio',
        'name'     => 'Noir Studio',
        'style'    => 'Dark & Bold',
        'desc'     => 'A dramatic, dark-themed design with strong typography and spotlight photography. Designed for avant-garde stylists who want to stand out.',
        'badge'    => 'premium',
        'features' => ['Portfolio', 'Services', 'Booking'],
        'accent'   => '#e0e0e0',
        'dark'     => '#050505',
        'light'    => '#1a1a1a',
        'url_slug' => 'noir-studio',
    ],
    [
        'id'       => 'sage-collective',
        'name'     => 'Sage Collective',
        'style'    => 'Organic',
        'desc'     => 'Natural, earthy design for eco-conscious salons and organic hair care studios. Warm greens, flowing layouts, and sustainability messaging built in.',
        'badge'    => 'new',
        'features' => ['Services', 'Team', 'Booking'],
        'accent'   => '#6a8a5a',
        'dark'     => '#0a1500',
        'light'    => '#141f0a',
        'url_slug' => 'sage-collective',
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
                <div class="template-card__thumb">
                    <?php if (!empty($t['badge'])): ?>
                    <span class="template-card__badge badge--<?php echo htmlspecialchars($t['badge']); ?>">
                        <?php echo ucfirst($t['badge']); ?>
                    </span>
                    <?php endif; ?>
                    <?php render_template_preview($t); ?>
                    <div class="template-card__overlay">
                        <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--primary">Live Preview</a>
                        <a href="<?php echo BASE_PATH; ?>/select.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--orange">Use This Design</a>
                    </div>
                    <div class="preview-progress"></div>
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
