<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'quotation_id', 'product_id', 'product_variant_id', 'item_title',
        'technical_description', 'quantity', 'unit_cost_currency', 'unit_price_irr',
        'total_cost_currency', 'total_price_irr',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost_currency' => 'decimal:4',
        'unit_price_irr' => 'decimal:0',
        'total_cost_currency' => 'decimal:4',
        'total_price_irr' => 'decimal:0',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
