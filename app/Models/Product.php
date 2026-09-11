<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name_fa',
        'name_en',
        'slug',
        'base_sku',
        'description_fa',
        'description_en',
        'base_currency',
        'base_price',
        'moq',
        'metadata',
        'is_active',
        'status', 'catalog_visibility', 'seo_title', 'seo_slug', 'seo_desc', 'image_alt',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'moq' => 'integer',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('created_at');
    }

    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function supplierProducts(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function inventoryStocks(): HasMany { return $this->hasMany(InventoryStock::class); }
    public function inventoryMovements(): HasMany { return $this->hasMany(InventoryMovement::class); }
    public function shipmentItems(): HasMany { return $this->hasMany(ShipmentItem::class); }
    public function wholesaleRules(): HasMany { return $this->hasMany(WholesaleRule::class); }
}
