<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'f_name',
        'l_name',
        'email',
        'password',
        'phone',
        'avatar',
        'user_type',
        'status',
        'language',
        'timezone',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'string',
        'notification_preferences' => 'array',
        'push_notifications_enabled' => 'boolean',
        'last_notification_read_at' => 'datetime',
    ];

    /**
     * Accessor for role attribute (alias for user_type)
     */
    public function getRoleAttribute(): string
    {
        return $this->user_type;
    }

    /**
     * Mutator for role attribute (alias for user_type)
     */
    public function setRoleAttribute($value): void
    {
        $this->attributes['user_type'] = $value;
    }

    /**
     * Check if user can access Filament panels
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Handle different panel access requirements
        switch ($panel->getId()) {
            case 'admin':
                return $this->hasRole('Admin') || $this->user_type === 'admin';
            case 'merchant':
                return $this->hasRole('Merchant') || $this->user_type === 'merchant';
            case 'customer':
                return $this->hasRole('Customer') || $this->user_type === 'customer';
            default:
                // Fallback for other panels
                return $this->hasRole(['Admin', 'Merchant', 'Customer']) || 
                       in_array($this->user_type, ['admin', 'merchant', 'customer', 'partner']);
        }
    }

    /**
     * Relationship with Merchant model
     */
    public function merchant(): HasOne
    {
        return $this->hasOne(Merchant::class);
    }

    /**
     * Relationship with Partner model
     */
    public function partner(): HasOne
    {
        return $this->hasOne(Partner::class);
    }

    /**
     * Customer bookings relationship
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    /**
     * Customer bookings relationship (alias)
     */
    public function customerBookings(): HasMany
    {
        return $this->bookings();
    }

    /**
     * Favorite services relationship
     */
    public function favoriteServices(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'user_favorite_services')
                    ->withTimestamps();
    }

    /**
     * Check if user is admin (using Spatie roles or legacy user_type)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin') || $this->user_type === 'admin';
    }

    /**
     * Check if user is merchant (using Spatie roles or legacy user_type)
     */
    public function isMerchant(): bool
    {
        return $this->hasRole('Merchant') || $this->user_type === 'merchant';
    }

    /**
     * Check if user is customer (using Spatie roles or legacy user_type)
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('Customer') || $this->user_type === 'customer';
    }

    /**
     * Sync user's Spatie role with user_type field for backward compatibility
     */
    public function syncRoleWithUserType(): void
    {
        if ($this->user_type && !$this->roles->isNotEmpty()) {
            $roleMapping = [
                'admin' => 'Admin',
                'merchant' => 'Merchant', 
                'customer' => 'Customer'
            ];

            if (isset($roleMapping[$this->user_type])) {
                $this->assignRole($roleMapping[$this->user_type]);
            }
        }
    }

    /**
     * Get primary role name
     */
    public function getPrimaryRole(): ?string
    {
        $firstRole = $this->roles->first();
        return $firstRole ? $firstRole->name : null;
    }

    /**
     * Check if user is partner
     */
    public function isPartner(): bool
    {
        return $this->user_type === 'partner';
    }

    /**
     * Get the branches for the user (merchant).
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get the wallet for the user (merchant).
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(MerchantWallet::class);
    }

    /**
     * Get the shopping cart items for the user.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
