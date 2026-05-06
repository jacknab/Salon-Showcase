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
        'accent'   => '#b0bec5',
        'dark'     => '#0f0f1a',
        'light'    => '#1a1a2a',
        'url_slug' => 'chrome-nails',
    ],
    [
        'id'       => 'petal-studio',
        'name'     => 'Petal Studio',
        'style'    => 'Soft & Feminine',
        'desc'     => 'Soft blush tones, floral accents, and a warm, inviting design. Ideal for nail salons that want to project elegance and femininity.',
        'badge'    => 'premium',
        'features' => ['Gallery', 'Menu', 'Online Booking'],
        'accent'   => '#e8a0b4',
        'dark'     => '#1a0a10',
        'light'    => '#2a1020',
        'url_slug' => 'petal-studio',
    ],
    [
        'id'       => 'nail-bar-nyc',
        'name'     => 'Nail Bar NYC',
        'style'    => 'Urban',
        'desc'     => 'City-chic, fast-paced design made for busy urban nail bars. Clean service menus, quick booking, and a professional punch.',
        'badge'    => '',
        'features' => ['Services', 'Booking', 'Reviews'],
        'accent'   => '#3d7cd1',
        'dark'     => '#0a0a1a',
        'light'    => '#141424',
        'url_slug' => 'nail-bar-nyc',
    ],
    [
        'id'       => 'luxe-nails',
        'name'     => 'Luxe Nails',
        'style'    => 'Luxury',
        'desc'     => 'Opulent, dark-background luxury design for high-end nail studios. Gold accents, editorial photography, and a sense of exclusivity.',
        'badge'    => 'premium',
        'features' => ['Portfolio', 'VIP Booking', 'Gallery'],
        'accent'   => '#d4a853',
        'dark'     => '#120a08',
        'light'    => '#1e1008',
        'url_slug' => 'luxe-nails',
    ],
    [
        'id'       => 'pastel-pop',
        'name'     => 'Pastel Pop',
        'style'    => 'Playful',
        'desc'     => 'Fun, bold, and full of personality. Pastel gradients and playful typography for nail artists with a vibrant, expressive brand.',
        'badge'    => 'new',
        'features' => ['Gallery', 'Booking', 'Services'],
        'accent'   => '#b39ddb',
        'dark'     => '#0f0a18',
        'light'    => '#1a1228',
        'url_slug' => 'pastel-pop',
    ],
    [
        'id'       => 'zen-nails',
        'name'     => 'Zen Nails',
        'style'    => 'Minimal',
        'desc'     => 'Clean, minimal aesthetic with generous white space and quiet sophistication. For studios where the work speaks for itself.',
        'badge'    => 'new',
        'features' => ['Portfolio', 'Services', 'Map'],
        'accent'   => '#80cbc4',
        'dark'     => '#080e0e',
        'light'    => '#0f1818',
        'url_slug' => 'zen-nails',
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
                <div class="template-card__thumb">
                    <?php if (!empty($t['badge'])): ?>
                    <span class="template-card__badge badge--<?php echo htmlspecialchars($t['badge']); ?>">
                        <?php echo ucfirst($t['badge']); ?>
                    </span>
                    <?php endif; ?>
                    <img
                        src="<?php echo BASE_PATH; ?>/assets/img/thumbs/<?php echo urlencode($t['id']); ?>.jpg"
                        alt="<?php echo htmlspecialchars($t['name']); ?> template preview"
                        class="template-card__img"
                        loading="lazy"
                    >
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
        <h2>Ready to launch your nail salon website?</h2>
        <p>Start your free trial, pick a template, and go live with your own domain today.</p>
        <div class="cta-banner-actions">
            <a href="https://certxa.com/signup" class="btn btn--primary btn--lg">Start Free Trial</a>
            <a href="<?php echo BASE_PATH; ?>/" class="btn btn--ghost btn--lg">Browse All Templates</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
