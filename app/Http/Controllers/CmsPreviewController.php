<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\SitePage;
use App\Services\CmsService;
use Illuminate\View\View;

class CmsPreviewController extends Controller
{
    public function __construct(protected CmsService $cmsService) {}

    public function show(SitePage $page): View
    {
        $chrome = $this->cmsService->getSiteChrome();
        $page->load([
            'blocks' => fn ($query) => $query
                ->where('status', '!=', ContentStatus::ARCHIVED->value)
                ->orderBy('position'),
            'ogImage',
        ]);

        return view('pages.preview', compact('chrome', 'page'));
    }
}
