<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourcingRequestStatusHistory extends Model
{
    protected $fillable = ['from_status', 'to_status', 'changed_by', 'note'];

    public function request(): BelongsTo
    {
        return $this->belongsTo(SourcingRequest::class, 'sourcing_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
