<?php

namespace App\Models;

use App\Enums\SupplierStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_name', 'country', 'city', 'factory_address', 'contact_person',
        'phone', 'email', 'wechat_id', 'whatsapp', 'rating', 'status',
        'default_currency', 'default_payment_terms', 'evaluation_notes',
    ];

    protected $casts = ['status' => SupplierStatus::class];

    public function products(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }
}
