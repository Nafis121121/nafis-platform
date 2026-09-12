<?php
namespace App\Enums;
enum OrderPaymentType: string { case DEPOSIT='deposit'; case MILESTONE='milestone'; case FINAL_BALANCE='final_balance'; }
