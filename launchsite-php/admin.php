<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/data/templates.php';

define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'launchit-admin');

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . BASE_PATH . '/admin.php');
    exit;
}

// Login POST
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . BASE_PATH . '/admin.php');
        exit;
    } else {
        $login_error = 'Incorrect password. Please try again.';
    }
}

$is_authed = !empty($_SESSION['admin_logged_in']);

// Stats
$hair_count  = count(array_filter($all_templates, fn($t) => $t['category'] === 'Hair Salon'));
$barb_count  = count(array_filter($all_templates, fn($t) => $t['category'] === 'Barbershop'));
$nail_count  = count(array_filter($all_templates, fn($t) => $t['category'] === 'Nail Salon'));
$total_count = count($all_templates);
$thumbs_dir  = __DIR__ . '/assets/img/thumbs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Launchit Admin — Certxa</title>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<?php if (!$is_authed): ?>
<!-- ── Login ── -->
<div class="admin-login-wrap">
    <div class="admin-login-box">
        <div class="admin-login-logo">🚀</div>
        <div class="admin-login-title">Launchit Admin</div>
        <div class="admin-login-sub">Sign in to manage templates</div>
        <?php if ($login_error): ?>
        <div class="admin-login-error"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="password" name="password" class="form-input" placeholder="Admin password" autofocus required>
            <button type="submit" class="btn-admin btn-admin--primary" style="width:100%;justify-content:center;">Sign In</button>
        </form>
        <p style="margin-top:18px;font-size:0.75rem;color:rgba(255,255,255,0.2);">
            Set the <code>ADMIN_PASSWORD</code> environment variable to change the password.
        </p>
    </div>
</div>

<?php else: ?>
<!-- ── Dashboard ── -->
<header class="admin-header">
    <a class="admin-header__brand" href="<?php echo BASE_PATH; ?>/admin.php">
        <div class="admin-header__logo">🚀</div>
        Launchit Admin
        <span class="admin-header__tag">Certxa</span>
    </a>
    <div class="admin-header__actions">
        <span class="admin-header__user">Catalog Manager</span>
        <a href="<?php echo BASE_PATH; ?>/" target="_blank" class="admin-logout">View Catalog ↗</a>
        <a href="?logout=1" class="admin-logout">Sign Out</a>
    </div>
</header>

<div class="admin-layout">
    <div class="admin-page-title">Template Catalog</div>
    <div class="admin-page-sub">Upload, manage, and preview all salon website templates.</div>

    <!-- Stats -->
    <div class="admin-stats">
        <div class="admin-stat admin-stat--purple">
            <div class="admin-stat__num"><?php echo $total_count; ?></div>
            <div class="admin-stat__label">Total Templates</div>
        </div>
        <div class="admin-stat">
            <div class="admin-stat__num"><?php echo $hair_count; ?></div>
            <div class="admin-stat__label">Hair Salons</div>
        </div>
        <div class="admin-stat">
            <div class="admin-stat__num"><?php echo $barb_count; ?></div>
            <div class="admin-stat__label">Barbershops</div>
        </div>
        <div class="admin-stat admin-stat--green">
            <div class="admin-stat__num"><?php echo $nail_count; ?></div>
            <div class="admin-stat__label">Nail Salons</div>
        </div>
    </div>

    <!-- Template List -->
    <div class="admin-card">
        <div class="admin-card__header">
            <span class="admin-card__title">All Templates</span>
            <a href="#upload" class="btn-admin btn-admin--orange btn-admin--sm">+ Upload New Template</a>
        </div>
        <div class="admin-card__body" style="padding:0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Template</th>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Thumbnail</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_templates as $id => $t):
                        $thumb_ok = file_exists($thumbs_dir . '/' . $id . '.jpg');
                        $type     = $t['type'] ?? 'php';
                    ?>
                    <tr>
                        <td style="font-weight:600;color:white;"><?php echo htmlspecialchars($t['name']); ?></td>
                        <td class="tbl-id"><?php echo htmlspecialchars($id); ?></td>
                        <td class="tbl-cat"><?php echo htmlspecialchars($t['category']); ?></td>
                        <td>
                            <span class="tbl-badge tbl-badge--<?php echo $type; ?>">
                                <?php echo strtoupper($type); ?>
                            </span>
                        </td>
                        <td>
                            <span class="thumb-dot thumb-dot--<?php echo $thumb_ok ? 'ok' : 'missing'; ?>"></span>
                            <?php echo $thumb_ok ? 'OK' : 'Missing'; ?>
                        </td>
                        <td>
                            <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($id); ?>"
                               target="_blank" class="tbl-link">Preview ↗</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload New Template -->
    <div class="admin-card" id="upload">
        <div class="admin-card__header">
            <span class="admin-card__title">Upload New React/Vite Template</span>
        </div>
        <div class="admin-card__body">
            <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;margin-bottom:20px;line-height:1.6;">
                Upload a zipped React/Vite project. If the zip contains a <code style="color:#a78bfa">launchit.json</code> file, the form fields below will be filled in automatically.
                The admin will install dependencies, build the app, register it in the catalog, and generate a thumbnail — all automatically.
            </p>

            <form id="uploadForm" method="POST" action="<?php echo BASE_PATH; ?>/admin-install.php"
                  enctype="multipart/form-data" onsubmit="return confirmInstall()">

                <!-- Drop zone -->
                <div class="upload-drop" id="dropZone" onclick="document.getElementById('zipFile').click()">
                    <div class="upload-drop__icon">📦</div>
                    <div class="upload-drop__title">Drop your ZIP file here, or click to browse</div>
                    <div class="upload-drop__sub">Accepts .zip files up to 50 MB</div>
                    <div class="upload-drop__filename" id="fileNameDisplay" style="display:none;"></div>
                    <input type="file" name="zipfile" id="zipFile" accept=".zip" required>
                </div>

                <div class="form-section-title">Template Identity</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Template ID *</label>
                        <input type="text" name="id" id="fId" class="form-input"
                               placeholder="e.g. luxury-nails-spa" required
                               pattern="[a-z0-9\-]+"
                               title="Lowercase letters, numbers, and hyphens only">
                        <span class="form-hint">Lowercase, hyphens only. Used in the URL.</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Template Name *</label>
                        <input type="text" name="name" id="fName" class="form-input"
                               placeholder="e.g. Luxury Nails Spa" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category *</label>
                        <select name="category" id="fCategory" class="form-select" required>
                            <option value="">— Select —</option>
                            <option value="Hair Salon">Hair Salon</option>
                            <option value="Barbershop">Barbershop</option>
                            <option value="Nail Salon">Nail Salon</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Badge</label>
                        <select name="badge" id="fBadge" class="form-select">
                            <option value="">None</option>
                            <option value="new">New</option>
                            <option value="popular">Popular</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div class="form-group form-group--full">
                        <label class="form-label">Description</label>
                        <textarea name="desc" id="fDesc" class="form-textarea"
                                  placeholder="Short description shown in the catalog card…"></textarea>
                    </div>
                </div>

                <div class="form-section-title">Display Colors</div>
                <div class="form-grid form-grid--3">
                    <div class="form-group">
                        <label class="form-label">Accent Color</label>
                        <div class="color-row">
                            <input type="color" name="accent_pick" id="fAccentPick" class="form-color" value="#7c3aed"
                                   oninput="document.getElementById('fAccent').value=this.value">
                            <input type="text" name="accent" id="fAccent" class="form-input" value="#7c3aed"
                                   placeholder="#7c3aed"
                                   oninput="document.getElementById('fAccentPick').value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dark Background</label>
                        <div class="color-row">
                            <input type="color" name="dark_pick" id="fDarkPick" class="form-color" value="#0a0b15"
                                   oninput="document.getElementById('fDark').value=this.value">
                            <input type="text" name="dark" id="fDark" class="form-input" value="#0a0b15"
                                   placeholder="#0a0b15"
                                   oninput="document.getElementById('fDarkPick').value=this.value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Light Background</label>
                        <div class="color-row">
                            <input type="color" name="light_pick" id="fLightPick" class="form-color" value="#151829"
                                   oninput="document.getElementById('fLight').value=this.value">
                            <input type="text" name="light" id="fLight" class="form-input" value="#151829"
                                   placeholder="#151829"
                                   oninput="document.getElementById('fLightPick').value=this.value">
                        </div>
                    </div>
                </div>

                <div class="form-section-title">Catalog & Preview Copy</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" id="fBusinessName" class="form-input"
                               placeholder="e.g. Luxury Nails Spa">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Style Tag</label>
                        <input type="text" name="style" id="fStyle" class="form-input"
                               placeholder="e.g. Luxury Spa">
                    </div>
                    <div class="form-group form-group--full">
                        <label class="form-label">Hero Tagline</label>
                        <input type="text" name="hero_tagline" id="fHeroTagline" class="form-input"
                               placeholder="e.g. Where beauty meets relaxation.">
                    </div>
                    <div class="form-group form-group--full">
                        <label class="form-label">Hero Sub-Text</label>
                        <input type="text" name="hero_sub" id="fHeroSub" class="form-input"
                               placeholder="e.g. Experience premium nail care in an elegant environment.">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin btn-admin--orange" id="installBtn">
                        🚀 Install Template
                    </button>
                    <span style="color:rgba(255,255,255,0.3);font-size:0.8rem;align-self:center;">
                        This will run <code>pnpm install</code> + <code>vite build</code> — may take 30–60 seconds.
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ── Drag & drop ──
const dropZone = document.getElementById('dropZone');
const zipFile  = document.getElementById('zipFile');
const fileNameDisplay = document.getElementById('fileNameDisplay');

dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.zip')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        zipFile.files = dt.files;
        onFileSelected(file);
    }
});
zipFile.addEventListener('change', () => {
    if (zipFile.files[0]) onFileSelected(zipFile.files[0]);
});

function onFileSelected(file) {
    fileNameDisplay.textContent = '📦 ' + file.name;
    fileNameDisplay.style.display = 'block';
    // Auto-fill ID from filename (strip .zip, lowercase, replace spaces/underscores with dashes)
    const guessId = file.name.replace(/\.zip$/i,'').toLowerCase().replace(/[\s_]+/g,'-').replace(/[^a-z0-9\-]/g,'');
    if (!document.getElementById('fId').value) {
        document.getElementById('fId').value = guessId;
    }
    // Try to read launchit.json from zip (requires JSZip — skip if unavailable)
    tryReadManifest(file);
}

// Try to read launchit.json from the ZIP using the File API + DecompressionStream (not widely available)
// Simple fallback: just let the user fill the form
async function tryReadManifest(file) {
    try {
        const JSZip = (await import('https://cdn.jsdelivr.net/npm/jszip@3.10.1/+esm')).default;
        const zip   = await JSZip.loadAsync(file);
        // Find launchit.json (may be in a subdirectory)
        let manifest = null;
        zip.forEach((path, f) => {
            if (path.endsWith('launchit.json') && !manifest) manifest = f;
        });
        if (!manifest) return;
        const data = JSON.parse(await manifest.async('string'));
        if (data.id)            document.getElementById('fId').value = data.id;
        if (data.name)          document.getElementById('fName').value = data.name;
        if (data.category)      document.getElementById('fCategory').value = data.category;
        if (data.badge)         document.getElementById('fBadge').value = data.badge;
        if (data.desc)          document.getElementById('fDesc').value = data.desc;
        if (data.accent)        { document.getElementById('fAccent').value = data.accent; document.getElementById('fAccentPick').value = data.accent; }
        if (data.dark)          { document.getElementById('fDark').value = data.dark; document.getElementById('fDarkPick').value = data.dark; }
        if (data.light)         { document.getElementById('fLight').value = data.light; document.getElementById('fLightPick').value = data.light; }
        if (data.business_name) document.getElementById('fBusinessName').value = data.business_name;
        if (data.style)         document.getElementById('fStyle').value = data.style;
        if (data.hero_tagline)  document.getElementById('fHeroTagline').value = data.hero_tagline;
        if (data.hero_sub)      document.getElementById('fHeroSub').value = data.hero_sub;
    } catch(e) { /* silently ignore — user fills form manually */ }
}

function confirmInstall() {
    const id = document.getElementById('fId').value;
    if (!id) return false;
    document.getElementById('installBtn').innerHTML = '<span class="spinner"></span> Installing…';
    document.getElementById('installBtn').disabled = true;
    return true;
}
</script>
<?php endif; ?>
</body>
</html>
