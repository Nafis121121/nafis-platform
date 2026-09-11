<?php

namespace App\Models;

use App\Enums\ShipmentMethod;
use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['order_id', 'tracking_code', 'method', 'freight_forwarder', 'bill_of_lading', 'container_tracking_number', 'origin_warehouse_id', 'destination_warehouse_id', 'status', 'etd', 'eta', 'ata', 'freight_cost', 'insurance_cost', 'customs_cost', 'cost_currency', 'notes'];
    protected $casts = [
        'method' => ShipmentMethod::class, 'status' => ShipmentStatus::class,
        'etd' => 'date', 'eta' => 'date', 'ata' => 'date',
        'freight_cost' => 'decimal:4', 'insurance_cost' => 'decimal:4', 'customs_cost' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::creating(fn (Shipment $shipment) => $shipment->tracking_code ??= 'SHP-' . now()->format('Ym') . '-' . strtoupper(str()->random(4)));
    }

    public function originWarehouse(): BelongsTo { return $this->belongsTo(Warehouse::class, 'origin_warehouse_id'); }
    public function destinationWarehouse(): BelongsTo { return $this->belongsTo(Warehouse::class, 'destination_warehouse_id'); }
    public function items(): HasMany { return $this->hasMany(ShipmentItem::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
