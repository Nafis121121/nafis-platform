<?php
namespace App\Enums;
enum PaymentVerificationStatus: string { case PENDING_VERIFICATION='pending_verification'; case VERIFIED='verified'; case REJECTED='rejected'; }
