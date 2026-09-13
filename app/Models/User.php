<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'user_id');
    }

    public function assignedQuotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'assigned_to');
    }

    public function sourcingRequests(): HasMany
    {
        return $this->hasMany(SourcingRequest::class, 'user_id');
    }

    public function assignedSourcingRequests(): HasMany
    {
        return $this->hasMany(SourcingRequest::class, 'assigned_to');
    }

    public function customerInteractions(): HasMany
    {
        return $this->hasMany(CustomerInteraction::class, 'customer_id');
    }

    public function staffInteractions(): HasMany
    {
        return $this->hasMany(CustomerInteraction::class, 'staff_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function isRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'portal') {
            return $this->isRole('customer', 'super_admin');
        }

        if ($panel->getId() === 'admin') {
            return $this->isRole('super_admin', 'sales_manager', 'content_manager', 'warehouse_staff');
        }

        return false;
    }
}
