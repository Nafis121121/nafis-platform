<?php

namespace App\Models;

use App\Enums\CustomerInteractionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerInteraction extends Model
{
    protected $fillable = [
        'customer_id', 'staff_id', 'type', 'summary', 'business_outcome',
        'next_follow_up_at', 'quotation_id', 'order_id', 'sourcing_request_id',
    ];

    protected $casts = [
        'type' => CustomerInteractionType::class,
        'next_follow_up_at' => 'datetime',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function staff(): BelongsTo { return $this->belongsTo(User::class, 'staff_id'); }
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function sourcingRequest(): BelongsTo { return $this->belongsTo(SourcingRequest::class); }
}
