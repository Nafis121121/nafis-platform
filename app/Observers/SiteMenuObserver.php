<?php
namespace App\Observers;
use App\Models\SiteMenu;
use App\Services\CmsService;
class SiteMenuObserver
{
    public function saved(SiteMenu $menu): void { app(CmsService::class)->clearCache(); }
    public function deleted(SiteMenu $menu): void { app(CmsService::class)->clearCache(); }
}
