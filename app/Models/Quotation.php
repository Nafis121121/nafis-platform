<?php

namespace App\Models;

use App\Enums\QuotationCurrency;
use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reference_code', 'user_id', 'assigned_to', 'status', 'base_currency',
        'shipping_currency', 'exchange_rate', 'subtotal_base_currency', 'subtotal_irr',
        'total_foreign_currency',
        'shipping_cost_base_currency', 'shipping_weight_kg', 'shipping_volume_cbm',
        'shipping_rate_per_kg', 'shipping_rate_per_cbm', 'customs_duty_irr',
        'customs_rate_per_kg_irr', 'inland_shipping_irr', 'unforeseen_cost_irr',
        'inspection_fee_base_currency', 'handling_fee_irr', 'margin_percentage',
        'profit_type', 'profit_fixed_irr',
        'total_profit_irr', 'tax_irr', 'final_total_irr', 'payment_terms',
        'valid_until', 'internal_notes', 'customer_notes',
    ];

    protected $casts = [
        'status' => QuotationStatus::class,
        'base_currency' => QuotationCurrency::class,
        'shipping_currency' => QuotationCurrency::class,
        'exchange_rate' => 'decimal:4',
        'subtotal_base_currency' => 'decimal:4',
        'subtotal_irr' => 'decimal:0',
        'total_foreign_currency' => 'decimal:4',
        'shipping_cost_base_currency' => 'decimal:4',
        'shipping_weight_kg' => 'decimal:3',
        'shipping_volume_cbm' => 'decimal:4',
        'shipping_rate_per_kg' => 'decimal:4',
        'shipping_rate_per_cbm' => 'decimal:4',
        'customs_duty_irr' => 'decimal:0',
        'customs_rate_per_kg_irr' => 'decimal:0',
        'inland_shipping_irr' => 'decimal:0',
        'unforeseen_cost_irr' => 'decimal:0',
        'inspection_fee_base_currency' => 'decimal:4',
        'handling_fee_irr' => 'decimal:0',
        'margin_percentage' => 'decimal:4',
        'profit_fixed_irr' => 'decimal:0',
        'total_profit_irr' => 'decimal:0',
        'tax_irr' => 'decimal:0',
        'final_total_irr' => 'decimal:0',
        'valid_until' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation): void {
            $quotation->reference_code ??= 'QT-' . now()->format('Ym') . '-' . strtoupper(str()->random(4));
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function sourcingRequests(): HasMany
    {
        return $this->hasMany(SourcingRequest::class);
    }
}
