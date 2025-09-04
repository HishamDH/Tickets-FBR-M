<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @mixin IdeHelperAdvancedSetting
 * @property int $id
 * @property string $key
 * @property array $value
 * @property string|null $description
 * @property string $type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereValue($value)
 */
	class AdvancedSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAnalytics
 * @property int $id
 * @property string $metric_name
 * @property string $metric_type
 * @property array $metric_data
 * @property string $period
 * @property \Illuminate\Support\Carbon $metric_date
 * @property \Illuminate\Support\Carbon $recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics byDateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics byMetric($metricName)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics byPeriod($period)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics query()
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereMetricData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereMetricDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereMetricName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereMetricType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Analytics whereUpdatedAt($value)
 */
	class Analytics extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAnalyticsAlert
 * @property int $id
 * @property string $alert_type
 * @property string $severity
 * @property string $title
 * @property string $message
 * @property array|null $metadata
 * @property bool $is_active
 * @property bool $is_dismissed
 * @property \Illuminate\Support\Carbon $triggered_at
 * @property \Illuminate\Support\Carbon|null $dismissed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert active()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert bySeverity($severity)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereAlertType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereDismissedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereIsDismissed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereTriggeredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereUpdatedAt($value)
 */
	class AnalyticsAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAnalyticsCache
 * @property int $id
 * @property string $cache_key
 * @property string $cache_data
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache whereCacheData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache whereCacheKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsCache whereUpdatedAt($value)
 */
	class AnalyticsCache extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperBooking
 * @property int $id
 * @property string $booking_number
 * @property int|null $customer_id
 * @property int $service_id
 * @property int $merchant_id
 * @property \Illuminate\Support\Carbon $booking_date
 * @property string|null $booking_time
 * @property int|null $guest_count
 * @property string $total_amount
 * @property string $commission_amount
 * @property string $commission_rate
 * @property string $payment_status
 * @property string|null $status
 * @property string $booking_source
 * @property string|null $special_requests
 * @property string|null $cancellation_reason
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $cancelled_by
 * @property string|null $qr_code
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_email
 * @property int|null $number_of_people
 * @property int|null $number_of_tables
 * @property int|null $duration_hours
 * @property string|null $notes
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $customer
 * @property-read float $merchant_amount
 * @property-read string $payment_status_arabic
 * @property-read string $status_arabic
 * @property-read \App\Models\Payment|null $latestPayment
 * @property-read \App\Models\User $merchant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Service $service
 * @method static \Database\Factories\BookingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCancelledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereDurationHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereGuestCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereNumberOfPeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereNumberOfTables($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereSpecialRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperBranch
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUserId($value)
 */
	class Branch extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperCart
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property int $item_id
 * @property string $item_type
 * @property int $quantity
 * @property string $price
 * @property string $discount
 * @property array|null $additional_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $original_total
 * @property-read float $subtotal
 * @property-read float $total_discount
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $item
 * @property-read \App\Models\Offering|null $offering
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Cart forGuest($sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart forSession($sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereAdditionalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserId($value)
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperCategory
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Offering> $offerings
 * @property-read int|null $offerings_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperConversation
 * @property int $id
 * @property string|null $title
 * @property string $type
 * @property string $status
 * @property int $customer_id
 * @property int|null $merchant_id
 * @property int|null $support_agent_id
 * @property int|null $booking_id
 * @property \Illuminate\Support\Carbon|null $last_message_at
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PaidReservation|null $booking
 * @property-read \App\Models\User $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $latestMessage
 * @property-read int|null $latest_message_count
 * @property-read \App\Models\User|null $merchant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User|null $supportAgent
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation active()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation merchantCustomer()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation support()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereSupportAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereUpdatedAt($value)
 */
	class Conversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property array $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereUserId($value)
 */
	class CustomDashboard extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperCustomerRating
 * @property int $id
 * @property int $service_id
 * @property int $user_id
 * @property int $rating Rating from 1 to 5
 * @property string|null $review
 * @property bool $is_approved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $customer
 * @property-read \App\Models\Offering $offering
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereUserId($value)
 */
	class CustomerRating extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperDynamicSetting
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereValue($value)
 */
	class DynamicSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperLog
 * @property int $id
 * @property string $action
 * @property array|null $details
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUserId($value)
 */
	class Log extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperMerchant
 * @property int $id
 * @property int $user_id
 * @property string $business_name
 * @property string $business_type
 * @property string $cr_number
 * @property string|null $business_address
 * @property string $city
 * @property string $verification_status
 * @property string $commission_rate
 * @property int|null $partner_id
 * @property int|null $account_manager_id
 * @property array|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $accountManager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read mixed $slug
 * @property-read mixed $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Offering> $offerings
 * @property-read int|null $offerings_count
 * @property-read \App\Models\Partner|null $partner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MerchantPaymentSetting> $paymentSettings
 * @property-read int|null $payment_settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\MerchantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereAccountManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCrNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereVerificationStatus($value)
 */
	class Merchant extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperMerchantPaymentSetting
 * @property int $id
 * @property int $merchant_id
 * @property int $payment_gateway_id
 * @property bool $is_enabled
 * @property array|null $gateway_credentials
 * @property string|null $custom_fee
 * @property string|null $custom_fee_type
 * @property int $display_order
 * @property array|null $additional_settings
 * @property \Illuminate\Support\Carbon|null $last_tested_at
 * @property bool $test_passed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Merchant $merchant
 * @property-read \App\Models\PaymentGateway $paymentGateway
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereAdditionalSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereCustomFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereCustomFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereGatewayCredentials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereLastTestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting wherePaymentGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereTestPassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereUpdatedAt($value)
 */
	class MerchantPaymentSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperMerchantWallet
 * @property int $id
 * @property int $user_id
 * @property string $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereUserId($value)
 */
	class MerchantWallet extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperMerchantWithdraw
 * @property int $id
 * @property int $merchant_id
 * @property string $amount
 * @property string $status
 * @property array $bank_details
 * @property string|null $transaction_id
 * @property string|null $notes
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon $requested_at
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $account_holder_name
 * @property-read string|null $account_number
 * @property-read string|null $bank_name
 * @property-read int $days_ago
 * @property-read string $formatted_amount
 * @property-read string|null $iban
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read string|null $swift_code
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WithdrawLog> $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\User $merchant
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw approved()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw completed()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw forMerchant($merchantId)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw pending()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw rejected()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw thisYear()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereBankDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereUpdatedAt($value)
 */
	class MerchantWithdraw extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperMessage
 * @property int $id
 * @property int $conversation_id
 * @property int $sender_id
 * @property string $content
 * @property string $type
 * @property array|null $attachments
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property bool $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message notDeleted()
 * @method static \Illuminate\Database\Eloquent\Builder|Message notFrom($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message unread()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperNotification
 * @property int $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $title
 * @property string $message
 * @property array|null $data
 * @property string $priority
 * @property string $channel
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property bool $is_sent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $notifiable
 * @method static \Illuminate\Database\Eloquent\Builder|Notification byPriority($priority)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification read()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification recent($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification unread()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperOffering
 * @property int $id
 * @property string|null $name
 * @property string|null $location
 * @property string|null $description
 * @property string|null $image
 * @property string|null $price
 * @property \Illuminate\Support\Carbon|null $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property string|null $status
 * @property string|null $type
 * @property string|null $category
 * @property array|null $additional_data
 * @property array|null $translations
 * @property bool|null $has_chairs
 * @property int|null $chairs_count
 * @property int $max_capacity
 * @property int $min_capacity
 * @property bool $allow_overbooking
 * @property string $overbooking_percentage
 * @property string $capacity_type
 * @property int $buffer_capacity
 * @property int $user_id
 * @property array|null $features
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read mixed $rating
 * @property-read int|null $reviews_count
 * @property-read mixed $title
 * @property-read \App\Models\Merchant $merchant
 * @property-read \App\Models\Merchant|null $merchantModel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaidReservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerRating> $reviews
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\OfferingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Offering newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offering newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offering query()
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereAdditionalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereAllowOverbooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereBufferCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereCapacityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereChairsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereHasChairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereMaxCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereMinCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereOverbookingPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereTranslations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereUserId($value)
 */
	class Offering extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperOrder
 * @property int $id
 * @property int $user_id
 * @property string $order_number
 * @property string $status
 * @property string $payment_status
 * @property string $payment_method
 * @property string $subtotal
 * @property string $discount
 * @property string $total
 * @property string $currency
 * @property array $billing_details
 * @property array|null $shipping_details
 * @property array|null $payment_details
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $billing_address
 * @property-read string $billing_name
 * @property-read string $payment_method_label
 * @property-read string $payment_status_color
 * @property-read string $payment_status_label
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order confirmed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order paid()
 * @method static \Illuminate\Database\Eloquent\Builder|Order pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order unpaid()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperOrderItem
 * @property int $id
 * @property int $order_id
 * @property int $item_id
 * @property string $item_type
 * @property int $quantity
 * @property string $price
 * @property string $discount
 * @property string $total
 * @property array|null $item_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $additional_data
 * @property-read array|null $branch_info
 * @property-read string $item_name
 * @property-read float $original_total
 * @property-read array $selected_options
 * @property-read float $subtotal
 * @property-read array|null $time_slot
 * @property-read float $total_discount
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $item
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereItemData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereUpdatedAt($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPaidReservation
 * @property int $id
 * @property int $item_id
 * @property string $item_type
 * @property float $quantity
 * @property string $discount
 * @property string $code
 * @property string $status
 * @property int $user_id
 * @property string $booking_date
 * @property string|null $booking_time
 * @property int $guest_count
 * @property string $total_amount
 * @property string $payment_status
 * @property string $reservation_status
 * @property string|null $special_requests
 * @property string|null $qr_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $item
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereBookingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereGuestCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereReservationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereSpecialRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereUserId($value)
 */
	class PaidReservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPartner
 * @property int $id
 * @property int $user_id
 * @property string $partner_code
 * @property string $business_name
 * @property string $contact_person
 * @property string $commission_rate
 * @property string $status
 * @property array|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Merchant> $merchants
 * @property-read int|null $merchants_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner wherePartnerCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUserId($value)
 */
	class Partner extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPayment
 * @property int $id
 * @property string $payment_number
 * @property int $booking_id
 * @property int $merchant_id
 * @property int $payment_gateway_id
 * @property int|null $customer_id
 * @property string $amount
 * @property string $gateway_fee
 * @property string $platform_fee
 * @property string $total_amount
 * @property string $currency
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $gateway_transaction_id
 * @property string|null $gateway_reference
 * @property array|null $gateway_response
 * @property array|null $gateway_metadata
 * @property \Illuminate\Support\Carbon|null $initiated_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $failed_at
 * @property string|null $failure_reason
 * @property string|null $customer_ip
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\User|null $customer
 * @property-read string $status_arabic
 * @property-read \App\Models\Merchant $merchant
 * @property-read \App\Models\PaymentGateway $paymentGateway
 * @method static \Illuminate\Database\Eloquent\Builder|Payment completed()
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Payment failed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCustomerIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInitiatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePlatformFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPaymentGateway
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $display_name_ar
 * @property string $display_name_en
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $provider
 * @property array|null $settings
 * @property string $transaction_fee
 * @property string $fee_type
 * @property bool $is_active
 * @property bool $supports_refund
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $localized_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MerchantPaymentSetting> $merchantSettings
 * @property-read int|null $merchant_settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway active()
 * @method static \Database\Factories\PaymentGatewayFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereDisplayNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereDisplayNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereSupportsRefund($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereTransactionFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereUpdatedAt($value)
 */
	class PaymentGateway extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperRefund
 * @property int $id
 * @property string $refund_reference
 * @property int $payment_id
 * @property int $booking_id
 * @property int $user_id
 * @property string $amount
 * @property string $fee
 * @property string $net_amount
 * @property string $status
 * @property string $type
 * @property string|null $reason
 * @property string|null $admin_notes
 * @property array|null $gateway_response
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read string $type_label
 * @property-read \App\Models\Payment $payment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund query()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereGatewayResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereNetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereRefundReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUserId($value)
 */
	class Refund extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperReport
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperReservation
 * @property int $id
 * @property int $offering_id
 * @property int $user_id
 * @property string $status
 * @property string $reserved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereReservedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUserId($value)
 */
	class Reservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperReview
 * @property int $id
 * @property int $offering_id
 * @property int $user_id
 * @property int $rating Rating from 1 to 5
 * @property string|null $review_text
 * @property string $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereOfferingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereReviewText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperScheduledTask
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask whereUpdatedAt($value)
 */
	class ScheduledTask extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperService
 * @property int $id
 * @property int|null $merchant_id
 * @property string $name
 * @property string $description
 * @property string $location
 * @property string $price
 * @property string|null $base_price
 * @property string $currency
 * @property int|null $duration_hours
 * @property int|null $capacity
 * @property array|null $features
 * @property string $price_type
 * @property string $pricing_model
 * @property string $category
 * @property string $service_type
 * @property string|null $image
 * @property array|null $images
 * @property bool $is_featured
 * @property bool $is_available
 * @property bool $is_active
 * @property string $status
 * @property bool $online_booking_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceAvailability> $availability
 * @property-read int|null $availability_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read string $category_arabic
 * @property-read mixed $price_formatted
 * @property-read string $pricing_model_name
 * @property-read string $service_type_name
 * @property-read \App\Models\User|null $merchant
 * @method static \Illuminate\Database\Eloquent\Builder|Service active()
 * @method static \Database\Factories\ServiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Service featured()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDurationHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereOnlineBookingEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePriceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePricingModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereUpdatedAt($value)
 */
	class Service extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperServiceAvailability
 * @property int $id
 * @property int $service_id
 * @property string $availability_date
 * @property mixed|null $start_time
 * @property mixed|null $end_time
 * @property int|null $available_slots
 * @property int $booked_slots
 * @property int $is_available
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Service $service
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereAvailabilityDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereAvailableSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereBookedSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereUpdatedAt($value)
 */
	class ServiceAvailability extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperStatistics
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics query()
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics whereUpdatedAt($value)
 */
	class Statistics extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperSupport
 * @method static \Illuminate\Database\Eloquent\Builder|Support active()
 * @method static \Illuminate\Database\Eloquent\Builder|Support byCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder|Support newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Support newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Support query()
 */
	class Support extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperSupportTicket
 * @property int $id
 * @property string $ticket_number
 * @property int $user_id
 * @property int|null $assigned_to
 * @property int|null $booking_id
 * @property string $subject
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property string $category
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $first_response_at
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property string|null $resolution_notes
 * @property array|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \App\Models\Booking|null $booking
 * @property-read string $category_label
 * @property-read string $priority_color
 * @property-read string $priority_label
 * @property-read int|null $resolution_time
 * @property-read int|null $response_time
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupportTicketResponse> $responses
 * @property-read int|null $responses_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereFirstResponseAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereTicketNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUserId($value)
 */
	class SupportTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperSupportTicketResponse
 * @property int $id
 * @property int $support_ticket_id
 * @property int $user_id
 * @property string $message
 * @property bool $is_internal
 * @property array|null $attachments
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SupportTicket $supportTicket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereIsInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereSupportTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicketResponse whereUserId($value)
 */
	class SupportTicketResponse extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUser
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
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

namespace App\Models{
/**
 * @mixin IdeHelperWalletTransaction
 * @property int $id
 * @property string $transaction_reference
 * @property int $merchant_wallet_id
 * @property int|null $booking_id
 * @property int|null $payment_id
 * @property string $type
 * @property string $category
 * @property string $amount
 * @property string $balance_after
 * @property string|null $description
 * @property array|null $metadata
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking|null $booking
 * @property-read string $category_label
 * @property-read string $formatted_amount
 * @property-read string $status_color
 * @property-read string $type_color
 * @property-read \App\Models\MerchantWallet $merchantWallet
 * @property-read \App\Models\Payment|null $payment
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereBalanceAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereMerchantWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereUpdatedAt($value)
 */
	class WalletTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperWithdrawLog
 * @property int $id
 * @property int $merchant_withdraw_id
 * @property int $merchant_id
 * @property string $action
 * @property string $amount
 * @property string $status
 * @property int $performed_by
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $action_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $action_color
 * @property-read string $action_label
 * @property-read string $formatted_amount
 * @property-read \App\Models\User $merchant
 * @property-read \App\Models\User $performedBy
 * @property-read \App\Models\MerchantWithdraw $withdrawal
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog byAction($action)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog forMerchant($merchantId)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog forWithdrawal($withdrawalId)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereActionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereMerchantWithdrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog wherePerformedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereUpdatedAt($value)
 */
	class WithdrawLog extends \Eloquent {}
}

