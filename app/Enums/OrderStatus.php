<?php
namespace App\Enums;
enum OrderStatus: string {
    case PENDING_PAYMENT='pending_payment'; case DEPOSIT_PAID='deposit_paid'; case IN_PROCUREMENT='in_procurement';
    case IN_PRODUCTION='in_production'; case SHIPPED='shipped'; case IN_CUSTOMS='in_customs';
    case READY_FOR_DELIVERY='ready_for_delivery'; case COMPLETED='completed'; case CANCELLED='cancelled';
}
