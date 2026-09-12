<?php

namespace App\Observers;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;

class MediaAssetObserver
{
    public function saving(MediaAsset $asset): void
    {
        if (blank($asset->path)) {
            return;
        }

        $disk = $asset->disk ?: 'public';
        $storage = Storage::disk($disk);

        if (! $storage->exists($asset->path)) {
            return;
        }

        $asset->mime_type = $storage->mimeType($asset->path) ?: $asset->mime_type;
        $asset->size_bytes = $storage->size($asset->path) ?: $asset->size_bytes;

        if ($disk === 'public') {
            $absolutePath = $storage->path($asset->path);
            if (is_file($absolutePath)) {
                $dimensions = @getimagesize($absolutePath);
                if (is_array($dimensions)) {
                    $asset->width = $dimensions[0] ?? $asset->width;
                    $asset->height = $dimensions[1] ?? $asset->height;
                }
            }
        }
    }
}
