<?php

namespace App\Models;

use App\Enums\AiProductDraftStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiProductDraft extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'source_url', 'raw_payload', 'status', 'name', 'name_en', 'brand_name',
        'sku', 'model_number', 'country_of_origin', 'short_desc', 'long_desc',
        'specifications', 'images', 'seo_title', 'seo_slug', 'seo_desc',
        'category_id', 'product_id', 'created_by', 'reviewed_by',
    ];

    protected $casts = [
        'status' => AiProductDraftStatus::class,
        'raw_payload' => 'array',
        'specifications' => 'array',
        'images' => 'array',
    ];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
