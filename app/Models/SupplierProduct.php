<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProduct extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'supplier_id', 'product_id', 'product_variant_id', 'supplier_sku',
        'moq', 'cost_price', 'currency', 'lead_time_days', 'product_url',
    ];

    protected $casts = [
        'cost_price' => 'decimal:4',
        'moq' => 'integer',
        'lead_time_days' => 'integer',
    ];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
}
