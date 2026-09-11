<?php

namespace App\Models;

use App\Enums\BlockType;
use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentBlock extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'content_blocks';

    protected $fillable = [
        'page_id',
        'block_key',
        'type',
        'draft_data',
        'published_data',
        'status',
        'position',
        'starts_at',
        'ends_at',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'type' => BlockType::class,
        'status' => ContentStatus::class,
        'draft_data' => 'array',
        'published_data' => 'array',
        'position' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(SitePage::class, 'page_id');
    }

    public function publish(): bool
    {
        $this->published_data = $this->draft_data ?? $this->published_data ?? [];
        $this->status = ContentStatus::PUBLISHED;
        $this->published_at = now();

        return $this->save();
    }

    public function revertToPublished(): bool
    {
        $this->draft_data = $this->published_data;
        return $this->save();
    }

    public function scopePublishedAndActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('status', ContentStatus::PUBLISHED->value)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }
}
