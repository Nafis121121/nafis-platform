<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SitePage;
use App\Services\CmsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(protected CmsService $cmsService) {}

    public function index(Request $request): View
    {
        $chrome = $this->cmsService->getSiteChrome();

        $products = Product::query()
            ->with(['category', 'brand'])
            ->where('is_active', true)
            ->where('status', 'active')
            ->where('catalog_visibility', 'visible')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->string('q'));
                $query->where(function ($q) use ($term) {
                    $q->where('name_fa', 'like', "%{$term}%")
                        ->orWhere('name_en', 'like', "%{$term}%")
                        ->orWhere('base_sku', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($cq) => $cq->where('slug', $request->string('category'))))
            ->when($request->filled('brand'), fn ($q) => $q->whereHas('brand', fn ($bq) => $bq->where('slug', $request->string('brand'))))
            ->latest()
            ->paginate(18)
            ->withQueryString();

        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name_fa')->get();
        $brands = Brand::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name_fa')->get();
        $page = $this->catalogPage('کاتالوگ عمده کالا', 'catalog');

        return view('pages.catalog.index', compact('chrome', 'page', 'products', 'categories', 'brands'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active && $product->status === 'active' && $product->catalog_visibility === 'visible', 404);

        $chrome = $this->cmsService->getSiteChrome();
        $product->load(['category', 'brand', 'variants']);
        $page = $this->catalogPage($product->seo_title ?: $product->name_fa, 'catalog/' . $product->slug);
        $page->seo_description = $product->seo_desc ?: $product->description_fa;

        return view('pages.catalog.show', compact('chrome', 'page', 'product'));
    }

    private function catalogPage(string $title, string $slug): SitePage
    {
        return new SitePage([
            'slug' => $slug,
            'route_path' => '/' . $slug,
            'title_fa' => $title,
            'status' => ContentStatus::PUBLISHED,
            'seo_title' => $title . ' | نفیس تجارت',
            'seo_description' => 'کاتالوگ عمده کالا و خدمات تأمین نفیس تجارت',
            'noindex' => false,
        ]);
    }
}
