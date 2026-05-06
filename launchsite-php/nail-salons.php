<?php
$page_title = 'Nail Salon Templates';
require_once __DIR__ . '/config.php';

$templates = [
    [
        'id'       => 'chrome-nails',
        'name'     => 'Chrome & Co.',
        'style'    => 'Modern Glam',
        'desc'     => 'High-shine, chrome-inspired aesthetic. A bold layout with a portfolio-first approach — perfect for nail artists who lead with their work.',
        'badge'    => 'popular',
        'features' => ['Portfolio', 'Booking', 'Services'],
        'color'    => '#1a1a2a',
    ],
    [
        'id'       => 'petal-studio',
        'name'     => 'Petal Studio',
        'style'    => 'Soft & Feminine',
        'desc'     => 'Soft blush tones, floral accents, and a warm, inviting design. Ideal for nail salons that want to project elegance and femininity.',
        'badge'    => 'premium',
        'features' => ['Gallery', 'Menu', 'Online Booking'],
        'color'    => '#1a0f14',
    ],
    [
        'id'       => 'nail-bar-nyc',
        'name'     => 'Nail Bar NYC',
        'style'    => 'Urban',
        'desc'     => 'City-chic, fast-paced design made for busy urban nail bars. Clean service menus, quick booking, and a professional punch.',
        'badge'    => '',
        'features' => ['Services', 'Booking', 'Reviews'],
        'color'    => '#0f0f1f',
    ],
    [
        'id'       => 'luxe-nails',
        'name'     => 'Luxe Nails',
        'style'    => 'Luxury',
        'desc'     => 'Opulent, dark-background luxury design for high-end nail studios. Gold accents, editorial photography, and a sense of exclusivity.',
        'badge'    => 'premium',
        'features' => ['Portfolio', 'VIP Booking', 'Gallery'],
        'color'    => '#12080a',
    ],
    [
        'id'       => 'pastel-pop',
        'name'     => 'Pastel Pop',
        'style'    => 'Playful',
        'desc'     => 'Fun, bold, and full of personality. Pastel gradients and playful typography for nail artists with a vibrant, expressive brand.',
        'badge'    => 'new',
        'features' => ['Gallery', 'Booking', 'Services'],
        'color'    => '#0f0a1a',
    ],
    [
        'id'       => 'zen-nails',
        'name'     => 'Zen Nails',
        'style'    => 'Minimal',
        'desc'     => 'Clean, minimal aesthetic with generous white space and quiet sophistication. For studios where the work speaks for itself.',
        'badge'    => 'new',
        'features' => ['Portfolio', 'Services', 'Map'],
        'color'    => '#0a0f0f',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="section-label">💅 Nail Salons</div>
        <h1>Nail Salon <em>Templates</em></h1>
        <p>Chic, vibrant designs built for nail salons and nail artists. Show off your portfolio, services, and booking link in style.</p>
    </div>
</section>

<nav class="category-nav">
    <div class="category-nav-inner">
        <a href="<?php echo BASE_PATH; ?>/" class="category-tab">All Templates</a>
        <a href="<?php echo BASE_PATH; ?>/hair-salons.php" class="category-tab">
            <span class="tab-icon">💇‍♀️</span> Hair Salons
        </a>
        <a href="<?php echo BASE_PATH; ?>/barbershops.php" class="category-tab">
            <span class="tab-icon">✂️</span> Barbershops
        </a>
        <a href="<?php echo BASE_PATH; ?>/nail-salons.php" class="category-tab is-active">
            <span class="tab-icon">💅</span> Nail Salons <span class="tab-count"><?php echo count($templates); ?></span>
        </a>
    </div>
</nav>

<section class="catalog-section">
    <div class="container">
        <div class="catalog-header">
            <h2>Nail Salon Templates</h2>
            <span class="catalog-meta"><?php echo count($templates); ?> designs available</span>
        </div>
        <div class="template-grid">
            <?php foreach ($templates as $index => $t): ?>
            <div class="template-card" data-category="nail-salon" style="transition-delay: <?php echo $index * 60; ?>ms;">
                <div class="template-card__thumb" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($t['color']); ?>, #0b0d1a);">
                    <?php if (!empty($t['badge'])): ?>
                    <span class="template-card__badge badge--<?php echo htmlspecialchars($t['badge']); ?>">
                        <?php echo ucfirst($t['badge']); ?>
                    </span>
                    <?php endif; ?>
                    <div class="template-card__thumb-placeholder">
                        <div class="thumb-icon">💅</div>
                        <span><?php echo htmlspecialchars($t['name']); ?></span>
                    </div>
                    <div class="template-card__overlay">
                        <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--primary">Live Preview</a>
                        <a href="<?php echo BASE_PATH; ?>/select.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn--orange">Use This Design</a>
                    </div>
                </div>
                <div class="template-card__body">
                    <div class="template-card__meta">
                        <span class="template-card__category">Nail Salon</span>
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
        <h2>Ready to launch your nail salon website?</h2>
        <p>Start your free trial, pick a template, and go live with your own domain today.</p>
        <div class="cta-banner-actions">
            <a href="https://certxa.com/signup" class="btn btn--primary btn--lg">Start Free Trial</a>
            <a href="<?php echo BASE_PATH; ?>/" class="btn btn--ghost btn--lg">Browse All Templates</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
