<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'media_assets';

    protected $fillable = [
        'bucket',
        'folder',
        'disk',
        'path',
        'url',
        'title',
        'alt_fa',
        'alt_en',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'uploaded_by',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(SitePage::class, 'og_image_id');
    }

    public function getResolvedUrlAttribute(): string
    {
        // Prefer a real uploaded file over a legacy/external URL. Seeded media
        // records may contain placeholder URLs that do not exist locally.
        if (!empty($this->path)) {
            if (($this->disk ?? 'public') === 'public') {
                return '/storage/' . ltrim($this->path, '/');
            }

            return Storage::disk($this->disk ?? 'public')->url($this->path);
        }

        if (!empty($this->url)) {
            return $this->url;
        }

        return '';
    }
}
