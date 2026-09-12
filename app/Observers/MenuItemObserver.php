<?php
namespace App\Observers;
use App\Models\MenuItem;
use App\Services\CmsService;
class MenuItemObserver
{
    public function saved(MenuItem $item): void { app(CmsService::class)->clearCache(); }
    public function deleted(MenuItem $item): void { app(CmsService::class)->clearCache(); }
}
