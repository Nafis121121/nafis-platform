<?php

namespace App\Enums;

enum CustomerInteractionType: string
{
    case CALL = 'call';
    case MEETING = 'meeting';
    case EMAIL = 'email';
    case WHATSAPP = 'whatsapp';
    case NOTE = 'note';
}
