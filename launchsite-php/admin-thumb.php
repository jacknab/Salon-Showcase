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

require_once __DIR__ . '/data/templates.php';

$template_id = preg_replace('/[^a-z0-9\-]/', '', strtolower(trim($_POST['template_id'] ?? '')));

if (!$template_id || !isset($all_templates[$template_id])) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Template not found: ' . htmlspecialchars($template_id)];
    header('Location: ' . BASE_PATH . '/admin.php');
    exit;
}

$t          = $all_templates[$template_id];
$thumbs_dir = __DIR__ . '/assets/img/thumbs';

// ── Thumbnail generator (same as in admin-replace.php) ────────────────────────

function hex2rgb_t(string $hex): array {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
}
function alloc_ct($img, string $hex, int $alpha = 0): int {
    [$r,$g,$b] = hex2rgb_t($hex);
    return imagecolorallocatealpha($img, $r, $g, $b, $alpha);
}
function rrect_t($img, int $x1, int $y1, int $x2, int $y2, int $r, int $color): void {
    if ($r < 1) $r = 1;
    imagefilledrectangle($img, $x1+$r, $y1, $x2-$r, $y2, $color);
    imagefilledrectangle($img, $x1, $y1+$r, $x2, $y2-$r, $color);
    imagefilledellipse($img, $x1+$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x1+$r, $y2-$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y2-$r, $r*2, $r*2, $color);
}

function generate_single_thumb(array $t, string $thumbs_dir): bool {
    if (!function_exists('imagecreatetruecolor')) return false;
    $fb = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
    $fr = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
    if (!file_exists($fb)) return false;

    $id      = $t['id'];
    $name    = $t['name'];
    $accent  = $t['accent'] ?? '#7c3aed';
    $dark    = $t['dark']   ?? '#0a0b15';
    $light   = $t['light']  ?? '#12152a';
    $tagline = $t['hero_tagline'] ?? $name;

    $W = 900; $H = 620;
    $img = imagecreatetruecolor($W, $H);
    [$dr,$dg,$db] = hex2rgb_t($dark);
    [$lr,$lg,$lb] = hex2rgb_t($light);
    [$ar,$ag,$ab] = hex2rgb_t($accent);

    for ($y = 0; $y < $H; $y++) {
        $tt = $y/$H;
        $c  = imagecolorallocate($img,
            (int)($dr+($lr-$dr)*$tt*0.7),
            (int)($dg+($lg-$dg)*$tt*0.7),
            (int)($db+($lb-$db)*$tt*0.7));
        imagefilledrectangle($img, 0, $y, $W, $y, $c);
    }

    $white = imagecolorallocate($img, 255, 255, 255);
    $acc_c = imagecolorallocate($img, $ar, $ag, $ab);

    for ($rad = 280; $rad >= 20; $rad -= 22) {
        $alpha = (int)(120 - ($rad/280)*118);
        imagefilledellipse($img, (int)($W*0.5), 200, $rad*2, (int)($rad*1.2),
            imagecolorallocatealpha($img,$ar,$ag,$ab,$alpha));
    }

    imagefilledrectangle($img, 0, 0, $W, 52, imagecolorallocatealpha($img,$dr,$dg,$db,30));
    imagefilledrectangle($img, 0, 52, $W, 53, imagecolorallocatealpha($img,255,255,255,115));

    rrect_t($img, 26, 14, 186, 40, 6, alloc_ct($img,$accent,90));
    imagettftext($img, 10, 0, 35, 32, $white, $fb, $name);

    foreach ([240,295,348,398] as $lx)
        rrect_t($img,$lx,22,$lx+38,30,2,imagecolorallocatealpha($img,255,255,255,100));

    rrect_t($img, $W-110, 14, $W-26, 40, 13, $acc_c);
    imagettftext($img, 8, 0, $W-100, 31, $white, $fb, 'Book Now');

    $ey = 100;
    imagettftext($img, 9, 0, (int)(($W/2)-44), $ey+42,
        imagecolorallocatealpha($img,min(255,$ar+80),$ag,$ab,50), $fb, 'WELCOME TO');

    $words = explode(' ', $tagline ?: $name);
    $lines = []; $line = '';
    foreach ($words as $w) {
        $test = $line ? "$line $w" : $w;
        $box  = imagettfbbox(34, 0, $fb, $test);
        if (abs($box[2]-$box[0]) > 540 && $line) { $lines[] = $line; $line = $w; }
        else $line = $test;
    }
    if ($line) $lines[] = $line;

    $ty = $ey + 84;
    foreach (array_slice($lines, 0, 2) as $l) {
        $box = imagettfbbox(34, 0, $fb, $l);
        imagettftext($img, 34, 0, (int)(($W-abs($box[2]-$box[0]))/2), $ty, $white, $fb, $l);
        $ty += 46;
    }

    $ty += 28; $bx = (int)(($W-310)/2);
    rrect_t($img, $bx, $ty, $bx+160, $ty+38, 19, $acc_c);
    imagettftext($img, 10, 0, $bx+16, $ty+24, $white, $fb, 'Explore Services');
    rrect_t($img, $bx+176, $ty, $bx+310, $ty+38, 19,
        imagecolorallocatealpha($img,255,255,255,90));
    imagettftext($img, 10, 0, $bx+192, $ty+24, $white, $fb, 'Learn More');

    $sy  = 405;
    $wbg = imagecolorallocate($img, 250, 250, 252);
    imagefilledrectangle($img, 0, $sy, $W, $H, $wbg);

    $dk  = imagecolorallocate($img, 20,  20,  35);
    $md  = imagecolorallocate($img, 110, 120, 140);
    $ln  = imagecolorallocate($img, 220, 224, 234);

    imagettftext($img, 9,  0, (int)($W/2)-30, $sy+24, $acc_c, $fb, 'OUR SERVICES');
    imagettftext($img, 18, 0, (int)($W/2)-55, $sy+50, $dk,    $fb, 'What We Offer');
    imagettftext($img, 10, 0, (int)($W/2)-90, $sy+68, $md,    $fr,
        'Professional services tailored just for you');

    $cw  = 236; $gap = 28;
    $cx0 = (int)(($W-(3*$cw+2*$gap))/2);
    $cy  = $sy + 80;
    $labels = ['Premium Service', 'Expert Care', 'Top Results'];

    for ($ci = 0; $ci < 3; $ci++) {
        $cx = $cx0 + $ci*($cw+$gap);
        rrect_t($img,$cx+3,$cy+4,$cx+$cw+3,$cy+90,8,
            imagecolorallocatealpha($img,0,0,0,118));
        rrect_t($img,$cx,$cy,$cx+$cw,$cy+90,8,
            imagecolorallocate($img,255,255,255));
        rrect_t($img,$cx+14,$cy+12,$cx+44,$cy+42,6,alloc_ct($img,$accent,100));
        imagettftext($img,11,0,$cx+14,$cy+60,$dk,$fb,$labels[$ci]);
        rrect_t($img,$cx+14,$cy+66,$cx+$cw-20,$cy+70,2,$ln);
        rrect_t($img,$cx+14,$cy+76,$cx+$cw-50,$cy+80,2,$ln);
    }

    $ok = imagejpeg($img, $thumbs_dir . '/' . $id . '.jpg', 88);
    imagedestroy($img);
    return (bool)$ok;
}

$ok = generate_single_thumb($t, $thumbs_dir);

if ($ok) {
    $_SESSION['flash'] = [
        'type' => 'success',
        'msg'  => 'Thumbnail regenerated for "' . $t['name'] . '" (' . $template_id . ')',
    ];
} else {
    $_SESSION['flash'] = [
        'type' => 'error',
        'msg'  => 'Could not regenerate thumbnail — GD library may be unavailable on this server.',
    ];
}

header('Location: ' . BASE_PATH . '/admin.php');
exit;
