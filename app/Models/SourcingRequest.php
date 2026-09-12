<?php

namespace App\Models;

use App\Enums\SourcingRequestStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourcingRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reference_code', 'user_id', 'assigned_to', 'quotation_id', 'status',
        'title', 'technical_specifications', 'required_standards',
        'estimated_quantity', 'target_price', 'target_currency', 'attachments',
        'follow_up_notes', 'result_notes',
    ];

    protected $casts = [
        'status' => SourcingRequestStatus::class,
        'target_price' => 'decimal:4',
        'estimated_quantity' => 'integer',
        'attachments' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (SourcingRequest $request): void {
            $request->reference_code ??= 'SRC-' . now()->format('Ym') . '-' . strtoupper(str()->random(4));
        });
        static::updated(function (SourcingRequest $request): void {
            if (! $request->wasChanged('status')) {
                return;
            }
            $old = $request->getOriginal('status');
            $old = $old instanceof SourcingRequestStatus ? $old->value : $old;
            $new = $request->status instanceof SourcingRequestStatus ? $request->status->value : $request->status;
            $request->statusHistories()->create([
                'from_status' => $old,
                'to_status' => $new,
                'changed_by' => auth()->id(),
            ]);
        });
    }

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function statusHistories(): HasMany { return $this->hasMany(SourcingRequestStatusHistory::class); }
}
