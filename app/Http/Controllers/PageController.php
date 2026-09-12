<?php

namespace App\Http\Controllers;

use App\Services\CmsService;
use App\Models\Product;
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
        $featuredProducts = Product::query()
            ->with(['category', 'brand', 'images'])
            ->where('is_active', true)
            ->where('status', 'active')
            ->where('catalog_visibility', 'visible')
            ->where('homepage_featured', true)
            ->latest()
            ->limit(12)
            ->get();

        return view('pages.show', compact('chrome', 'page', 'featuredProducts'));
    }
}