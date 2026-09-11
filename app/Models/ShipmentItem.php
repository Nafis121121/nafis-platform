<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['shipment_id', 'product_id', 'product_variant_id', 'quantity', 'gross_weight_kg', 'volume_cbm', 'carton_count', 'pallet_count'];
    protected $casts = ['quantity' => 'decimal:3', 'gross_weight_kg' => 'decimal:3', 'volume_cbm' => 'decimal:4', 'carton_count' => 'integer', 'pallet_count' => 'integer'];

    public function shipment(): BelongsTo { return $this->belongsTo(Shipment::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
}
