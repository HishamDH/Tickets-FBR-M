<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->f_name,
            'last_name' => $this->l_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? url('storage/' . $this->avatar) : null,
            'user_type' => $this->user_type,
            'role' => $this->role,
            'status' => $this->status,
            'is_verified' => $this->email_verified_at !== null,
            
            // Personal information
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'full_address' => $this->full_address,
            
            // Business information (for merchants)
            'business_name' => $this->when($this->isMerchant(), $this->business_name),
            'business_type' => $this->when($this->isMerchant(), $this->business_type),
            'business_city' => $this->when($this->isMerchant(), $this->business_city),
            'commercial_registration_number' => $this->when($this->isMerchant(), $this->commercial_registration_number),
            'tax_number' => $this->when($this->isMerchant(), $this->tax_number),
            'merchant_status' => $this->when($this->isMerchant(), $this->merchant_status),
            'is_accepted' => $this->when($this->isMerchant(), $this->is_accepted),
            
            // Branding information (for merchants)
            'subdomain' => $this->when($this->isMerchant(), $this->subdomain),
            'store_url' => $this->when($this->isMerchant() && $this->subdomain, $this->store_url),
            'logo_url' => $this->when($this->isMerchant() && $this->logo_url, url('storage/' . $this->logo_url)),
            'store_description' => $this->when($this->isMerchant(), $this->store_description),
            'store_active' => $this->when($this->isMerchant(), $this->store_active),
            'social_links' => $this->when($this->isMerchant(), $this->social_links),
            'business_hours' => $this->when($this->isMerchant(), $this->business_hours),
            'branding' => $this->when($this->isMerchant(), $this->branding),
            
            // Settings
            'language' => $this->language,
            'timezone' => $this->timezone,
            'notification_preferences' => $this->notification_preferences,
            'push_notifications_enabled' => $this->push_notifications_enabled,
            
            // Timestamps
            'last_login_at' => $this->last_login_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relationships (when loaded)
            'bookings_count' => $this->when($this->relationLoaded('bookings'), $this->bookings_count),
            'merchant_services_count' => $this->when($this->isMerchant() && $this->relationLoaded('merchantServices'), $this->merchant_services_count),
            'wallet' => $this->when($this->isMerchant() && $this->relationLoaded('wallet'), new MerchantWalletResource($this->wallet)),
            
            // Permissions (when loaded)
            'permissions' => $this->when($this->relationLoaded('permissions'), $this->permissions->pluck('name')),
            'roles' => $this->when($this->relationLoaded('roles'), $this->roles->pluck('name')),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'user_type_display' => match ($this->user_type) {
                    'admin' => 'Administrator',
                    'merchant' => 'Merchant',
                    'customer' => 'Customer',
                    'partner' => 'Partner',
                    default => 'Unknown'
                },
                'status_display' => match ($this->status) {
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'suspended' => 'Suspended',
                    'pending' => 'Pending Verification',
                    default => 'Unknown'
                },
                'merchant_status_display' => $this->when($this->isMerchant(), match ($this->merchant_status ?? 'pending') {
                    'approved' => 'Approved',
                    'pending' => 'Pending Approval',
                    'rejected' => 'Rejected',
                    'suspended' => 'Suspended',
                    default => 'Unknown'
                }),
            ]
        ];
    }
}