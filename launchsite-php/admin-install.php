<?php
session_start();
require_once __DIR__ . '/config.php';

define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'launchit-admin');

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . BASE_PATH . '/admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/admin.php');
    exit;
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function step(string $icon, string $msg): void {
    echo "<li class='result-step'><span class='result-step__icon'>$icon</span><span>$msg</span></li>\n";
    ob_flush(); flush();
}

function step_log(string $icon, string $msg, string $log): void {
    $safe = htmlspecialchars(trim($log));
    echo "<li class='result-step'><span class='result-step__icon'>$icon</span>"
       . "<span>$msg<div class='log-block'>$safe</div></span></li>\n";
    ob_flush(); flush();
}

function abort(string $msg): void {
    echo "</ul></div>"
       . "<div class='result-box result-box--error'><div class='result-box__title'>Installation failed</div>"
       . "<p style='color:rgba(255,255,255,0.6);font-size:0.875rem;'>$msg</p></div>"
       . "<div class='result-actions'>"
       . "<a href='" . BASE_PATH . "/admin.php#upload' class='btn-admin btn-admin--ghost'>← Back to Admin</a>"
       . "</div></div></body></html>";
    exit;
}

function run(string $cmd, string $cwd = ''): array {
    $full = $cwd ? "cd " . escapeshellarg($cwd) . " && $cmd 2>&1" : "$cmd 2>&1";
    exec($full, $out, $code);
    return ['output' => implode("\n", $out), 'code' => $code];
}

function hex2rgb_local(string $hex): array {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
}
function alloc_c($img, string $hex, int $alpha = 0): int {
    [$r,$g,$b] = hex2rgb_local($hex);
    return imagecolorallocatealpha($img, $r, $g, $b, $alpha);
}
function rounded_rect_local($img, int $x1, int $y1, int $x2, int $y2, int $r, int $color): void {
    if ($r < 1) $r = 1;
    imagefilledrectangle($img, $x1+$r, $y1, $x2-$r, $y2, $color);
    imagefilledrectangle($img, $x1, $y1+$r, $x2, $y2-$r, $color);
    imagefilledellipse($img, $x1+$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x1+$r, $y2-$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y2-$r, $r*2, $r*2, $color);
}

function generate_thumbnail(
    string $id, string $name, string $accent, string $dark, string $light,
    string $tagline, string $out_dir
): bool {
    if (!function_exists('imagecreatetruecolor')) return false;
    $font_bold    = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
    $font_regular = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
    if (!file_exists($font_bold)) return false;

    $W = 900; $H = 620;
    $img = imagecreatetruecolor($W, $H);
    imagesavealpha($img, true);

    [$dr,$dg,$db] = hex2rgb_local($dark);
    [$lr,$lg,$lb] = hex2rgb_local($light);
    [$ar,$ag,$ab] = hex2rgb_local($accent);

    // Gradient background
    for ($y = 0; $y < $H; $y++) {
        $ratio = $y / $H;
        $r = (int)($dr + ($lr-$dr)*$ratio*0.7);
        $g = (int)($dg + ($lg-$dg)*$ratio*0.7);
        $b = (int)($db + ($lb-$db)*$ratio*0.7);
        $c = imagecolorallocate($img, $r, $g, $b);
        imagefilledrectangle($img, 0, $y, $W, $y, $c);
    }

    $white   = imagecolorallocate($img, 255, 255, 255);
    $acc_c   = imagecolorallocate($img, $ar, $ag, $ab);

    // Hero overlay glow
    for ($rad = 280; $rad >= 20; $rad -= 22) {
        $alpha = (int)(120 - ($rad/280)*118);
        $glow  = imagecolorallocatealpha($img, $ar, $ag, $ab, $alpha);
        imagefilledellipse($img, (int)($W*0.5), 200, $rad*2, (int)($rad*1.2), $glow);
    }

    // Nav bar
    $nav_bg = imagecolorallocatealpha($img, $dr, $dg, $db, 30);
    imagefilledrectangle($img, 0, 0, $W, 52, $nav_bg);
    $sep = imagecolorallocatealpha($img, 255,255,255, 115);
    imagefilledrectangle($img, 0, 52, $W, 53, $sep);

    // Logo pill
    rounded_rect_local($img, 26, 14, 26+160, 40, 6, alloc_c($img, $accent, 90));
    imagettftext($img, 10, 0, 35, 32, $white, $font_bold, $name);

    // Nav links (mock)
    $link_c = imagecolorallocatealpha($img, 255,255,255, 100);
    foreach ([240, 295, 348, 398] as $lx) {
        rounded_rect_local($img, $lx, 22, $lx+38, 30, 2, $link_c);
    }
    // CTA button
    rounded_rect_local($img, $W-110, 14, $W-26, 40, 13, $acc_c);
    imagettftext($img, 8, 0, $W-100, 31, $white, $font_bold, 'Book Now');

    // Hero text
    $ey = 100;
    // Eyebrow
    $eyebrow_c = imagecolorallocatealpha($img, min(255,$ar+80), $ag, $ab, 50);
    imagettftext($img, 9, 0, (int)(($W/2)-44), $ey+42, $eyebrow_c, $font_bold, 'WELCOME TO');

    // Title (wrap)
    $words = explode(' ', $tagline ?: $name);
    $lines = []; $line = '';
    foreach ($words as $word) {
        $test = $line ? "$line $word" : $word;
        $box  = imagettfbbox(34, 0, $font_bold, $test);
        if (abs($box[2]-$box[0]) > 540 && $line) { $lines[] = $line; $line = $word; }
        else $line = $test;
    }
    if ($line) $lines[] = $line;

    $ty = $ey + 84;
    foreach (array_slice($lines, 0, 2) as $l) {
        $box = imagettfbbox(34, 0, $font_bold, $l);
        $lw  = abs($box[2]-$box[0]);
        imagettftext($img, 34, 0, (int)(($W-$lw)/2), $ty, $white, $font_bold, $l);
        $ty += 46;
    }

    // CTA buttons
    $ty += 28;
    $bx = (int)(($W-310)/2);
    rounded_rect_local($img, $bx, $ty, $bx+160, $ty+38, 19, $acc_c);
    imagettftext($img, 10, 0, $bx+16, $ty+24, $white, $font_bold, 'Explore Services');
    $outline = imagecolorallocatealpha($img, 255,255,255, 90);
    rounded_rect_local($img, $bx+176, $ty, $bx+310, $ty+38, 19, $outline);
    imagettftext($img, 10, 0, $bx+192, $ty+24, $white, $font_bold, 'Learn More');

    // White section
    $svc_y  = 405;
    $white_bg = imagecolorallocate($img, 250, 250, 252);
    imagefilledrectangle($img, 0, $svc_y, $W, $H, $white_bg);
    $dark_t = imagecolorallocate($img, 20, 20, 35);
    $mid_t  = imagecolorallocate($img, 110,120,140);
    $line_c = imagecolorallocate($img, 220,224,234);

    imagettftext($img, 9, 0, (int)($W/2)-30, $svc_y+24, $acc_c, $font_bold, 'OUR SERVICES');
    imagettftext($img, 18, 0, (int)($W/2)-55, $svc_y+50, $dark_t, $font_bold, 'What We Offer');
    imagettftext($img, 10, 0, (int)($W/2)-90, $svc_y+68, $mid_t, $font_regular, 'Professional services tailored just for you');

    // 3 service cards
    $cw = 236; $gap = 28;
    $tot = 3*$cw + 2*$gap;
    $cx0 = (int)(($W-$tot)/2);
    $cy  = $svc_y + 80;
    $labels = ['Premium Service', 'Expert Care', 'Top Results'];
    $shad   = imagecolorallocatealpha($img, 0,0,0, 118);
    for ($ci = 0; $ci < 3; $ci++) {
        $cx = $cx0 + $ci*($cw+$gap);
        // shadow
        rounded_rect_local($img, $cx+3, $cy+4, $cx+$cw+3, $cy+90, 8, $shad);
        // card
        rounded_rect_local($img, $cx, $cy, $cx+$cw, $cy+90, 8, imagecolorallocate($img,255,255,255));
        // icon bg
        rounded_rect_local($img, $cx+14, $cy+12, $cx+44, $cy+42, 6, alloc_c($img,$accent,100));
        // text
        imagettftext($img, 11, 0, $cx+14, $cy+60, $dark_t, $font_bold, $labels[$ci]);
        rounded_rect_local($img, $cx+14, $cy+66, $cx+$cw-20, $cy+70, 2, $line_c);
        rounded_rect_local($img, $cx+14, $cy+76, $cx+$cw-50, $cy+80, 2, $line_c);
    }

    $ok = imagejpeg($img, $out_dir . '/' . $id . '.jpg', 88);
    imagedestroy($img);
    return (bool)$ok;
}

// ── Collect inputs ─────────────────────────────────────────────────────────────

$id           = preg_replace('/[^a-z0-9\-]/', '', strtolower(trim($_POST['id'] ?? '')));
$name         = trim($_POST['name'] ?? '');
$category     = trim($_POST['category'] ?? '');
$badge        = trim($_POST['badge'] ?? '');
$desc         = trim($_POST['desc'] ?? '');
$accent       = preg_replace('/[^#a-fA-F0-9]/', '', $_POST['accent'] ?? '#7c3aed');
$dark         = preg_replace('/[^#a-fA-F0-9]/', '', $_POST['dark']   ?? '#0a0b15');
$light        = preg_replace('/[^#a-fA-F0-9]/', '', $_POST['light']  ?? '#151829');
$business     = trim($_POST['business_name'] ?? $name);
$style        = trim($_POST['style'] ?? '');
$hero_tagline = trim($_POST['hero_tagline'] ?? $name . '.');
$hero_sub     = trim($_POST['hero_sub'] ?? '');

$valid_cats = ['Hair Salon', 'Barbershop', 'Nail Salon'];
$errors = [];
if (!$id)                        $errors[] = 'Template ID is required.';
if (!$name)                      $errors[] = 'Template name is required.';
if (!in_array($category, $valid_cats)) $errors[] = 'Category must be Hair Salon, Barbershop, or Nail Salon.';
if (empty($_FILES['zipfile']['tmp_name'])) $errors[] = 'ZIP file is required.';

// Check for duplicate ID
require_once __DIR__ . '/data/templates.php';
if (isset($all_templates[$id])) $errors[] = "Template ID \"$id\" already exists.";

// ── Paths ──────────────────────────────────────────────────────────────────────

$workspace_dir  = dirname(dirname(__DIR__)) . '/workspace';
$artifacts_dir  = __DIR__ . '/../../artifacts';
$dest_dir       = realpath($artifacts_dir) . '/template-' . $id;
$built_dir      = __DIR__ . '/templates/' . $id;
$thumbs_dir     = __DIR__ . '/assets/img/thumbs';
$templates_file = __DIR__ . '/data/templates.php';
$base_path_url  = '/launchsite/templates/' . $id . '/';
$react_path     = $base_path_url;

// Find pnpm
$pnpm = trim(shell_exec('which pnpm 2>/dev/null') ?? '');
if (!$pnpm) $pnpm = '/home/runner/.nix-profile/bin/pnpm';

$pnpm_env = 'HOME=' . escapeshellarg(getenv('HOME') ?: '/home/runner')
          . ' PATH=' . escapeshellarg(getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin')
          . ' ' . escapeshellarg($pnpm);

// ── Output starts here ─────────────────────────────────────────────────────────

ob_implicit_flush(true);
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Installing Template — Launchit Admin</title>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/admin.css">
</head>
<body class="admin-body">
<header class="admin-header">
    <a class="admin-header__brand" href="<?php echo BASE_PATH; ?>/admin.php">
        <div class="admin-header__logo">🚀</div>
        Launchit Admin
    </a>
</header>

<div class="install-result">
    <div class="admin-page-title" style="margin-bottom:6px;">Installing Template</div>
    <div class="admin-page-sub" style="margin-bottom:24px;">
        <?php echo htmlspecialchars($name ?: $id); ?>
    </div>

<?php if ($errors): ?>
    <div class="result-box result-box--error">
        <div class="result-box__title">Validation failed</div>
        <ul style="margin-top:8px;padding-left:20px;color:rgba(255,255,255,0.6);font-size:0.875rem;line-height:1.8;">
            <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="result-actions">
        <a href="<?php echo BASE_PATH; ?>/admin.php#upload" class="btn-admin btn-admin--ghost">← Back to Admin</a>
    </div>
</div></body></html>
<?php exit; endif; ?>

    <div class="result-box result-box--success">
        <div class="result-box__title" style="margin-bottom:14px;">Installation progress</div>
        <ul class="result-steps" id="steps">
<?php

// ── Step 1: Extract ZIP ────────────────────────────────────────────────────────
step('📦', 'Extracting ZIP archive…');

$zip_tmp = $_FILES['zipfile']['tmp_name'];
$zip     = new ZipArchive();
if ($zip->open($zip_tmp) !== true) abort('Could not open ZIP file.');

// Find top-level prefix (some zips wrap everything in a folder)
$prefix = '';
$first  = $zip->getNameIndex(0);
if ($first && substr($first, -1) === '/') {
    // Check if ALL entries start with this prefix
    $all_under = true;
    for ($i = 1; $i < $zip->numFiles; $i++) {
        if (strpos($zip->getNameIndex($i), $first) !== 0) { $all_under = false; break; }
    }
    if ($all_under) $prefix = $first;
}

// Extract to temp dir, then move
$tmp_extract = sys_get_temp_dir() . '/launchit_' . $id . '_' . time();
if (!mkdir($tmp_extract, 0755, true)) abort('Could not create temp directory.');
$zip->extractTo($tmp_extract);
$zip->close();

// Move to dest
$src = $prefix ? $tmp_extract . '/' . trim($prefix, '/') : $tmp_extract;
if (is_dir($dest_dir)) {
    step('⚠️', "Removing existing directory <code>artifacts/template-$id/</code>…");
    $r = run("rm -rf " . escapeshellarg($dest_dir));
}
if (!rename($src, $dest_dir)) {
    // Try copy if rename fails (cross-device)
    run("cp -r " . escapeshellarg($src) . " " . escapeshellarg($dest_dir));
    run("rm -rf " . escapeshellarg($tmp_extract));
}
step('✅', "Extracted to <code>artifacts/template-$id/</code>");

// ── Step 2: Configure Vite ─────────────────────────────────────────────────────
step('⚙️', 'Configuring Vite build paths…');

$vite_config_ts = $dest_dir . '/vite.config.ts';
$vite_config_js = $dest_dir . '/vite.config.js';
$vite_out_rel   = '../../launchsite-php/templates/' . $id;
$vite_content   = <<<VITE
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  base: '$base_path_url',
  build: {
    outDir: '$vite_out_rel',
    emptyOutDir: true,
  },
})
VITE;

file_put_contents($vite_config_ts, $vite_content);
// Remove js config if exists (avoid conflict)
if (file_exists($vite_config_js)) unlink($vite_config_js);

step('✅', 'vite.config.ts written with correct base path and outDir');

// ── Step 3: pnpm install ───────────────────────────────────────────────────────
step('📥', 'Running <code>pnpm install</code> (this may take 20–40 s)…');

$r_install = run("HOME=" . escapeshellarg(getenv('HOME') ?: '/home/runner')
               . " PATH=" . escapeshellarg(getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin')
               . " $pnpm install --ignore-workspace", $dest_dir);

if ($r_install['code'] !== 0) {
    step_log('❌', 'pnpm install failed', $r_install['output']);
    abort('Dependency installation failed. See log above.');
}
step('✅', 'Dependencies installed');

// ── Step 4: Vite build ─────────────────────────────────────────────────────────
step('🔨', 'Running <code>vite build</code>…');

$r_build = run("HOME=" . escapeshellarg(getenv('HOME') ?: '/home/runner')
             . " PATH=" . escapeshellarg(getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin')
             . " $pnpm exec vite build", $dest_dir);

if ($r_build['code'] !== 0 || !file_exists($built_dir . '/index.html')) {
    step_log('❌', 'Build failed', $r_build['output']);
    abort('Vite build failed. See log above. Check that your vite.config.ts imports @vitejs/plugin-react.');
}
step('✅', "Built successfully → <code>launchsite-php/templates/$id/</code>");

// ── Step 5: Register in templates.php ─────────────────────────────────────────
step('📝', 'Registering template in catalog data…');

$features_arr = ['Services', 'Gallery', 'Booking'];
$badge_val    = $badge ? "'$badge'" : "''";
$feat_str     = "['" . implode("', '", $features_arr) . "']";

$entry = <<<PHP

    '$id' => [
        'id'       => '$id',
        'name'     => '$name',
        'category' => '$category',
        'style'    => '$style',
        'desc'     => '$desc',
        'badge'    => '$badge',
        'features' => $feat_str,
        'accent'   => '$accent',
        'dark'     => '$dark',
        'light'    => '$light',
        'url_slug' => '$id',
        'hero_tagline' => '$hero_tagline',
        'hero_sub'     => '$hero_sub',
        'business_name'=> '$business',
        'type'     => 'react',
        'react_path' => '$react_path',
    ],
PHP;

$tpl_content = file_get_contents($templates_file);
// Insert before the closing ]; of $all_templates
$tpl_content = preg_replace('/(\n\];\s*)$/', $entry . "\n];", $tpl_content);
file_put_contents($templates_file, $tpl_content);
step('✅', "Registered in <code>data/templates.php</code> — template will appear in catalog automatically");

// ── Step 6: Thumbnail ─────────────────────────────────────────────────────────
step('🖼️', 'Generating catalog thumbnail…');
$thumb_ok = generate_thumbnail($id, $name, $accent, $dark, $light, $hero_tagline, $thumbs_dir);
if ($thumb_ok) {
    step('✅', "Thumbnail generated at <code>assets/img/thumbs/$id.jpg</code>");
} else {
    step('⚠️', 'Thumbnail generation skipped (GD library not available) — add a JPG manually to <code>assets/img/thumbs/'.$id.'.jpg</code>');
}

$preview_url = BASE_PATH . '/preview.php?id=' . urlencode($id);
$cat_slug    = ['Hair Salon' => 'hair-salons.php', 'Barbershop' => 'barbershops.php', 'Nail Salon' => 'nail-salons.php'][$category] ?? '';
$cat_url     = BASE_PATH . '/' . $cat_slug;
?>
        </ul>
    </div>

    <div class="result-box result-box--success">
        <div class="result-box__title">🎉 Template installed successfully!</div>
        <p style="color:rgba(255,255,255,0.6);font-size:0.875rem;margin-top:4px;">
            <strong style="color:white;"><?php echo htmlspecialchars($name); ?></strong>
            is now live in the catalog under <strong style="color:white;"><?php echo htmlspecialchars($category); ?></strong>.
        </p>
    </div>

    <div class="result-actions">
        <a href="<?php echo $preview_url; ?>" target="_blank" class="btn-admin btn-admin--orange">Preview Template ↗</a>
        <a href="<?php echo $cat_url; ?>" target="_blank" class="btn-admin btn-admin--ghost">View in Catalog ↗</a>
        <a href="<?php echo BASE_PATH; ?>/admin.php" class="btn-admin btn-admin--ghost">← Back to Admin</a>
    </div>
</div>
</body>
</html>
