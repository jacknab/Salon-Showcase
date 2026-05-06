# Certxa LaunchSite PHP Catalog

A PHP-based template catalog for Certxa's LaunchSite product — allows salon owners to browse premium pre-made website designs across 3 categories: Hair Salons, Barbershops, and Nail Salons.

## Run & Operate

- `php -S 0.0.0.0:8008 -t /home/runner/workspace/launchsite-php /home/runner/workspace/launchsite-php/router.php` — PHP catalog server (workflow: "LaunchSite PHP Catalog")
- `pnpm --filter @workspace/api-server run dev` — Express API server (port 8080)
- `pnpm run typecheck` — full typecheck across all packages
- Required env: `DATABASE_URL` — Postgres connection string (API server only)

## Stack

- **Catalog frontend**: PHP 8.2 (built-in dev server), vanilla CSS, vanilla JS
- **API server**: Node.js 24, Express 5, TypeScript 5.9
- DB: PostgreSQL + Drizzle ORM (API server)
- Validation: Zod (`zod/v4`), `drizzle-zod`
- API codegen: Orval (from OpenAPI spec)

## Where things live

- `launchsite-php/` — PHP catalog pages (served at `/launchsite/`)
  - `index.php` — Main catalog page (3 category cards)
  - `hair-salons.php` — Hair salon template grid
  - `barbershops.php` — Barbershop template grid
  - `nail-salons.php` — Nail salon template grid
  - `config.php` — `BASE_PATH` constant (change for production)
  - `router.php` — PHP built-in server router (strips `/launchsite` prefix)
  - `includes/header.php`, `includes/footer.php` — Shared layout
  - `assets/css/style.css` — All styles (Certxa dark navy/purple theme)
  - `assets/js/main.js` — Mobile menu + scroll animations
- `artifacts/api-server/` — Express backend
- `artifacts/launchsite-catalog/` — Artifact registration (routes `/launchsite` → port 8008)
- `lib/api-spec/openapi.yaml` — API contract source of truth

## Architecture decisions

- PHP built-in server is used for development. A router.php strips the `/launchsite` prefix since Replit's shared proxy does NOT rewrite paths — services must handle their full base path.
- `config.php` defines `BASE_PATH` so all internal links work both in dev (`/launchsite`) and production (`/launchsite`). Change this constant if the install path changes.
- Static files (CSS/JS) are served manually via `readfile()` in the router — returning `false` from the router causes PHP to use the un-stripped URI (leading to 404s).
- Template data is stored in PHP arrays in each category file. When real templates exist, replace these arrays with DB queries or a JSON data file.

## Product

Salon owners (Hair Salon, Barbershop, Nail Salon) browse a catalog of premium pre-made website designs. Each template card shows a name, style tag, description, feature pills, and hover overlay with "Live Preview" + "Use This Design" buttons. Part 2 (React app) will handle live demo viewing, domain setup, content editing, and account management.

## User preferences

- PHP for the catalog frontend pages
- React for Part 2: live demo viewer, domain config, text editing, subdomain/domain forwarding
- Consistent with certxa.com design: dark navy/purple background, purple + orange accents
- Files should be deployable to certxa.com/launchsite/ on their VPS

## Gotchas

- Port 8080 is taken by the API server. PHP catalog uses port 8008.
- PHP router MUST serve static files via `readfile()`, NOT `return false`. Returning false causes PHP to try the un-stripped path.
- When deploying to production at certxa.com, no router.php needed — Apache/Nginx handles routing. The catalog files go in `/var/www/certxa.com/launchsite/`.
- For production, update `config.php` `BASE_PATH` to `/launchsite` (same as dev, since certxa.com serves it at that path).

## Pointers

- See the `pnpm-workspace` skill for workspace structure, TypeScript setup, and package details
