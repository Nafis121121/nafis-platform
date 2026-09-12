<?php

namespace Tests\Feature;

use App\Enums\AiProductDraftStatus;
use App\Models\{AiProductDraft, Category, Product};
use App\Services\AiProductImporterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiProductImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_is_created_and_approved_as_hidden_product(): void
    {
        $category = Category::create(['name_fa' => 'تجهیزات', 'slug' => 'equipment', 'is_active' => true]);
        $draft = app(AiProductImporterService::class)->importFromUrl('https://example.com/products/widget');
        $draft->update([
            'category_id' => $category->id,
            'name' => 'ویجت صنعتی',
            'brand_name' => 'برند آزمایشی',
            'sku' => 'W-100',
            'specifications' => ['voltage' => '220V'],
        ]);

        $product = app(AiProductImporterService::class)->approveDraftToProduct($draft);

        $this->assertSame('draft', $product->status);
        $this->assertSame('hidden', $product->catalog_visibility);
        $this->assertSame($product->id, $draft->refresh()->product_id);
        $this->assertSame(AiProductDraftStatus::IMPORTED, $draft->status);
        $this->assertSame('برند آزمایشی', $product->brand->name_fa);
    }

    public function test_draft_can_update_an_existing_product_and_import_images(): void
    {
        $category = Category::create(['name_fa' => 'تجهیزات', 'slug' => 'equipment', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'محصول قبلی',
            'slug' => 'old-product',
            'status' => 'active',
            'catalog_visibility' => 'visible',
        ]);
        $draft = AiProductDraft::create([
            'source_url' => 'https://example.com/item',
            'status' => AiProductDraftStatus::PENDING,
            'category_id' => $category->id,
            'name' => 'محصول به‌روزشده',
            'short_desc' => 'توضیح جدید',
            'images' => ['https://example.com/image.jpg'],
        ]);

        app(AiProductImporterService::class)->linkDraftToProduct($draft, $product);

        $this->assertSame('محصول به‌روزشده', $product->refresh()->name_fa);
        $this->assertCount(1, $product->images);
        $this->assertSame(AiProductDraftStatus::IMPORTED, $draft->refresh()->status);
    }
}
