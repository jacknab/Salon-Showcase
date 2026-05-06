# Certxa Launchit Catalog

A PHP-based website design catalog for Certxa's **Launchit** product — salon owners browse professionally pre-built websites (Hair Salons, Barbershops, Nail Salons), pick a design, connect their domain, and go live. They can optionally edit any text.

## Run & Operate

- `php -S 0.0.0.0:8008 -t /home/runner/workspace/launchsite-php /home/runner/workspace/launchsite-php/router.php` — PHP catalog server (workflow: "LaunchSite PHP Catalog")
- `pnpm --filter @workspace/api-server run dev` — Express API server (port 8080)
- Required env: `DATABASE_URL` — Postgres connection string (API server only)

## Stack

- **Catalog frontend**: PHP 8.2 (built-in dev server), vanilla CSS, vanilla JS
- **API server**: Node.js 24, Express 5, TypeScript 5.9
- DB: PostgreSQL + Drizzle ORM (API server)

## Where things live

- `launchsite-php/` — PHP catalog pages (served at `/launchsite/`)
  - `index.php` — Main catalog page with hero, category cards, How It Works
  - `hair-salons.php`, `barbershops.php`, `nail-salons.php` — Category template grids
  - `preview.php` — Full-page live preview of any template (`?id=template-id`)
  - `data/templates.php` — **Central template data** (all 18 templates keyed by ID)
  - `config.php` — `BASE_PATH` constant
  - `router.php` — PHP built-in server router (strips `/launchsite` prefix)
  - `includes/header.php`, `includes/footer.php` — Shared layout (preview.php skips the nav/footer)
  - `includes/template-preview.php` — `render_template_preview()` — miniature scroll preview for cards
  - `assets/css/style.css` — Catalog styles (Certxa dark navy/purple theme)
  - `assets/css/preview.css` — Full-page preview styles
  - `assets/js/main.js` — Mobile menu + scroll-pan hover effect + progress bar
- `artifacts/api-server/` — Express backend
- `artifacts/launchsite-catalog/` — Artifact registration (routes `/launchsite` → port 8008)

## Architecture decisions

- PHP built-in server with `router.php` strips the `/launchsite` prefix (Replit proxy doesn't rewrite paths). Static files served via `readfile()` — returning `false` from the router uses the un-stripped URI and causes 404s.
- `data/templates.php` is the single source of truth for all 18 templates. Category pages define their own arrays for the card loop; `preview.php` uses the central data file.
- Preview page (`preview.php`) renders a full real-looking website for each template using the template's accent/dark/light color variables and category-appropriate copy.
- Cards use a CSS hover-scroll effect: `.preview-scroll.is-scrolling` pans the simulated mini-site over 9s; a purple→orange progress bar fills in sync via CSS `@keyframes`.

## Product

**Launchit** — Pre-built professional salon websites. Salon owners: (1) pick a design, (2) connect their domain, (3) go live — optionally editing any text. Not a website builder. The site is fully built; text editing is optional.

Categories: Hair Salons, Barbershops, Nail Salons. 18 templates total.

## User preferences

- Service name: **Launchit** (not LaunchSite)
- PHP for the catalog frontend
- React for Part 2: domain setup, optional text editing, account management
- Certxa.com design: dark navy/purple background, purple + orange accents
- Files deployable to certxa.com/launchsite/ on VPS

## Gotchas

- Port 8080 is taken by the API server. PHP catalog uses port 8008.
- PHP router MUST serve static files via `readfile()`, NOT `return false`.
- Production deployment: Apache/Nginx handles routing — no router.php needed. Files go in `/var/www/certxa.com/launchsite/`.
- `preview.php` skips the normal site header/footer and renders its own chrome bar instead.

## Pointers

- See the `pnpm-workspace` skill for workspace structure details
