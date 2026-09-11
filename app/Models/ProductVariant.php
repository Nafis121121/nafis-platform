<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'sku',
        'name_fa',
        'name_en',
        'option_values',
        'price_currency',
        'price',
        'moq',
        'weight_kg',
        'is_active',
    ];

    protected $casts = [
        'option_values' => 'array',
        'price' => 'decimal:2',
        'moq' => 'integer',
        'weight_kg' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function quotationItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function supplierProducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function inventoryStocks(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(InventoryStock::class); }
    public function inventoryMovements(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(InventoryMovement::class); }
    public function shipmentItems(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(ShipmentItem::class); }
}
