<?php

namespace Tests\Feature;

use App\Enums\SourcingRequestStatus;
use App\Enums\SupplierStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\SourcingRequest;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\User;
use App\Services\SourcingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SourcingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_product_cost_and_sourcing_status_history_are_persisted(): void
    {
        $customer = User::factory()->create();
        $category = Category::create(['name_fa' => 'دسته', 'slug' => 'category', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'محصول',
            'slug' => 'product',
            'base_currency' => 'USD',
            'moq' => 1,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'company_name' => 'Factory Co',
            'country' => 'China',
            'city' => 'Shenzhen',
            'status' => SupplierStatus::ACTIVE,
            'default_currency' => 'CNY',
        ]);
        $supplierProduct = SupplierProduct::create([
            'supplier_id' => $supplier->id,
            'product_id' => $product->id,
            'supplier_sku' => 'FAC-001',
            'moq' => 100,
            'cost_price' => 12.5,
            'currency' => 'CNY',
            'lead_time_days' => 20,
        ]);

        $this->assertSame('12.5000', $supplierProduct->refresh()->cost_price);
        $this->assertCount(1, $supplier->products()->get());

        $request = SourcingRequest::create([
            'user_id' => $customer->id,
            'title' => 'محصول جدید',
            'status' => SourcingRequestStatus::PENDING,
            'target_currency' => 'USD',
        ]);

        app(SourcingService::class)->changeStatus($request, SourcingRequestStatus::IN_PROGRESS, 'بررسی کارخانه‌ها آغاز شد.');

        $this->assertSame('in_progress', $request->refresh()->status->value);
        $this->assertSame('pending', $request->statusHistories()->first()->from_status);
        $this->assertSame('in_progress', $request->statusHistories()->first()->to_status);
        $this->assertSame('بررسی کارخانه‌ها آغاز شد.', $request->statusHistories()->first()->note);
    }

    public function test_sourcing_request_can_create_quotation(): void
    {
        $customer = User::factory()->create();
        $request = SourcingRequest::create([
            'user_id' => $customer->id,
            'title' => 'کالای سفارشی',
            'status' => SourcingRequestStatus::IN_PROGRESS,
            'target_currency' => 'AED',
        ]);

        $quotation = app(SourcingService::class)->createQuotation($request);

        $this->assertSame($customer->id, $quotation->user_id);
        $this->assertSame($quotation->id, $request->refresh()->quotation_id);
        $this->assertSame('quoted', $request->status->value);
    }
}
