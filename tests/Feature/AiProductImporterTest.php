<?php

namespace Tests\Feature;

use App\Enums\AiProductDraftStatus;
use App\Models\{AiProductDraft, Category};
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
}
