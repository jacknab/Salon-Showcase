# Launchit — VPS Deployment Guide

Complete instructions for deploying the Certxa Launchit catalog to a production VPS.
The catalog lives at `certxa.com/launchsite/` alongside the rest of your site.

---

## Table of Contents

1. [Server Requirements](#1-server-requirements)
2. [Get the Files onto Your VPS](#2-get-the-files-onto-your-vps)
3. [Web Server Configuration](#3-web-server-configuration)
   - [Apache](#apache-recommended)
   - [Nginx](#nginx-alternative)
4. [PHP Configuration](#4-php-configuration)
5. [File Permissions](#5-file-permissions)
6. [Admin Password](#6-admin-password)
7. [Node.js + pnpm (React template uploads)](#7-nodejs--pnpm-react-template-uploads)
8. [Chromium / Puppeteer (Regen Thumb)](#8-chromium--puppeteer-regen-thumb)
9. [First-Run Checklist](#9-first-run-checklist)
10. [Ongoing Admin Workflow](#10-ongoing-admin-workflow)
11. [Security Hardening](#11-security-hardening)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. Server Requirements

| Component | Minimum | Notes |
|---|---|---|
| OS | Ubuntu 22.04 LTS | Other Debian-based distros work too |
| Web server | Apache 2.4 **or** Nginx 1.18 | Apache is easier for this setup |
| PHP | **8.2+** | 8.3 is fine |
| PHP extensions | `gd`, `mbstring`, `json`, `session` | GD is required for thumbnail upload/resize |
| Node.js | **20 LTS** or 22 LTS | Required only for React template installs |
| pnpm | **8+** | Package manager for React templates |
| Disk space | 2 GB+ free | Each React template build ~50–200 MB |
| RAM | 1 GB+ | 2 GB recommended if using Puppeteer |

---

## 2. Get the Files onto Your VPS

### Option A — rsync from your local machine (recommended)

From your local machine (or Replit shell), push the catalog files directly:

```bash
rsync -avz --delete \
  /path/to/workspace/launchsite-php/ \
  user@your-vps-ip:/var/www/certxa.com/launchsite/
```

> **Note:** The `launchsite-php/` folder is the web root for the catalog.
> Everything inside it (PHP files, assets, templates, thumbs) goes into `/var/www/certxa.com/launchsite/`.

### Option B — Git

If your repo is on GitHub/GitLab:

```bash
ssh user@your-vps-ip
cd /var/www/certxa.com
git clone https://github.com/yourname/yourrepo.git .
# then copy launchsite-php/ into place:
cp -r launchsite-php/ launchsite/
```

### Option C — SFTP / FileZilla

Connect with your SFTP client and upload the contents of `launchsite-php/` into `/var/www/certxa.com/launchsite/`.

---

## 3. Web Server Configuration

> `router.php` is **only** used by the PHP built-in dev server (Replit).
> On Apache/Nginx you do **not** use `router.php` — the web server handles routing directly.

### Apache (recommended)

#### 1. Enable required modules

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 2. Create a virtual host config

```apache
# /etc/apache2/sites-available/certxa.conf

<VirtualHost *:80>
    ServerName certxa.com
    ServerAlias www.certxa.com
    DocumentRoot /var/www/certxa.com

    # ── Launchit catalog ──────────────────────────────────────
    <Directory /var/www/certxa.com/launchsite>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # React SPA sub-directories: serve index.html for all non-file routes
    <Directory /var/www/certxa.com/launchsite/templates>
        Options -Indexes
        AllowOverride All
        Require all granted
        FallbackResource /launchsite/templates/%1/index.html
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/certxa_error.log
    CustomLog ${APACHE_LOG_DIR}/certxa_access.log combined
</VirtualHost>
```

#### 3. Create a `.htaccess` inside `launchsite/`

Create `/var/www/certxa.com/launchsite/.htaccess`:

```apache
Options -Indexes

# React SPA routing: for each templates/{id}/* request, fall back to that SPA's index.html
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /launchsite/

    # Don't rewrite real files or directories
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    # React template SPA fallback
    RewriteRule ^templates/([^/]+)/(.*)$ /launchsite/templates/$1/index.html [L]
</IfModule>

# PHP file upload limits (overrides php.ini for this directory)
php_value upload_max_filesize 60M
php_value post_max_size 65M
php_value max_execution_time 180
php_value memory_limit 256M
```

#### 4. Enable the site and reload

```bash
sudo a2ensite certxa.conf
sudo apache2ctl configtest   # should say: Syntax OK
sudo systemctl reload apache2
```

#### 5. HTTPS with Let's Encrypt

```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d certxa.com -d www.certxa.com
```

Certbot will auto-add HTTPS redirects and renew certificates automatically.

---

### Nginx (alternative)

```nginx
# /etc/nginx/sites-available/certxa

server {
    listen 80;
    server_name certxa.com www.certxa.com;
    root /var/www/certxa.com;
    index index.php index.html;

    # ── Launchit catalog ──────────────────────────────────────
    location /launchsite {
        try_files $uri $uri/ @php;
    }

    # React SPA fallback — each template/{id}/ is its own SPA
    location ~ ^/launchsite/templates/([^/]+)/(.*)$ {
        try_files $uri /launchsite/templates/$1/index.html;
    }

    # PHP handler
    location @php {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Block direct access to data/ directory
    location /launchsite/data/ {
        deny all;
    }

    client_max_body_size 65M;
}
```

```bash
sudo ln -s /etc/nginx/sites-available/certxa /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
# HTTPS:
sudo certbot --nginx -d certxa.com -d www.certxa.com
```

---

## 4. PHP Configuration

Check your active `php.ini` path:

```bash
php --ini | grep "Loaded Configuration"
```

Edit it (usually `/etc/php/8.2/apache2/php.ini` for Apache, `/etc/php/8.2/fpm/php.ini` for Nginx+FPM):

```ini
; Required for React template ZIP uploads
upload_max_filesize = 60M
post_max_size       = 65M
max_execution_time  = 180
max_input_time      = 120
memory_limit        = 256M

; Required for thumbnail generation
extension=gd
```

Verify GD is enabled:

```bash
php -m | grep gd
# should print: gd
```

If GD is missing:

```bash
sudo apt install php8.2-gd -y
sudo systemctl restart apache2   # or php8.2-fpm for Nginx
```

Restart PHP after any php.ini change:

```bash
sudo systemctl restart apache2
# or for Nginx+FPM:
sudo systemctl restart php8.2-fpm
```

---

## 5. File Permissions

The web server user (`www-data` on Ubuntu) must be able to **read** all files and **write** to specific directories used by the admin panel.

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/certxa.com/launchsite/

# Directories the admin panel writes to:
sudo chmod 775 /var/www/certxa.com/launchsite/data/
sudo chmod 775 /var/www/certxa.com/launchsite/assets/img/thumbs/
sudo chmod 775 /var/www/certxa.com/launchsite/templates/

# Everything else: readable, not writable by web server
sudo find /var/www/certxa.com/launchsite/ -type f -exec chmod 644 {} \;
sudo find /var/www/certxa.com/launchsite/ -type d -exec chmod 755 {} \;

# Re-apply writable dirs after the above
sudo chmod 775 /var/www/certxa.com/launchsite/data/
sudo chmod 775 /var/www/certxa.com/launchsite/assets/img/thumbs/
sudo chmod 775 /var/www/certxa.com/launchsite/templates/
```

> If you also deploy React template source files (`artifacts/template-*/`), put them **outside** the web root, e.g. `/var/www/certxa-artifacts/`, so they are not publicly accessible. The admin install script only needs write access to `templates/` and `data/`.

---

## 6. Admin Password

The admin panel (`/launchsite/admin.php`) is protected by a password.

### Set via environment variable (recommended)

For Apache, add to your virtual host config:

```apache
SetEnv ADMIN_PASSWORD your-strong-password-here
```

For Nginx+FPM, add to `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
env[ADMIN_PASSWORD] = your-strong-password-here
```

Then restart Apache/FPM.

### Fallback default

If no environment variable is set, the default password is `launchit-admin`.
**Change this before going live.**

---

## 7. Node.js + pnpm (React template uploads)

This is required if you want to upload new React/Vite template ZIPs through the admin panel and have them built automatically on the server.

```bash
# Install Node.js 20 LTS via NodeSource
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify
node -v    # v20.x.x
npm -v

# Install pnpm globally
sudo npm install -g pnpm

# Verify
pnpm -v    # 8.x or 9.x
```

The admin install script runs `pnpm install` and `pnpm run build` inside a temp directory. Make sure the `www-data` user can run these commands.

If `www-data` can't run `node`/`pnpm` due to PATH issues, add this to your Apache virtual host:

```apache
SetEnv PATH /usr/local/bin:/usr/bin:/bin
```

---

## 8. Chromium / Puppeteer (Regen Thumb)

The **Regen Thumb** button in the admin panel takes a headless browser screenshot of the live template and saves it as the catalog card image. This requires Chromium on the server.

```bash
# Install Chromium
sudo apt install -y chromium-browser

# Verify path
which chromium-browser
# typically: /usr/bin/chromium-browser
```

The screenshot script is at `scripts/src/screenshot.mjs` in your project. Update the Chromium path in the script if it differs from the Replit path:

Open `scripts/src/screenshot.mjs` and find the `executablePath` line. Change it to match your server's Chromium path:

```js
executablePath: '/usr/bin/chromium-browser',
```

Then install script dependencies (run once from the project root):

```bash
cd /var/www/certxa-artifacts/scripts   # wherever your scripts/ folder lives
pnpm install
```

> **Note:** The **Upload Image** button does not need Chromium — it uses PHP GD to process your uploaded image directly. Chromium is only needed for the automatic screenshot feature.

---

## 9. First-Run Checklist

After uploading files and configuring the server, run through this list:

```
[ ] Visit https://certxa.com/launchsite/ — catalog homepage loads
[ ] Visit https://certxa.com/launchsite/hair-salons.php — template grid loads with thumbnails
[ ] Click a template card — preview page opens correctly
[ ] Visit https://certxa.com/launchsite/admin.php — login form appears
[ ] Log in with your admin password — template table loads
[ ] Click "Preview ↗" on a PHP template — full preview renders
[ ] Click "Preview ↗" on a React template — iframe loads the built SPA
[ ] Click "Edit" on any template — modal opens with current values
[ ] Edit a field, Save — flash message confirms, change appears in table
[ ] Click "Upload Image" — modal opens, upload a test image, thumbnail updates
[ ] Check file permissions: try uploading a React ZIP through the admin panel
```

---

## 10. Ongoing Admin Workflow

Once live, you manage everything through the admin panel at `/launchsite/admin.php`.

### Adding a new React template

1. Build your React/Vite project locally and zip the source folder
2. Log into the admin panel
3. Click **Upload New React/Vite Template**, choose category, upload ZIP
4. The server installs dependencies, builds the site, registers it, and generates a thumbnail — takes 30–90 seconds
5. The new template card appears in the catalog immediately

### Updating a template's catalog copy

- **Edit** — change name, description, colors, badge, hero text directly in the admin
- **Upload Image** — replace the catalog card thumbnail with any JPG/PNG/WebP
- **Regen Thumb** — re-capture a fresh screenshot from the live template
- **Duplicate** — create a second catalog entry (React: also copies built files)
- **Replace** — upload a new ZIP to rebuild the template completely

### Deploying file updates from Replit to VPS

After making code changes in Replit, push them to your VPS with rsync:

```bash
rsync -avz --delete \
  /path/to/workspace/launchsite-php/ \
  user@your-vps-ip:/var/www/certxa.com/launchsite/

# Fix permissions after sync
ssh user@your-vps-ip "sudo chown -R www-data:www-data /var/www/certxa.com/launchsite/"
```

---

## 11. Security Hardening

### Block direct access to sensitive directories

Add to `.htaccess` (Apache) or Nginx config:

```apache
# Block data/ directory from web access (contains templates.php registry)
<Directory /var/www/certxa.com/launchsite/data>
    Require all denied
</Directory>

# Block direct access to PHP admin scripts from non-admin IPs (optional)
<FilesMatch "^admin.*\.php$">
    Require ip 203.0.113.0/24   # replace with your IP or IP range
    # Or remove this block and rely only on the password
</FilesMatch>
```

### Strong admin password

Set a long random password:

```bash
openssl rand -base64 24
# e.g. K8mXpQ2nLv7rJwYcFtAdEhBs
```

Set it as the `ADMIN_PASSWORD` environment variable (see Section 6).

### Keep PHP and Node.js updated

```bash
sudo apt update && sudo apt upgrade -y
```

### File upload directory

Consider storing uploaded ZIP files temporarily in `/tmp` (already the default) so they are never web-accessible.

### Restrict PHP execution to PHP files only

In your Apache config, ensure `.php` files can only execute inside the web root — not inside `templates/` (which are React static files). This is handled by limiting `<FilesMatch "\.php$">` to the `launchsite/` directory only, not subdirectories that contain pure HTML/JS.

---

## 12. Troubleshooting

### Catalog page shows a PHP parse error

The `data/templates.php` file has a syntax error (can happen after a failed install or delete).

```bash
php -l /var/www/certxa.com/launchsite/data/templates.php
```

If it fails, open the file in a text editor and look for the broken entry near the end. Remove the incomplete block. Each entry should look like:

```php
'template-id' => [
    'id'       => 'template-id',
    'name'     => '...',
    ...
    'type'     => 'react',
],
```

### React template shows 404 or blank page

The SPA routing rewrite rule is not working. Verify:
- Apache: `mod_rewrite` is enabled (`sudo a2enmod rewrite`)
- Apache: `.htaccess` is being read — `AllowOverride All` is set in your VirtualHost `<Directory>` block
- The built template files exist: `ls /var/www/certxa.com/launchsite/templates/{template-id}/`

### Thumbnail not generating (Regen Thumb)

- Verify Chromium is installed: `which chromium-browser`
- Check the `executablePath` in `scripts/src/screenshot.mjs` matches
- Check that `pnpm install` has been run in the `scripts/` directory
- The `www-data` user must be able to execute `node` — check PATH

### ZIP upload fails or times out

- Increase PHP limits (Section 4)
- Check `post_max_size` > `upload_max_filesize`
- Verify disk space: `df -h`
- Check `/tmp` is writable by `www-data`

### pnpm install fails during template upload

- Confirm `pnpm` is in the PATH available to `www-data`
- Check internet access from the server: `curl https://registry.npmjs.org`
- Check disk space: `df -h`

### Admin panel redirects to login after every action

PHP sessions are not persisting. Check:

```bash
php -i | grep session.save_path
# ensure the path exists and is writable
ls -la /var/lib/php/sessions/
sudo chmod 777 /var/lib/php/sessions/   # temporary fix to confirm
```

If that fixes it, set proper ownership instead:

```bash
sudo chown www-data:www-data /var/lib/php/sessions/
sudo chmod 700 /var/lib/php/sessions/
```

---

## Quick Reference

| URL | What it is |
|---|---|
| `certxa.com/launchsite/` | Catalog homepage |
| `certxa.com/launchsite/hair-salons.php` | Hair salon template grid |
| `certxa.com/launchsite/barbershops.php` | Barbershop template grid |
| `certxa.com/launchsite/nail-salons.php` | Nail salon template grid |
| `certxa.com/launchsite/preview.php?id={id}` | Full template preview |
| `certxa.com/launchsite/admin.php` | Admin panel (password protected) |

| File / Directory | Purpose |
|---|---|
| `data/templates.php` | Central template registry — source of truth |
| `assets/img/thumbs/` | Catalog card thumbnails (900×620 JPEG) |
| `templates/{id}/` | Built React SPA files |
| `assets/css/style.css` | Catalog styles |
| `config.php` | `BASE_PATH` constant |
