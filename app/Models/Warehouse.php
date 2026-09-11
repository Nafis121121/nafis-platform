<?php

namespace App\Models;

use App\Enums\WarehouseType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'type', 'country', 'city', 'address', 'manager_name', 'phone', 'is_active'];
    protected $casts = ['type' => WarehouseType::class, 'is_active' => 'boolean'];

    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class); }
    public function originShipments(): HasMany { return $this->hasMany(Shipment::class, 'origin_warehouse_id'); }
    public function destinationShipments(): HasMany { return $this->hasMany(Shipment::class, 'destination_warehouse_id'); }
}
