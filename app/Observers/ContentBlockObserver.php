<?php
namespace App\Observers;
use App\Models\ContentBlock;
use App\Services\CmsService;
class ContentBlockObserver
{
    public function saved(ContentBlock $block): void { $this->clear($block); }
    public function deleted(ContentBlock $block): void { $this->clear($block); }
    private function clear(ContentBlock $block): void
    {
        $slug = $block->page()->value('slug');
        app(CmsService::class)->clearCache($slug ?: null);
    }
}
