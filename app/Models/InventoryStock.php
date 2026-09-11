<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['warehouse_id', 'product_id', 'product_variant_id', 'quantity_on_hand', 'quantity_reserved', 'reorder_point'];
    protected $casts = ['quantity_on_hand' => 'decimal:3', 'quantity_reserved' => 'decimal:3', 'reorder_point' => 'decimal:3'];

    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function movements(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(InventoryMovement::class); }

    public function getAvailableQuantityAttribute(): float
    {
        return max(0, (float) $this->quantity_on_hand - (float) $this->quantity_reserved);
    }
}
