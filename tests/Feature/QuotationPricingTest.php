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
        $this->assertSame('100000000', $quotation->subtotal_irr);
        $this->assertSame('110000000', $quotation->items->first()->total_price_irr);
        $this->assertSame('111600000', $quotation->final_total_irr);
    }
}
