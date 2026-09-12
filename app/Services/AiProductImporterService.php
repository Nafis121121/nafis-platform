<?php

namespace App\Services;

use App\Enums\AiProductDraftStatus;
use App\Models\{AiProductDraft, Brand, Product};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AiProductImporterService
{
    public function importFromUrl(string $sourceUrl, ?int $createdBy = null): AiProductDraft
    {
        if (! filter_var($sourceUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('آدرس منبع معتبر نیست.');
        }

        $payload = [
            'source_url' => $sourceUrl,
            'fetched_at' => now()->toIso8601String(),
            'provider' => 'simulated-ai',
        ];
        $name = Str::headline((string) parse_url($sourceUrl, PHP_URL_HOST));

        return AiProductDraft::create([
            'source_url' => $sourceUrl,
            'raw_payload' => $payload,
            'status' => AiProductDraftStatus::PENDING,
            'name' => $name ?: 'پیش‌نویس محصول',
            'name_en' => $name,
            'seo_title' => $name,
            'seo_slug' => Str::slug($name),
            'created_by' => $createdBy,
        ]);
    }

    public function approveDraftToProduct(AiProductDraft $draft, ?int $reviewedBy = null): Product
    {
        if (! $draft->category_id) {
            throw new RuntimeException('برای تایید پیش‌نویس، دسته‌بندی را انتخاب کنید.');
        }

        return DB::transaction(function () use ($draft, $reviewedBy): Product {
            $brand = null;
            if (filled($draft->brand_name)) {
                $brand = Brand::firstOrCreate(
                    ['slug' => Str::slug($draft->brand_name)],
                    ['name_fa' => $draft->brand_name, 'name_en' => $draft->brand_name, 'is_active' => true],
                );
            }

            $slug = $draft->seo_slug ?: $draft->sku ?: $draft->name;
            $slug = Str::slug($slug);
            $baseSlug = $slug;
            $counter = 2;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $product = Product::create([
                'category_id' => $draft->category_id,
                'brand_id' => $brand?->id,
                'name_fa' => $draft->name ?: $draft->name_en ?: 'محصول واردشده',
                'name_en' => $draft->name_en,
                'slug' => $slug,
                'base_sku' => $draft->sku,
                'description_fa' => $draft->long_desc ?: $draft->short_desc,
                'description_en' => $draft->long_desc,
                'metadata' => ['model_number' => $draft->model_number, 'country_of_origin' => $draft->country_of_origin, 'specifications' => $draft->specifications, 'images' => $draft->images],
                'status' => 'draft',
                'catalog_visibility' => 'hidden',
                'seo_title' => $draft->seo_title,
                'seo_slug' => $draft->seo_slug ?: $slug,
                'seo_desc' => $draft->seo_desc,
                'image_alt' => $draft->name,
                'is_active' => false,
            ]);

            $draft->update(['product_id' => $product->id, 'reviewed_by' => $reviewedBy, 'status' => AiProductDraftStatus::IMPORTED]);

            return $product;
        });
    }
}
