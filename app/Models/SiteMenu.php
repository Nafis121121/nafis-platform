<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\MenuLocation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteMenu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'site_menus';

    protected $fillable = [
        'name',
        'location',
        'status',
    ];

    protected $casts = [
        'location' => MenuLocation::class,
        'status' => ContentStatus::class,
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->orderBy('position');
    }

    public function rootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')
            ->whereNull('parent_id')
            ->orderBy('position');
    }

    public function visibleRootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')
            ->whereNull('parent_id')
            ->where('visible', true)
            ->with(['children' => fn($q) => $q->where('visible', true)->orderBy('position')])
            ->orderBy('position');
    }

    public static function forLocation(MenuLocation|string $location): ?self
    {
        $value = $location instanceof MenuLocation ? $location->value : $location;
        return static::query()->where('location', $value)->first();
    }
}
