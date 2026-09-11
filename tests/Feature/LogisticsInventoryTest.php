<?php

namespace Tests\Feature;

use App\Enums\ShipmentStatus;
use App\Enums\WarehouseType;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Warehouse;
use App\Services\LogisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogisticsInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiving_shipment_adds_stock_and_movement_once(): void
    {
        $category = Category::create(['name_fa' => 'دسته', 'slug' => 'logistics-category', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'محصول حمل‌شده',
            'slug' => 'shipped-product',
            'base_currency' => 'USD',
            'moq' => 1,
            'is_active' => true,
        ]);
        $origin = Warehouse::create(['name' => 'مبدأ', 'type' => WarehouseType::ORIGIN, 'country' => 'China', 'is_active' => true]);
        $destination = Warehouse::create(['name' => 'مقصد', 'type' => WarehouseType::DESTINATION, 'country' => 'Iran', 'is_active' => true]);
        $shipment = Shipment::create([
            'method' => 'sea',
            'origin_warehouse_id' => $origin->id,
            'destination_warehouse_id' => $destination->id,
            'status' => ShipmentStatus::IN_TRANSIT_ORIGIN,
        ]);
        $shipment->items()->create(['product_id' => $product->id, 'quantity' => 25]);

        app(LogisticsService::class)->receiveShipmentIntoWarehouse($shipment);
        app(LogisticsService::class)->receiveShipmentIntoWarehouse($shipment->refresh());

        $this->assertSame('delivered_to_warehouse', $shipment->refresh()->status->value);
        $this->assertSame('25.000', $destination->stocks()->first()->quantity_on_hand);
        $this->assertDatabaseCount('inventory_movements', 1);
    }
}
