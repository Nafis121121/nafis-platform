<?php
namespace App\Observers;
use App\Models\SitePage;
use App\Services\CmsService;
class SitePageObserver
{
    public function saved(SitePage $page): void { app(CmsService::class)->clearCache($page->slug); }
    public function deleted(SitePage $page): void { app(CmsService::class)->clearCache($page->slug); }
}
