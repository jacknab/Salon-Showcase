<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/data/templates.php';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$id || !isset($all_templates[$id])) {
    header('Location: ' . BASE_PATH . '/');
    exit;
}

$t = $all_templates[$id];
$page_title = 'Get Started with ' . $t['name'];

$category_map = [
    'Hair Salon'  => 'hair-salons.php',
    'Barbershop'  => 'barbershops.php',
    'Nail Salon'  => 'nail-salons.php',
];
$back_url     = BASE_PATH . '/' . ($category_map[$t['category']] ?? '');
$preview_url  = BASE_PATH . '/preview.php?id=' . urlencode($id);

require_once __DIR__ . '/includes/header.php';
?>

<section class="select-page">
    <div class="container">
        <a href="<?php echo $back_url; ?>" class="select-back">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M10 3L5 8l5 5"/></svg>
            Back to designs
        </a>

        <div class="select-grid">

            <!-- Left: template summary card -->
            <div class="select-preview-col">
                <div class="select-template-card" style="--accent:<?php echo htmlspecialchars($t['accent']); ?>;--dark:<?php echo htmlspecialchars($t['dark']); ?>;">
                    <div class="select-template-thumb">
                        <div class="select-thumb-browser">
                            <span class="stb-dot stb-dot--r"></span>
                            <span class="stb-dot stb-dot--y"></span>
                            <span class="stb-dot stb-dot--g"></span>
                            <span class="stb-url">yourdomain.com</span>
                        </div>
                        <div class="select-thumb-hero" style="background:var(--dark);">
                            <div class="sth-eyebrow" style="background:var(--accent)"></div>
                            <div class="sth-h1"></div>
                            <div class="sth-h1 sth-h1--short"></div>
                            <div class="sth-sub"></div>
                            <div class="sth-btns">
                                <span class="sth-btn sth-btn--fill" style="background:var(--accent)"></span>
                                <span class="sth-btn sth-btn--ghost"></span>
                            </div>
                        </div>
                        <div class="select-thumb-sections">
                            <div class="sts-bar" style="background:var(--dark)"></div>
                            <div class="sts-bar sts-bar--light"></div>
                            <div class="sts-bar" style="background:var(--dark)"></div>
                        </div>
                    </div>
                    <div class="select-template-info">
                        <div class="select-template-meta">
                            <span class="select-template-category"><?php echo htmlspecialchars($t['category']); ?></span>
                            <span class="select-template-style"><?php echo htmlspecialchars($t['style']); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($t['name']); ?></h3>
                        <p><?php echo htmlspecialchars($t['desc']); ?></p>
                        <div class="select-features">
                            <?php foreach ($t['features'] as $f): ?>
                            <span class="feature-pill">✓ <?php echo htmlspecialchars($f); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?php echo $preview_url; ?>" class="select-preview-link">View full preview →</a>
                    </div>
                </div>

                <!-- What's included -->
                <div class="select-included">
                    <h4>What's included</h4>
                    <ul>
                        <li>✓ Fully designed, ready-to-launch website</li>
                        <li>✓ Mobile responsive on all devices</li>
                        <li>✓ SSL certificate &amp; fast hosting</li>
                        <li>✓ Your domain connected</li>
                        <li>✓ Optional text editing — change any words</li>
                        <li>✓ Support from the Certxa team</li>
                    </ul>
                </div>
            </div>

            <!-- Right: get started form -->
            <div class="select-form-col">
                <div class="select-form-card">
                    <div class="select-form-header">
                        <div class="select-step-badge">Step 1 of 2</div>
                        <h2>Get started with<br><span style="color:var(--color-orange)"><?php echo htmlspecialchars($t['name']); ?></span></h2>
                        <p>Tell us a bit about your business and the domain you'd like to use. We'll have your site live within 24 hours.</p>
                    </div>

                    <form class="select-form" action="https://certxa.com/launchit/start" method="POST">
                        <input type="hidden" name="template_id"   value="<?php echo htmlspecialchars($id); ?>">
                        <input type="hidden" name="template_name" value="<?php echo htmlspecialchars($t['name']); ?>">

                        <div class="form-group">
                            <label for="business_name">Your business name</label>
                            <input type="text" id="business_name" name="business_name" placeholder="e.g. Sophie's Salon" required autocomplete="organization">
                        </div>

                        <div class="form-group">
                            <label for="email">Your email address</label>
                            <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">
                        </div>

                        <div class="form-group">
                            <label>Domain name</label>
                            <div class="domain-choice-group">
                                <label class="domain-radio">
                                    <input type="radio" name="domain_type" value="own" id="domainOwn" checked>
                                    <span class="domain-radio__box">
                                        <span class="domain-radio__title">I have a domain</span>
                                        <span class="domain-radio__sub">e.g. mysalon.co.uk</span>
                                    </span>
                                </label>
                                <label class="domain-radio">
                                    <input type="radio" name="domain_type" value="new" id="domainNew">
                                    <span class="domain-radio__box">
                                        <span class="domain-radio__title">I need a domain</span>
                                        <span class="domain-radio__sub">We'll help you find one</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="domainInputGroup">
                            <label for="domain">Your domain name</label>
                            <input type="text" id="domain" name="domain" placeholder="mysalon.co.uk" autocomplete="url">
                            <span class="form-hint">We'll send you simple DNS instructions — takes about 5 minutes.</span>
                        </div>

                        <div class="form-group" id="domainSearchGroup" style="display:none;">
                            <label for="domain_search">What would you like your domain to be?</label>
                            <input type="text" id="domain_search" name="domain_search" placeholder="mysalon, sophieshair, urbancuts...">
                            <span class="form-hint">We'll search for available domains and suggest the best options.</span>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone number <span class="form-optional">(optional)</span></label>
                            <input type="tel" id="phone" name="phone" placeholder="+44 7700 000000" autocomplete="tel">
                        </div>

                        <button type="submit" class="btn btn--orange btn--lg select-submit">
                            Get My Website Live →
                        </button>
                        <p class="select-legal">No credit card required to start. By continuing you agree to Certxa's <a href="https://certxa.com/terms">Terms of Service</a>.</p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
(function () {
    var radios  = document.querySelectorAll('input[name="domain_type"]');
    var ownGrp  = document.getElementById('domainInputGroup');
    var newGrp  = document.getElementById('domainSearchGroup');

    radios.forEach(function (r) {
        r.addEventListener('change', function () {
            if (r.value === 'own') {
                ownGrp.style.display = '';
                newGrp.style.display = 'none';
            } else {
                ownGrp.style.display = 'none';
                newGrp.style.display = '';
            }
        });
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
