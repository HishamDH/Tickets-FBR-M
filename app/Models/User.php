<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $f_name
 * @property string $l_name
 * @property string $name
 * @property string $email
 * @property string|null $business_name
 * @property string|null $commercial_registration_number
 * @property string|null $tax_number
 * @property string $business_type
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $city
 * @property string|null $business_city
 * @property string $merchant_status
 * @property string|null $verification_notes
 * @property string|null $verified_at
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $additional_data
 * @property int $is_accepted
 * @property string $role
 * @property string|null $avatar
 * @property string $user_type
 * @property string $status
 * @property string $language
 * @property string $timezone
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property array|null $notification_preferences
 * @property bool $push_notifications_enabled
 * @property string|null $push_token
 * @property \Illuminate\Support\Carbon|null $last_notification_read_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cart> $carts
 * @property-read int|null $carts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomDashboard> $customDashboards
 * @property-read int|null $custom_dashboards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $customerBookings
 * @property-read int|null $customer_bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversation> $customerConversations
 * @property-read int|null $customer_conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $favoriteServices
 * @property-read int|null $favorite_services_count
 * @property string|null $first_name
 * @property-read string $full_address
 * @property string|null $last_name
 * @property-read \App\Models\Merchant|null $merchant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversation> $merchantConversations
 * @property-read int|null $merchant_conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $merchantServices
 * @property-read int|null $merchant_services_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Partner|null $partner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $sentMessages
 * @property-read int|null $sent_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversation> $supportConversations
 * @property-read int|null $support_conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\MerchantWallet|null $wallet
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdditionalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBusinessCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBusinessType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCommercialRegistrationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastNotificationReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMerchantStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePushNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePushToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerificationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

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
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'last_login_at',
        'business_name',
        'commercial_registration_number',
        'tax_number',
        'business_type',
        'business_city',
        'merchant_status',
        'verification_notes',
        'verified_at',
        'is_accepted',
        'role',
        'additional_data',
        'notification_preferences',
        'push_notifications_enabled',
        'push_token',
        'last_notification_read_at',
        // Password security fields
        'password_changed_at',
        'force_password_reset',
        'failed_login_attempts',
        'locked_until',
        'last_login_ip',
        'last_login_user_agent',
        // Branding and subdomain fields
        'subdomain',
        'branding',
        'logo_url',
        'custom_domain',
        'custom_domain_verified',
        'store_description',
        'social_links',
        'business_hours',
        'store_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_history',
        'failed_login_attempts',
        'locked_until',
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
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_accepted' => 'boolean',
        // Password security casts
        'password_changed_at' => 'datetime',
        'password_history' => 'array',
        'force_password_reset' => 'boolean',
        'failed_login_attempts' => 'integer',
        'locked_until' => 'datetime',
        // Branding and subdomain casts
        'branding' => 'array',
        'social_links' => 'array',
        'business_hours' => 'array',
        'custom_domain_verified' => 'boolean',
        'store_active' => 'boolean',
    ];

    /**
     * Accessor for role attribute (alias for user_type)
     */
    public function getRoleAttribute(): string
    {
        return $this->user_type ?? 'customer';
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
        return $this->hasOne(\App\Models\Merchant::class);
    }

    /**
     * Relationship with Partner model
     */
    public function partner(): HasOne
    {
        return $this->hasOne(\App\Models\Partner::class);
    }

    /**
     * Customer bookings relationship
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(\App\Models\Booking::class, 'customer_id');
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
        return $this->belongsToMany(\App\Models\Service::class, 'user_favorite_services')
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
        if ($this->user_type && $this->roles->isEmpty()) {
            $roleMapping = [
                'admin' => 'Admin',
                'merchant' => 'Merchant',
                'customer' => 'Customer',
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
        return $this->hasMany(\App\Models\Branch::class);
    }

    /**
     * Get the wallet for the user (merchant).
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(\App\Models\MerchantWallet::class);
    }

    /**
     * Get the shopping cart items for the user.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(\App\Models\Cart::class);
    }

    /**
     * Orders relationship
     */
    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * Conversations where user is the customer
     */
    public function customerConversations(): HasMany
    {
        return $this->hasMany(\App\Models\Conversation::class, 'customer_id');
    }

    /**
     * Conversations where user is the merchant
     */
    public function merchantConversations(): HasMany
    {
        return $this->hasMany(\App\Models\Conversation::class, 'merchant_id');
    }

    /**
     * Conversations where user is the support agent
     */
    public function supportConversations(): HasMany
    {
        return $this->hasMany(\App\Models\Conversation::class, 'support_agent_id');
    }

    /**
     * All conversations for this user
     */
    public function allConversations()
    {
        return \App\Models\Conversation::where('customer_id', $this->id)
            ->orWhere('merchant_id', $this->id)
            ->orWhere('support_agent_id', $this->id);
    }

    /**
     * Messages sent by this user
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    /**
     * Get the custom dashboards for the user.
     */
    public function customDashboards(): HasMany
    {
        return $this->hasMany(\App\Models\CustomDashboard::class);
    }

    /**
     * Get the services for the user (if they are a merchant).
     */
    public function merchantServices(): HasMany
    {
        return $this->hasMany(\App\Models\Service::class, 'merchant_id');
    }

    // Accessor and Mutator methods for compatibility with checkout forms
    public function getFirstNameAttribute(): ?string
    {
        return $this->f_name;
    }

    public function setFirstNameAttribute($value): void
    {
        $this->attributes['f_name'] = $value;
        $this->updateFullName();
    }

    public function getLastNameAttribute(): ?string
    {
        return $this->l_name;
    }

    public function setLastNameAttribute($value): void
    {
        $this->attributes['l_name'] = $value;
        $this->updateFullName();
    }

    /**
     * Update full name when first or last name changes
     */
    private function updateFullName(): void
    {
        $this->attributes['name'] = trim(($this->f_name ?? '') . ' ' . ($this->l_name ?? ''));
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            // Ensure name is updated when f_name or l_name changes
            if ($user->isDirty(['f_name', 'l_name'])) {
                $user->name = trim(($user->f_name ?? '') . ' ' . ($user->l_name ?? ''));
            }
        });

        static::created(function ($user) {
            // Sync role with user_type after creation
            $user->syncRoleWithUserType();
        });
    }

    /**
     * Get merchant's employees
     */
    public function employees(): HasMany
    {
        return $this->hasMany(MerchantEmployee::class, 'merchant_id');
    }

    /**
     * Get employee record if user is an employee
     */
    public function employeeRecord(): HasOne
    {
        return $this->hasOne(MerchantEmployee::class, 'employee_id');
    }

    /**
     * Get CSS variables for branding
     */
    public function getBrandCssVariables(): string
    {
        $branding = $this->branding ?? [];
        
        $css = ':root {';
        $css .= '--primary-color: ' . ($branding['primary_color'] ?? '#007bff') . ';';
        $css .= '--secondary-color: ' . ($branding['secondary_color'] ?? '#6c757d') . ';';
        $css .= '--accent-color: ' . ($branding['accent_color'] ?? '#28a745') . ';';
        $css .= '--font-family: ' . ($branding['font_family'] ?? 'Arial') . ', sans-serif;';
        $css .= '}';
        
        return $css;
    }

    /**
     * Check if merchant has a complete branding setup
     */
    public function hasCompleteBranding(): bool
    {
        return !empty($this->subdomain) && 
               !empty($this->branding) && 
               !empty($this->store_description);
    }

    /**
     * Get merchant's store URL
     */
    public function getStoreUrlAttribute(): ?string
    {
        if (!$this->subdomain) {
            return null;
        }
        
        return 'http://' . $this->subdomain . '.' . config('app.main_domain');
    }

    // Password Security Methods

    /**
     * Check if password has expired
     */
    public function isPasswordExpired(): bool
    {
        if (!$this->password_changed_at) {
            return true; // Force password change if never set
        }

        $expiryDays = config('auth.password_expiry_days', 90);
        return $this->password_changed_at->diffInDays(now()) >= $expiryDays;
    }

    /**
     * Check if user account is locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedLoginAttempts(): void
    {
        $this->increment('failed_login_attempts');
        
        $maxAttempts = config('auth.max_login_attempts', 5);
        if ($this->failed_login_attempts >= $maxAttempts) {
            $lockoutDuration = config('auth.lockout_duration', 300); // 5 minutes
            $this->locked_until = now()->addSeconds($lockoutDuration);
            $this->save();
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedLoginAttempts(): void
    {
        $this->failed_login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    /**
     * Add password to history
     */
    public function addPasswordToHistory(string $password): void
    {
        $history = $this->password_history ?? [];
        $history[] = $password;
        
        // Keep only the last N passwords based on config
        $maxHistory = config('auth.password_history_count', 5);
        if (count($history) > $maxHistory) {
            $history = array_slice($history, -$maxHistory);
        }
        
        $this->password_history = $history;
    }

    /**
     * Check if password was used before
     */
    public function wasPasswordUsedBefore(string $password): bool
    {
        if (!$this->password_history) {
            return false;
        }

        foreach ($this->password_history as $oldHash) {
            if (password_verify($password, $oldHash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update login information
     */
    public function updateLoginInfo(string $ip, string $userAgent): void
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ip;
        $this->last_login_user_agent = $userAgent;
        $this->resetFailedLoginAttempts();
    }

    /**
     * Force password reset
     */
    public function forcePasswordReset(): void
    {
        $this->force_password_reset = true;
        $this->save();
    }

    /**
     * Mark password as changed
     */
    public function markPasswordAsChanged(): void
    {
        $this->password_changed_at = now();
        $this->force_password_reset = false;
        $this->save();
    }
}
