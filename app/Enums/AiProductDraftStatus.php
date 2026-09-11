<?php

namespace App\Enums;

enum AiProductDraftStatus: string
{
    case PENDING = 'pending';
    case REVIEWING = 'reviewing';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case IMPORTED = 'imported';
}
