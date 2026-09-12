<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LogisticsService
{
    public function updateStatus(Shipment $shipment, ShipmentStatus $status): Shipment
    {
        if ($status === ShipmentStatus::DELIVERED_TO_WAREHOUSE || $status === ShipmentStatus::COMPLETED) {
            return $this->receiveShipmentIntoWarehouse($shipment, $status);
        }
        $shipment->update(['status' => $status]);
        return $shipment->refresh();
    }

    public function receiveShipmentIntoWarehouse(Shipment $shipment, ShipmentStatus $finalStatus = ShipmentStatus::DELIVERED_TO_WAREHOUSE): Shipment
    {
        return DB::transaction(function () use ($shipment, $finalStatus): Shipment {
            $shipment->loadMissing(['items.product', 'items.variant', 'destinationWarehouse']);
            $alreadyReceived = $shipment->items->contains(
                fn ($item): bool => ($item->variant ?: $item->product)->inventoryMovements()
                    ->where('reference_type', Shipment::class)
                    ->where('reference_id', $shipment->id)
                    ->where('warehouse_id', $shipment->destination_warehouse_id)
                    ->exists()
            );
            if ($alreadyReceived) {
                if ($shipment->status !== $finalStatus) {
                    $shipment->update(['status' => $finalStatus]);
                }
                return $shipment;
            }
            $inventory = app(InventoryService::class);
            foreach ($shipment->items as $item) {
                $inventory->add(
                    $shipment->destinationWarehouse,
                    $item->product,
                    (float) $item->quantity,
                    $item->variant,
                    auth()->user(),
                    'دریافت محموله ' . $shipment->tracking_code,
                    $shipment,
                );
            }
            $shipment->update(['status' => $finalStatus, 'ata' => $shipment->ata ?: now()->toDateString()]);
            return $shipment->refresh();
        });
    }
}
