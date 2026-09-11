<?php
namespace App\Enums;
enum PaymentMethod: string { case BANK_TRANSFER='bank_transfer'; case GATEWAY='gateway'; case CASH='cash'; case LETTER_OF_CREDIT='letter_of_credit'; case CHEQUE='cheque'; }
