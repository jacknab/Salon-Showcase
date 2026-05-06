# Certxa Launchit Catalog

A PHP-based website design catalog for Certxa's **Launchit** product — salon owners browse professionally pre-built websites (Hair Salons, Barbershops, Nail Salons), pick a design, connect their domain, and go live. They can optionally edit any text.

## Run & Operate

- `php -S 0.0.0.0:8008 -t /home/runner/workspace/launchsite-php /home/runner/workspace/launchsite-php/router.php` — PHP catalog server (workflow: "LaunchSite PHP Catalog")
- `pnpm --filter @workspace/api-server run dev` — Express API server (port 8080)
- To add a new React template: (1) extract zip to `artifacts/template-{id}/`, (2) install (`pnpm install`), (3) set `base` + `outDir` in `vite.config.ts`, (4) build (`pnpm run build`), (5) register in `data/templates.php` with `type: 'react'` + `react_path`, (6) add card to category page, (7) regenerate thumbnail.
- Required env: `DATABASE_URL` — Postgres connection string (API server only)

## Stack

- **Catalog frontend**: PHP 8.2 (built-in dev server), vanilla CSS, vanilla JS
- **React templates**: React 18 + Vite, built as static SPAs into `launchsite-php/templates/{id}/`
- **API server**: Node.js 24, Express 5, TypeScript 5.9
- DB: PostgreSQL + Drizzle ORM (API server)

## Where things live

- `launchsite-php/` — PHP catalog pages (served at `/launchsite/`)
  - `index.php` — Main catalog page with hero, category cards, How It Works
  - `hair-salons.php`, `barbershops.php`, `nail-salons.php` — Category template grids
  - `preview.php` — Full-page live preview; PHP templates = PHP-rendered site; React templates = full-page iframe
  - `data/templates.php` — **Central template data** (19 templates keyed by ID)
  - `config.php` — `BASE_PATH` constant
  - `router.php` — PHP built-in server router (strips `/launchsite` prefix; serves directory index.html for React SPAs)
  - `assets/css/style.css` — Catalog styles (Certxa dark navy/purple theme)
  - `assets/css/preview.css` — Full-page preview styles (includes `.preview-iframe` for React templates)
  - `assets/img/thumbs/` — JPEG thumbnails (900×620) for all template cards
  - `templates/` — Built React SPAs (e.g. `templates/luxury-nails-spa/`)
  - `generate-thumbs.php` — GD-based thumbnail generator (run from `launchsite-php/`)
- `artifacts/template-{id}/` — Source for each React/Vite template
- `artifacts/api-server/` — Express backend
- `artifacts/launchsite-catalog/` — Artifact registration (routes `/launchsite` → port 8008)

## Architecture decisions

- PHP built-in server with `router.php` strips the `/launchsite` prefix. Static files via `readfile()`; directory requests serve `index.html` (needed for React SPAs).
- Two template types coexist: `type: 'php'` (default) uses PHP-rendered preview in `preview.php`; `type: 'react'` uses an `<iframe>` pointing at `react_path` (`/launchsite/templates/{id}/`).
- React template Vite config: `base: '/launchsite/templates/{id}/'`, `outDir: '../../launchsite-php/templates/{id}'`. TypeScript strict check skipped at build time (workspace @types/react version mismatch) — build script is just `vite build`.
- `data/templates.php` is the single source of truth for all templates. Category pages define their own ID arrays for the card loop; `preview.php` reads from the central file.
- Thumbnails are GD-rendered JPEGs (900×620px). Run `php generate-thumbs.php` from `launchsite-php/` to regenerate all, including new React templates.

## Product

**Launchit** — Pre-built professional salon websites. Salon owners: (1) pick a design, (2) connect their domain, (3) go live — optionally editing any text. Not a website builder. The site is fully built; text editing is optional.

Categories: Hair Salons, Barbershops, Nail Salons. 19 templates total (18 PHP-rendered + 1 React: luxury-nails-spa).

## User preferences

- Service name: **Launchit** (not LaunchSite)
- PHP for the catalog frontend
- React/Vite for real uploaded templates (built as SPAs, iframed in preview)
- React for Part 2: domain setup, optional text editing, account management
- Certxa.com design: dark navy/purple background, purple + orange accents
- Files deployable to certxa.com/launchsite/ on VPS

## Gotchas

- Port 8080 is taken by the API server. PHP catalog uses port 8008.
- PHP router MUST serve static files via `readfile()`, NOT `return false`.
- React SPA router entry: `router.php` checks `is_dir($file)` and serves `index.html` — this must come BEFORE the static file check.
- Production deployment: Apache/Nginx handles routing — no router.php needed. Files go in `/var/www/certxa.com/launchsite/`.
- `preview.php` detects `$is_react` and branches: iframe for React, PHP-rendered site for PHP templates.
- Vite builds for React templates must use `pnpm run build` (not `pnpm run build:all` which runs tsc first).

## Pointers

- See the `pnpm-workspace` skill for workspace structure details
