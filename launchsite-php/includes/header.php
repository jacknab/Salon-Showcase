<?php
require_once dirname(__DIR__) . '/config.php';
$current_page = basename($_SERVER['PHP_SELF']);
$is_preview   = ($current_page === 'preview.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — Launchit by Certxa' : 'Launchit by Certxa'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <?php if ($is_preview): ?>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/preview.css">
    <?php endif; ?>
</head>
<body>
<?php if (!$is_preview): ?>
<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <a href="https://certxa.com" class="logo" target="_blank">
                <span class="logo-text">Certxa<span class="logo-dot">.</span></span>
            </a>
            <div class="nav-links">
                <a href="https://certxa.com/salonos" class="nav-link" target="_blank">SalonOS</a>
                <a href="https://certxa.com/launchit" class="nav-link nav-link--active" target="_blank">Launchit</a>
                <a href="https://certxa.com/#how-it-works" class="nav-link" target="_blank">How It Works</a>
                <a href="https://certxa.com/pricing" class="nav-link" target="_blank">Pricing</a>
            </div>
            <div class="nav-actions">
                <a href="https://certxa.com/login" class="btn btn--ghost" target="_blank">Log In</a>
                <a href="https://certxa.com/signup" class="btn btn--primary" target="_blank">Get Started</a>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="https://certxa.com/salonos" class="mobile-nav-link" target="_blank">SalonOS</a>
            <a href="https://certxa.com/launchit" class="mobile-nav-link" target="_blank">Launchit</a>
            <a href="https://certxa.com/#how-it-works" class="mobile-nav-link" target="_blank">How It Works</a>
            <a href="https://certxa.com/pricing" class="mobile-nav-link" target="_blank">Pricing</a>
            <a href="https://certxa.com/login" class="mobile-nav-link" target="_blank">Log In</a>
            <a href="https://certxa.com/signup" class="btn btn--primary" style="margin-top:1rem;display:inline-block;" target="_blank">Get Started</a>
        </div>
    </div>
</header>
<?php endif; ?>
