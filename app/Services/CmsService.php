<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\SiteMenu;
use App\Models\SitePage;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CmsService
{
    public const CHROME_CACHE_KEY = 'cms_site_chrome';
    public const PAGE_CACHE_PREFIX = 'cms_page_';
    public const CACHE_TTL = 60; // 60 seconds

    /**
     * Get site settings, defaults and navigation menus grouped by location.
     */
    public function getSiteChrome(): array
    {
        return Cache::remember(self::CHROME_CACHE_KEY, self::CACHE_TTL, function () {
            $settings = SiteSetting::current();

            $menus = SiteMenu::query()
                ->where('status', ContentStatus::PUBLISHED->value)
                ->with([
                    'items' => fn($q) => $q->where('visible', true)
                        ->whereNull('parent_id')
                        ->with([
                            'children' => fn($cq) => $cq->where('visible', true)->orderBy('position'),
                            'page',
                        ])
                        ->orderBy('position'),
                ])
                ->get();

            $groupedMenus = [];
            foreach ($menus as $menu) {
                $groupedMenus[$menu->location->value] = $menu->items;
            }

            return [
                'enabled' => (bool) $settings->cms_enabled,
                'settings' => $settings->toArray(),
                'menus' => $groupedMenus,
            ];
        });
    }

    /**
     * Get published page and its active published blocks for a given slug.
     */
    public function getPageContent(string $slug): SitePage
    {
        return Cache::remember(self::PAGE_CACHE_PREFIX . $slug, self::CACHE_TTL, function () use ($slug) {
            $page = SitePage::query()
                ->where('slug', $slug)
                ->where('status', ContentStatus::PUBLISHED->value)
                ->with([
                    'publishedBlocks' => fn($q) => $q->orderBy('position'),
                    'ogImage',
                ])
                ->first();

            if (!$page) {
                throw new NotFoundHttpException("صفحه مورد نظر [{$slug}] یافت نشد.");
            }

            return $page;
        });
    }

    /**
     * Clear all CMS caches or for a specific page.
     */
    public function clearCache(?string $slug = null): void
    {
        Cache::forget(self::CHROME_CACHE_KEY);

        if ($slug) {
            Cache::forget(self::PAGE_CACHE_PREFIX . $slug);
        } else {
            foreach (SitePage::pluck('slug') as $s) {
                Cache::forget(self::PAGE_CACHE_PREFIX . $s);
            }
        }
    }
}