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

// Flash messages from redirects
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

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

    <?php if ($flash): ?>
    <div class="flash-msg flash-msg--<?php echo htmlspecialchars($flash['type']); ?>" id="flashMsg">
        <span class="flash-msg__icon"><?php echo $flash['type'] === 'success' ? '✅' : '❌'; ?></span>
        <span><?php echo htmlspecialchars($flash['msg']); ?></span>
        <button class="flash-msg__close" onclick="this.parentElement.remove()">✕</button>
    </div>
    <?php endif; ?>

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
                        <td class="tbl-actions">
                            <a href="<?php echo BASE_PATH; ?>/preview.php?id=<?php echo urlencode($id); ?>"
                               target="_blank" class="tbl-link">Preview ↗</a>
                            <?php if ($type === 'react'): ?>
                            <button class="tbl-link tbl-link--replace btn-replace"
                                    data-id="<?php echo htmlspecialchars($id); ?>"
                                    data-name="<?php echo htmlspecialchars($t['name']); ?>">
                                Replace
                            </button>
                            <?php $has_source = is_dir(dirname(__DIR__) . '/artifacts/template-' . $id); ?>
                            <?php if ($has_source): ?>
                            <form method="POST" action="<?php echo BASE_PATH; ?>/admin-detect.php" style="display:inline;">
                                <input type="hidden" name="template_id" value="<?php echo htmlspecialchars($id); ?>">
                                <button type="submit" class="tbl-link tbl-link--sync"
                                        title="Re-scan source code and update name, colors, hero text &amp; thumbnail">
                                    Re-sync
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo BASE_PATH; ?>/admin-thumb.php" style="display:inline;">
                                <input type="hidden" name="template_id" value="<?php echo htmlspecialchars($id); ?>">
                                <button type="submit" class="tbl-link tbl-link--regen">Regen Thumb</button>
                            </form>
                            <button class="tbl-link tbl-link--delete btn-delete"
                                    data-id="<?php echo htmlspecialchars($id); ?>"
                                    data-name="<?php echo htmlspecialchars($t['name']); ?>"
                                    data-type="<?php echo htmlspecialchars($type); ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal-backdrop" style="display:none;">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-title">Delete Template</div>
                <button class="modal-close" onclick="closeDeleteModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="modal-template-info modal-template-info--danger" id="deleteTemplateName"></div>
                <p class="modal-desc" style="color:rgba(248,113,113,0.8);">
                    This will permanently remove the template from the catalog and delete all associated files.
                    This cannot be undone.
                </p>
                <div id="deleteReactNote" class="modal-delete-note" style="display:none;">
                    The following will be deleted:
                    <ul class="modal-delete-list">
                        <li>Catalog registration in <code>data/templates.php</code></li>
                        <li>Built site files in <code>launchsite-php/templates/{id}/</code></li>
                        <li>Source files in <code>artifacts/template-{id}/</code></li>
                        <li>Thumbnail image</li>
                    </ul>
                </div>
                <div id="deletePhpNote" class="modal-delete-note" style="display:none;">
                    The following will be deleted:
                    <ul class="modal-delete-list">
                        <li>Catalog registration in <code>data/templates.php</code></li>
                        <li>Thumbnail image</li>
                    </ul>
                    <p style="margin-top:8px;color:rgba(255,255,255,0.3);font-size:0.75rem;">
                        PHP template files are not deleted — remove them manually if needed.
                    </p>
                </div>
                <form id="deleteForm" method="POST" action="<?php echo BASE_PATH; ?>/admin-delete.php"
                      onsubmit="return confirmDelete()">
                    <input type="hidden" name="template_id" id="deleteTemplateId">
                    <div class="form-group" style="margin-top:18px;">
                        <label class="form-label" style="color:rgba(248,113,113,0.7);">
                            Type the template ID to confirm
                        </label>
                        <input type="text" id="deleteConfirmInput" class="form-input form-input--danger"
                               placeholder="e.g. luxury-nails-spa" autocomplete="off" spellcheck="false">
                        <span class="form-hint" id="deleteConfirmHint"></span>
                    </div>
                    <div class="modal-actions" style="margin-top:20px;">
                        <button type="submit" class="btn-admin btn-admin--danger" id="deleteBtn" disabled>
                            🗑️ Delete Permanently
                        </button>
                        <button type="button" class="btn-admin btn-admin--ghost" onclick="closeDeleteModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Replace Modal -->
    <div id="replaceModal" class="modal-backdrop" style="display:none;">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-title">Replace Template</div>
                <button class="modal-close" onclick="closeReplaceModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="modal-template-info" id="modalTemplateName"></div>
                <p class="modal-desc">
                    Upload a new ZIP to rebuild this template. The category stays the same.
                    Name, colors, and hero text are re-detected from your new source files.
                </p>
                <form id="replaceForm" method="POST" action="<?php echo BASE_PATH; ?>/admin-replace.php"
                      enctype="multipart/form-data" onsubmit="return confirmReplace()">
                    <input type="hidden" name="template_id" id="replaceTemplateId">
                    <div class="upload-drop upload-drop--compact" id="replaceDropZone"
                         onclick="document.getElementById('replaceZip').click()">
                        <div class="upload-drop__icon" style="font-size:1.8rem;margin-bottom:6px;">📦</div>
                        <div class="upload-drop__title" id="replaceDropTitle">Drop new ZIP here, or click to browse</div>
                        <div class="upload-drop__sub">React/Vite project ZIP · up to 50 MB</div>
                        <input type="file" name="zipfile" id="replaceZip" accept=".zip" required>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="btn-admin btn-admin--orange" id="replaceBtn">
                            🔄 Replace &amp; Rebuild
                        </button>
                        <button type="button" class="btn-admin btn-admin--ghost" onclick="closeReplaceModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload New Template -->
    <div class="admin-card" id="upload">
        <div class="admin-card__header">
            <span class="admin-card__title">Upload New React/Vite Template</span>
        </div>
        <div class="admin-card__body">
            <p style="color:rgba(255,255,255,0.5);font-size:0.85rem;margin-bottom:24px;line-height:1.7;">
                Upload your zipped React/Vite project and select a category. Everything else — the template name,
                colors, hero text, and business name — is detected automatically from your source files.
                Dependencies are installed and the site is built and registered in the catalog automatically.
            </p>

            <form id="uploadForm" method="POST" action="<?php echo BASE_PATH; ?>/admin-install.php"
                  enctype="multipart/form-data" onsubmit="return confirmInstall()">

                <!-- Drop zone -->
                <div class="upload-drop" id="dropZone" onclick="document.getElementById('zipFile').click()">
                    <div class="upload-drop__icon">📦</div>
                    <div class="upload-drop__title">Drop your ZIP file here, or click to browse</div>
                    <div class="upload-drop__sub">Accepts .zip files up to 50 MB · React/Vite projects only</div>
                    <div class="upload-drop__filename" id="fileNameDisplay" style="display:none;"></div>
                    <input type="file" name="zipfile" id="zipFile" accept=".zip" required>
                </div>

                <div class="form-section-title">One required field</div>
                <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:16px;max-width:560px;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Category *</label>
                        <select name="category" id="fCategory" class="form-select" required>
                            <option value="">— Select category —</option>
                            <option value="Hair Salon">Hair Salon</option>
                            <option value="Barbershop">Barbershop</option>
                            <option value="Nail Salon">Nail Salon</option>
                        </select>
                        <span class="form-hint">All other info (name, colors, hero text) is read from your source files.</span>
                    </div>
                </div>

                <div class="form-actions" style="margin-top:24px;">
                    <button type="submit" class="btn-admin btn-admin--orange" id="installBtn">
                        🚀 Install Template
                    </button>
                    <span style="color:rgba(255,255,255,0.3);font-size:0.8rem;align-self:center;">
                        Installs dependencies, builds, registers in catalog &amp; generates thumbnail — ~30–90 s
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ── New template upload ──────────────────────────────────────────────────────
const dropZone        = document.getElementById('dropZone');
const zipFile         = document.getElementById('zipFile');
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
    dropZone.querySelector('.upload-drop__title').textContent = 'File selected — ready to install';
}
function confirmInstall() {
    if (!document.getElementById('fCategory').value) {
        alert('Please select a category.');
        return false;
    }
    const btn = document.getElementById('installBtn');
    btn.innerHTML = '<span class="spinner"></span> Installing… (this takes ~60 s)';
    btn.disabled = true;
    return true;
}

// ── Delete modal ─────────────────────────────────────────────────────────────
let _deleteId = '';

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
        _deleteId = btn.dataset.id;
        const name = btn.dataset.name;
        const type = btn.dataset.type;
        document.getElementById('deleteTemplateId').value   = _deleteId;
        document.getElementById('deleteTemplateName').textContent = name + '  (' + _deleteId + ')';
        document.getElementById('deleteConfirmInput').value  = '';
        document.getElementById('deleteConfirmHint').textContent = 'Must match: ' + _deleteId;
        document.getElementById('deleteBtn').disabled = true;
        document.getElementById('deleteReactNote').style.display = type === 'react' ? 'block' : 'none';
        document.getElementById('deletePhpNote').style.display   = type === 'php'   ? 'block' : 'none';
        // Fill {id} placeholder in the note list
        document.querySelectorAll('#deleteReactNote li').forEach(li => {
            li.innerHTML = li.innerHTML.replace(/\{id\}/g, _deleteId);
        });
        document.getElementById('deleteModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('deleteConfirmInput').focus(), 80);
    });
});

document.getElementById('deleteConfirmInput').addEventListener('input', function() {
    const matches = this.value.trim() === _deleteId;
    document.getElementById('deleteBtn').disabled = !matches;
    this.style.borderColor = this.value ? (matches ? 'rgba(52,211,153,0.6)' : 'rgba(248,113,113,0.4)') : '';
});

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

function confirmDelete() {
    if (document.getElementById('deleteConfirmInput').value.trim() !== _deleteId) return false;
    const btn = document.getElementById('deleteBtn');
    btn.innerHTML = '<span class="spinner"></span> Deleting…';
    btn.disabled  = true;
    return true;
}

// ── Replace modal ────────────────────────────────────────────────────────────
document.querySelectorAll('.btn-replace').forEach(btn => {
    btn.addEventListener('click', () => {
        const id   = btn.dataset.id;
        const name = btn.dataset.name;
        document.getElementById('replaceTemplateId').value = id;
        document.getElementById('modalTemplateName').textContent = name + '  (' + id + ')';
        document.getElementById('replaceDropTitle').textContent  = 'Drop new ZIP here, or click to browse';
        document.getElementById('replaceBtn').innerHTML = '🔄 Replace & Rebuild';
        document.getElementById('replaceBtn').disabled  = false;
        // Reset file input
        document.getElementById('replaceZip').value = '';
        document.getElementById('replaceModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });
});

function closeReplaceModal() {
    document.getElementById('replaceModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('replaceModal').addEventListener('click', function(e) {
    if (e.target === this) closeReplaceModal();
});

// Replace drop zone
const replaceDropZone = document.getElementById('replaceDropZone');
const replaceZip      = document.getElementById('replaceZip');

replaceDropZone.addEventListener('dragover', e => { e.preventDefault(); replaceDropZone.classList.add('drag-over'); });
replaceDropZone.addEventListener('dragleave', () => replaceDropZone.classList.remove('drag-over'));
replaceDropZone.addEventListener('drop', e => {
    e.preventDefault();
    replaceDropZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.zip')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        replaceZip.files = dt.files;
        onReplaceFileSelected(file.name);
    }
});
replaceZip.addEventListener('change', () => {
    if (replaceZip.files[0]) onReplaceFileSelected(replaceZip.files[0].name);
});
function onReplaceFileSelected(name) {
    document.getElementById('replaceDropTitle').textContent = '📦 ' + name + ' — ready';
}

function confirmReplace() {
    if (!replaceZip.files[0]) { alert('Please select a ZIP file.'); return false; }
    const btn = document.getElementById('replaceBtn');
    btn.innerHTML = '<span class="spinner"></span> Rebuilding… (this takes ~60 s)';
    btn.disabled  = true;
    return true;
}

// Close modals on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeReplaceModal();
        closeDeleteModal();
    }
});
</script>
<?php endif; ?>
</body>
</html>
