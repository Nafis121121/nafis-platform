<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function reserve(Warehouse $warehouse, Product $product, float $quantity, ?ProductVariant $variant = null, ?User $user = null): InventoryStock
    {
        return DB::transaction(function () use ($warehouse, $product, $quantity, $variant, $user): InventoryStock {
            $stock = $this->stock($warehouse, $product, $variant, true);
            if ($stock->available_quantity < $quantity) {
                throw new RuntimeException('موجودی قابل رزرو کافی نیست.');
            }
            $stock->increment('quantity_reserved', $quantity);
            $this->movement($stock, InventoryMovementType::ADJUSTMENT, 0, $user, 'رزرو موجودی');
            return $stock->refresh();
        });
    }

    public function release(Warehouse $warehouse, Product $product, float $quantity, ?ProductVariant $variant = null, ?User $user = null): InventoryStock
    {
        return DB::transaction(function () use ($warehouse, $product, $quantity, $variant, $user): InventoryStock {
            $stock = $this->stock($warehouse, $product, $variant, true);
            if ((float) $stock->quantity_reserved < $quantity) {
                throw new RuntimeException('مقدار رزروشده برای آزادسازی کافی نیست.');
            }
            $stock->decrement('quantity_reserved', $quantity);
            $this->movement($stock, InventoryMovementType::ADJUSTMENT, 0, $user, 'آزادسازی رزرو');
            return $stock->refresh();
        });
    }

    public function transfer(Warehouse $from, Warehouse $to, Product $product, float $quantity, ?ProductVariant $variant = null, ?User $user = null, ?string $note = null): void
    {
        DB::transaction(function () use ($from, $to, $product, $quantity, $variant, $user, $note): void {
            $source = $this->stock($from, $product, $variant, true);
            if ($source->available_quantity < $quantity) {
                throw new RuntimeException('موجودی قابل انتقال کافی نیست.');
            }
            $destination = $this->stock($to, $product, $variant, true);
            $source->decrement('quantity_on_hand', $quantity);
            $destination->increment('quantity_on_hand', $quantity);
            $this->movement($source, InventoryMovementType::TRANSFER, $quantity, $user, $note ?: 'انتقال خروجی');
            $this->movement($destination, InventoryMovementType::TRANSFER, $quantity, $user, $note ?: 'انتقال ورودی');
        });
    }

    public function add(Warehouse $warehouse, Product $product, float $quantity, ?ProductVariant $variant = null, ?User $user = null, ?string $note = null, object|null $reference = null): InventoryStock
    {
        return DB::transaction(function () use ($warehouse, $product, $quantity, $variant, $user, $note, $reference): InventoryStock {
            $stock = $this->stock($warehouse, $product, $variant, true);
            $stock->increment('quantity_on_hand', $quantity);
            $this->movement($stock, InventoryMovementType::INBOUND, $quantity, $user, $note, $reference);
            return $stock->refresh();
        });
    }

    private function stock(Warehouse $warehouse, Product $product, ?ProductVariant $variant, bool $lock = false): InventoryStock
    {
        $query = InventoryStock::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id);
        if ($lock) {
            $query->lockForUpdate();
        }
        return $query->firstOrCreate([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
        ]);
    }

    private function movement(InventoryStock $stock, InventoryMovementType $type, float $quantity, ?User $user, ?string $note, object|null $reference = null): void
    {
        $stock->movements()->create([
            'warehouse_id' => $stock->warehouse_id,
            'product_id' => $stock->product_id,
            'product_variant_id' => $stock->product_variant_id,
            'type' => $type,
            'quantity' => $quantity,
            'user_id' => $user?->id ?? auth()->id(),
            'note' => $note,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
        ]);
    }
}
