<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'menu_items';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'page_id',
        'label_fa',
        'label_en',
        'href',
        'icon',
        'position',
        'visible',
        'open_in_new_tab',
    ];

    protected $casts = [
        'position' => 'integer',
        'visible' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(SiteMenu::class, 'menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('position');
    }

    public function visibleChildren(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->where('visible', true)
            ->orderBy('position');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(SitePage::class, 'page_id');
    }

    public function getComputedUrlAttribute(): string
    {
        if ($this->page) {
            if ($this->page->slug === 'home') {
                return '/';
            }

            return $this->page->route_path ?: '/' . $this->page->slug;
        }

        return $this->href ?? '#';
    }
}
