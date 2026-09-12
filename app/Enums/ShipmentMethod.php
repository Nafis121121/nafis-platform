<?php

namespace App\Enums;

enum ShipmentMethod: string
{
    case SEA = 'sea';
    case AIR = 'air';
    case LAND = 'land';
    case EXPRESS = 'express';
}
