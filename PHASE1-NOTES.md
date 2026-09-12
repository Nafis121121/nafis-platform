# Nafis V2 — Phase 1 Foundation

This phase intentionally keeps the current Laravel + Blade + Filament architecture.

## Changes applied
- Moved frontend styling from Tailwind Play CDN to the Vite build pipeline.
- Added Tailwind/PostCSS config and CSS variables for CMS-controlled theme colors.
- Added reduced-motion support.
- Fixed the contact form submit button and added a real POST route.
- Added validated, rate-limited contact message persistence with a honeypot field.
- Aligned Filament Site Settings fields with the actual JSON database schema.
- Added CMS cache invalidation observers for pages, blocks, menus and menu items.
- Added integrity constraints for singleton settings, menu location and per-page block keys.
- Menu page URLs now honor `site_pages.route_path` when present.

## Intentionally deferred to Phase 2+
- Content Block Builder redesign.
- Menu Item Relation Manager.
- Full Media Library/upload workflow.
- Sourcing Request module (`/requests/new`).
- Customer portal / tracking.
- Roles & permissions.
- GSAP / Three.js visual work.

## Local setup after replacing your project
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan test
```

Before running the integrity-constraint migration on an existing production database, confirm there are no duplicate settings/menu locations/block keys.
