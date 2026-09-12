# Nafis Platform — Phase 1 Stabilization

This package is based on the GitHub snapshot supplied on 2026-09-12.

## Changes in this package

1. Added a real public wholesale catalog:
   - `GET /catalog`
   - `GET /catalog/{product:slug}`
   - Search by Persian/English name or SKU
   - Category and brand filters
   - Only active + visible catalog products are exposed
   - Product detail page and sourcing CTA

2. Media URL stabilization:
   - `MediaAsset::resolved_url` now prefers an actually uploaded `path` over a legacy/external `url`.
   - This prevents seeded placeholder URLs from overriding images uploaded through Filament.

3. Order/inventory observability:
   - Existing behavior is intentionally preserved (an order can still be created if inventory reservation is unavailable).
   - Silent inventory reservation/release failures are now written to Laravel logs with order/product context.
   - A future business decision is still needed: hard-fail vs backorder vs sourcing flow.

4. No destructive database migration was added.
   - Existing local data should remain intact.

## Install over the existing local project

Back up or commit your current work first. Then replace the files from this package in the same project paths.

Run:

```bash
php artisan optimize:clear
php artisan route:list
npm run build
php artisan test
```

Then verify:

- `/`
- `/catalog`
- `/b2b`
- `/requests/new`
- `/contact`
- `/admin`
- `/portal/login`

## Important

Do not run `migrate:fresh` on the working database. It deletes data.
