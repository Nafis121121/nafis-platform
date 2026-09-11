<?php

namespace App\Http\Controllers;

use App\Services\CmsService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        protected CmsService $cmsService
    ) {}

    public function show(string $slug = 'home'): View
    {
        $chrome = $this->cmsService->getSiteChrome();
        $page = $this->cmsService->getPageContent($slug);

        return view('pages.show', compact('chrome', 'page'));
    }
}