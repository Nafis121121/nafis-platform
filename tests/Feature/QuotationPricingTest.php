<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Quotation;
use App\Services\QuotationPricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class QuotationPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_quotation_recalculates_items_costs_profit_and_total(): void
    {
        $customer = User::factory()->create();
        $category = Category::create([
            'name_fa' => 'کالای دیجیتال',
            'slug' => 'digital',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'محصول آزمایشی',
            'slug' => 'test-product',
            'base_currency' => 'USD',
            'moq' => 1,
            'is_active' => true,
        ]);
        $quotation = Quotation::create([
            'user_id' => $customer->id,
            'base_currency' => 'USD',
            'exchange_rate' => 500000,
            'margin_percentage' => 10,
            'customs_duty_irr' => 1000000,
            'tax_irr' => 500000,
        ]);
        $quotation->items()->create([
            'product_id' => $product->id,
            'item_title' => 'محصول آزمایشی',
            'quantity' => 2,
            'unit_cost_currency' => 100,
        ]);

        app(QuotationPricingService::class)->recalculate($quotation);

        $quotation->refresh();
        $this->assertSame('200.0000', $quotation->subtotal_base_currency);
        $this->assertSame('110000000', $quotation->subtotal_irr);
        $this->assertSame('110000000', $quotation->items->first()->total_price_irr);
        $this->assertSame('111500000', $quotation->final_total_irr);
    }

    public function test_quotation_pdf_download_returns_streamed_response(): void
    {
        $customer = User::factory()->create();
        $quotation = Quotation::create([
            'user_id' => $customer->id,
            'base_currency' => 'USD',
            'exchange_rate' => 500000,
        ]);

        $response = app(\App\Services\QuotationPdfService::class)->download($quotation);

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
    }

    public function test_global_pricing_matrix_is_applied_automatically_when_recalculating(): void
    {
        $customer = User::factory()->create();
        $settings = \App\Models\SiteSetting::current();
        $settings->update([
            'pricing' => [
                'exchange_rate_cny' => 130000,
                'exchange_rate_usd' => 950000,
                'default_margin_percentage' => 20,
                'shipping_rate_per_kg' => 6,
                'shipping_rate_per_cbm' => 200,
            ],
        ]);

        $category = Category::create(['name_fa' => 'پوشاک', 'slug' => 'clothing', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'تی‌شرت عمده',
            'slug' => 'bulk-tshirt',
            'base_currency' => 'CNY',
            'moq' => 10,
            'is_active' => true,
        ]);

        $quotation = Quotation::create([
            'user_id' => $customer->id,
            'base_currency' => 'CNY',
            'exchange_rate' => 0,
            'margin_percentage' => 0,
        ]);

        $quotation->items()->create([
            'product_id' => $product->id,
            'item_title' => 'تی‌شرت عمده',
            'quantity' => 100,
            'unit_cost_currency' => 50,
        ]);

        app(QuotationPricingService::class)->recalculate($quotation);

        $quotation->refresh();
        $this->assertEquals('130000.0000', $quotation->exchange_rate);
        $this->assertEquals('20.0000', $quotation->margin_percentage);
        $this->assertEquals('5000.0000', $quotation->subtotal_base_currency);
        // Items subtotal in IRR (post-margin sale amount): 100 * 7,800,000 = 780,000,000
        $this->assertEquals('780000000', $quotation->subtotal_irr);
        // Unit Price IRR with 20% margin: 50 * 130,000 * 1.2 = 7,800,000
        $this->assertEquals('7800000', $quotation->items->first()->unit_price_irr);
        // Total price item: 100 * 7,800,000 = 780,000,000
        $this->assertEquals('780000000', $quotation->items->first()->total_price_irr);
    }

    public function test_final_total_equals_items_plus_services_without_reapplying_exchange_rate_or_margin(): void
    {
        $customer = User::factory()->create();
        $category = Category::create(['name_fa' => 'ابزار', 'slug' => 'tools', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'دریل صنعتی',
            'slug' => 'industrial-drill',
            'base_currency' => 'USD',
            'moq' => 1,
            'is_active' => true,
        ]);

        $quotation = Quotation::create([
            'user_id' => $customer->id,
            'base_currency' => 'USD',
            'exchange_rate' => 600000,
            'margin_percentage' => 12,
            'shipping_cost_base_currency' => 50,
            'customs_duty_irr' => 2000000,
            'handling_fee_irr' => 500000,
            'tax_irr' => 300000,
        ]);

        $quotation->items()->create([
            'product_id' => $product->id,
            'item_title' => 'دریل صنعتی',
            'quantity' => 3,
            'unit_cost_currency' => 40,
        ]);

        // Manually overridden sale price: must flow through untouched, without
        // the document-level total re-multiplying it by margin or exchange rate.
        $quotation->items()->create([
            'product_id' => $product->id,
            'item_title' => 'دریل صنعتی (نسخه ویژه)',
            'quantity' => 1,
            'unit_cost_currency' => 100,
            'unit_price_irr' => 90000000,
        ]);

        app(QuotationPricingService::class)->recalculate($quotation);
        $quotation->refresh()->load('items');

        $itemsTotal = (float) $quotation->items->sum('total_price_irr');
        $servicesTotal = round(50 * 600000) + 2000000 + 500000;
        $expectedFinal = $itemsTotal + $servicesTotal + 300000;

        $this->assertEquals($itemsTotal, (float) $quotation->subtotal_irr);
        $this->assertEquals($expectedFinal, (float) $quotation->final_total_irr);
        // Sanity check: manual override amount is preserved verbatim.
        $this->assertEquals(90000000, (float) $quotation->items->last()->total_price_irr);
    }

    public function test_admin_quotation_creation_and_immediate_portal_visibility_for_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        $category = Category::create(['name_fa' => 'الکترونیک', 'slug' => 'electronics', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'دوربین مداربسته',
            'slug' => 'cctv-cam',
            'base_currency' => 'USD',
            'moq' => 5,
            'is_active' => true,
        ]);

        // Sourcing request flow
        $sourcingRequest = \App\Models\SourcingRequest::create([
            'user_id' => $customer->id,
            'title' => 'تامین دوربین مداربسته دید در شب',
            'target_currency' => 'USD',
            'estimated_quantity' => 20,
        ]);

        $quotation = app(\App\Services\SourcingService::class)->createQuotation($sourcingRequest);
        $this->assertSame($customer->id, $quotation->user_id);

        $quotation->update([
            'exchange_rate' => 900000,
            'margin_percentage' => 15,
        ]);

        $quotation->items()->create([
            'product_id' => $product->id,
            'item_title' => 'دوربین مداربسته دید در شب',
            'quantity' => 20,
            'unit_cost_currency' => 100,
        ]);

        app(QuotationPricingService::class)->recalculate($quotation);

        // Verify Customer sees this in their portal query
        $this->actingAs($customer);
        $portalQuotations = \App\Filament\Portal\Resources\PortalQuotations\PortalQuotationResource::getEloquentQuery()->get();
        $this->assertCount(1, $portalQuotations);
        $this->assertSame($quotation->id, $portalQuotations->first()->id);
        $this->assertEquals('2070000000', $portalQuotations->first()->final_total_irr);

        // Other customer cannot see it
        $this->actingAs($otherCustomer);
        $otherPortalQuotations = \App\Filament\Portal\Resources\PortalQuotations\PortalQuotationResource::getEloquentQuery()->get();
        $this->assertCount(0, $otherPortalQuotations);
    }
}
