<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case INBOUND = 'inbound';
    case OUTBOUND = 'outbound';
    case TRANSFER = 'transfer';
    case ADJUSTMENT = 'adjustment';
    case LOSS = 'loss';
}
