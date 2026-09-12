# Nafis Laravel V2 — Phase 2 (CMS V2)

## Implemented
- Dynamic Filament Content Block Builder for all current block types.
- Draft vs published data workflow with Publish and Revert actions.
- CMS preview route protected by authentication; preview renders draft data.
- Block ordering via drag/reorder and scheduled visibility using starts_at / ends_at.
- Real Media Manager upload field using Laravel public storage.
- Automatic media MIME type, file size, width and height extraction.
- Media Library selectors inside Hero, Banner Slider and Logos blocks.
- Menu Item relation manager with create/edit/delete, nesting, visibility, page links and ordering.
- Extended per-page SEO fields: canonical URL, OG title and OG description.
- Organization JSON-LD generated in the public layout.
- Hero now actually renders the configured background image and overlay.
- Logos block can render configured logo images and links.

## Important before running
1. `composer install`
2. `npm install`
3. `php artisan storage:link`
4. Review duplicate data before migrations if Phase 1 integrity constraints were not yet applied.
5. `php artisan migrate`
6. `npm run build`
7. `php artisan optimize:clear`

## Intentional next-phase items
- UI V2 / 3D design language, GSAP and selective Three.js.
- Business modules (sourcing requests, tracking, customers, suppliers).
- Roles / permissions and production authorization hardening.
- A richer image transformation pipeline (WebP/AVIF derivatives) and remote object storage support.
