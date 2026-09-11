<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CmsService;
use Illuminate\Http\JsonResponse;

class CmsApiController extends Controller
{
    public function __construct(
        protected CmsService $cmsService
    ) {}

    public function chrome(): JsonResponse
    {
        $chrome = $this->cmsService->getSiteChrome();

        return response()->json([
            'success' => true,
            'data' => $chrome,
        ]);
    }

    public function page(string $slug): JsonResponse
    {
        $page = $this->cmsService->getPageContent($slug);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'route_path' => $page->route_path,
                'title_fa' => $page->title_fa,
                'title_en' => $page->title_en,
                'summary_fa' => $page->summary_fa,
                'summary_en' => $page->summary_en,
                'seo_title' => $page->seo_title,
                'seo_description' => $page->seo_description,
                'seo_keywords' => $page->seo_keywords,
                'noindex' => $page->noindex,
                'og_image_url' => $page->ogImage?->resolved_url,
                'published_at' => $page->published_at?->toIso8601String(),
                'blocks' => $page->publishedBlocks->map(fn($b) => [
                    'id' => $b->id,
                    'block_key' => $b->block_key,
                    'type' => $b->type->value,
                    'type_label' => $b->type->label(),
                    'position' => $b->position,
                    'data' => $b->published_data,
                ]),
            ],
        ]);
    }
}