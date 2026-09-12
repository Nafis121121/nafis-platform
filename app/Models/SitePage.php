<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SitePage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'site_pages';

    protected $fillable = [
        'slug',
        'route_path',
        'title_fa',
        'title_en',
        'summary_fa',
        'summary_en',
        'status',
        'is_system',
        'position',
        'publish_at',
        'published_at',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'noindex',
        'og_image_id',
        'created_by',
    ];

    protected $casts = [
        'status' => ContentStatus::class,
        'is_system' => 'boolean',
        'noindex' => 'boolean',
        'position' => 'integer',
        'publish_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function blocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ContentBlock::class, 'page_id');
    }

    public function publishedBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class, 'page_id')
            ->publishedAndActive()
            ->orderBy('position');
    }

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'og_image_id');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'page_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::PUBLISHED->value);
    }
}
