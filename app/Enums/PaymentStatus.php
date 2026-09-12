<?php
namespace App\Enums;
enum PaymentStatus: string { case UNPAID='unpaid'; case PARTIALLY_PAID='partially_paid'; case FULLY_PAID='fully_paid'; case REFUNDED='refunded'; }
