# Shubak Tickets Platform - Laravel/Filament Technical Specification

## 1. PROJECT OVERVIEW & CORE FUNCTIONALITY

### Primary Business Purpose
Shubak Tickets is a comprehensive Arabic-language event management platform that serves as a marketplace connecting customers with service providers for various types of events, venues, restaurants, and experiences. The platform operates on a commission-based model with integrated payment processing, contract management, and multi-role user systems.

### Target User Personas & Role Definitions

#### 1. Customers (العملاء)
- **Primary Role**: Book event services and experiences
- **Key Features**: Browse services, make bookings, manage payments, leave reviews
- **Journey**: Discovery → Booking → Payment → Experience → Review

#### 2. Merchants/Service Providers (مزودو الخدمات)
- **Primary Role**: Offer and manage event-related services
- **Key Features**: Service catalog management, booking calendar, financial tracking, team management
- **Journey**: Registration → Setup → Service Creation → Booking Management → Revenue Collection

#### 3. Partners/Affiliates (الشركاء)
- **Primary Role**: Refer new merchants and earn commissions
- **Key Features**: Referral tracking, commission management, promotional tools
- **Journey**: Registration → Link Generation → Referral → Commission Tracking → Payout

#### 4. Platform Staff (موظفو المنصة)
- **Primary Role**: Platform administration and oversight
- **Key Features**: User management, financial oversight, content moderation, system configuration
- **Journey**: Login → Dashboard Review → Task Management → Decision Making → Monitoring

### Core Feature Inventory (Priority Levels)

#### High Priority (MVP)
- Multi-role authentication system
- Service catalog with categories (venues, catering, photography, beauty, entertainment, transportation, security, flowers/invitations)
- Booking management with calendar integration
- Payment processing (Stripe integration)
- Basic admin panel for platform management

#### Medium Priority
- Contract management with electronic signatures
- Review and rating system
- Notification system (email, SMS)
- Partner/affiliate system
- Financial reporting and analytics

#### Low Priority (Future Enhancements)
- Advanced AI analytics
- Multi-language support
- Mobile app API
- Advanced fraud detection

## 2. SYSTEM ARCHITECTURE & TECHNICAL STACK

### Proposed Laravel v10 Architecture

```php
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Customer/
│   │   ├── Merchant/
│   │   ├── Partner/
│   │   └── Api/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Services/
├── Repositories/
├── Jobs/
├── Events/
├── Listeners/
├── Notifications/
└── Filament/
    ├── Resources/
    ├── Pages/
    ├── Widgets/
    └── Clusters/
```

### Design Patterns Implementation

#### Repository Pattern
```php
interface BookingRepositoryInterface
{
    public function findByMerchant(int $merchantId): Collection;
    public function findByCustomer(int $customerId): Collection;
    public function findByDateRange(Carbon $start, Carbon $end): Collection;
}

class BookingRepository implements BookingRepositoryInterface
{
    public function __construct(private Booking $model) {}
    
    public function findByMerchant(int $merchantId): Collection
    {
        return $this->model->where('merchant_id', $merchantId)->get();
    }
}
```

#### Service Layer Pattern
```php
class BookingService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private PaymentService $paymentService,
        private NotificationService $notificationService
    ) {}
    
    public function createBooking(array $data): Booking
    {
        DB::transaction(function () use ($data) {
            $booking = $this->bookingRepository->create($data);
            $this->paymentService->processPayment($booking);
            $this->notificationService->sendBookingConfirmation($booking);
            return $booking;
        });
    }
}
```

## 3. FRONTEND COMPATIBILITY ANALYSIS

### Current Frontend Assessment
The existing React-based frontend uses:
- **UI Framework**: Custom components with Radix UI primitives
- **Styling**: Tailwind CSS with custom gradients and animations
- **State Management**: React hooks and local state
- **Routing**: Client-side routing with view state management

### Laravel Blade Migration Strategy

#### Compatible Elements (Direct Migration)
- **Forms**: All form components can be converted to Blade with Laravel Form Request validation
- **Tables**: Data tables can use Laravel pagination and Blade components
- **Cards**: Layout components translate directly to Blade partials
- **Modals**: Can be implemented with Alpine.js or Livewire components

#### Custom Development Required

##### 1. Interactive Calendar Component
```php
// Custom Blade component for booking calendar
// resources/views/components/booking-calendar.blade.php
<div x-data="bookingCalendar()" class="booking-calendar">
    <div class="calendar-grid">
        @foreach($availableDates as $date)
            <button 
                @click="selectDate('{{ $date }}')"
                class="calendar-day {{ $date->isAvailable ? 'available' : 'unavailable' }}"
            >
                {{ $date->format('d') }}
            </button>
        @endforeach
    </div>
</div>

<script>
function bookingCalendar() {
    return {
        selectedDate: null,
        selectDate(date) {
            this.selectedDate = date;
            // Fetch available packages for this date
        }
    }
}
</script>
```

##### 2. Real-time Dashboard Widgets
```php
// Livewire component for real-time stats
class RealtimeStats extends Component
{
    public $totalBookings;
    public $todayRevenue;
    
    protected $listeners = ['bookingCreated' => 'updateStats'];
    
    public function mount()
    {
        $this->updateStats();
    }
    
    public function updateStats()
    {
        $this->totalBookings = Booking::count();
        $this->todayRevenue = Booking::whereDate('created_at', today())->sum('amount');
    }
    
    public function render()
    {
        return view('livewire.realtime-stats');
    }
}
```

### Filament v3 UI Coverage

#### Covered by Filament (90% compatibility)
- **Forms**: All form fields and validation
- **Tables**: Advanced filtering, sorting, bulk actions
- **Navigation**: Multi-level navigation with role-based access
- **Widgets**: Charts, stats cards, recent activity
- **Modals**: Create, edit, view modals
- **Notifications**: Toast notifications and alerts

#### Custom Filament Components Required

##### 1. Calendar Widget for Merchant Dashboard
```php
class BookingCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.booking-calendar';
    
    public function getViewData(): array
    {
        return [
            'availableDates' => $this->getAvailableDates(),
            'bookings' => $this->getBookingsForMonth(),
        ];
    }
    
    private function getAvailableDates(): Collection
    {
        return ServiceAvailability::where('merchant_id', auth()->user()->merchant->id)
            ->where('available_date', '>=', now())
            ->get();
    }
}
```

##### 2. QR Code Scanner Component
```php
class QrScannerPage extends Page
{
    protected static string $view = 'filament.pages.qr-scanner';
    
    public function scanTicket(string $qrCode): void
    {
        $booking = Booking::where('qr_code', $qrCode)->first();
        
        if (!$booking) {
            Notification::make()
                ->title('تذكرة غير صالحة')
                ->danger()
                ->send();
            return;
        }
        
        $booking->update(['status' => 'checked_in']);
        
        Notification::make()
            ->title('تم تسجيل الدخول بنجاح')
            ->success()
            ->send();
    }
}
```

## 4. DATABASE DESIGN & IMPLEMENTATION

### Complete ERD Structure

#### Core Tables

##### Users Table
```php
// Migration: create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('phone', 20)->nullable();
    $table->string('avatar')->nullable();
    $table->enum('user_type', ['customer', 'merchant', 'partner', 'admin']);
    $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
    $table->string('language', 5)->default('ar');
    $table->string('timezone', 50)->default('Asia/Riyadh');
    $table->rememberToken();
    $table->timestamps();
    
    $table->index(['user_type', 'status']);
    $table->index('email');
});
```

##### Merchants Table
```php
// Migration: create_merchants_table.php
Schema::create('merchants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('business_name');
    $table->string('business_type', 100);
    $table->string('cr_number', 50)->unique();
    $table->text('business_address')->nullable();
    $table->string('city', 100);
    $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->decimal('commission_rate', 5, 2)->default(3.00);
    $table->foreignId('partner_id')->nullable()->constrained('partners')->onDelete('set null');
    $table->foreignId('account_manager_id')->nullable()->constrained('users')->onDelete('set null');
    $table->json('settings')->nullable();
    $table->timestamps();
    
    $table->index(['verification_status', 'city']);
});
```

##### Services Table
```php
// Migration: create_services_table.php
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->text('description')->nullable();
    $table->enum('category', [
        'venues', 'catering', 'photography', 'beauty', 
        'entertainment', 'transportation', 'security', 'flowers_invitations'
    ]);
    $table->enum('service_type', ['package', 'individual', 'addon']);
    $table->enum('pricing_model', ['fixed', 'per_person', 'per_hour']);
    $table->decimal('base_price', 10, 2);
    $table->string('currency', 3)->default('SAR');
    $table->integer('duration_hours')->nullable();
    $table->integer('capacity')->nullable();
    $table->json('features')->nullable();
    $table->json('images')->nullable();
    $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
    $table->boolean('online_booking_enabled')->default(false);
    $table->timestamps();
    
    $table->index(['category', 'status']);
    $table->index(['merchant_id', 'online_booking_enabled']);
});
```

##### Bookings Table
```php
// Migration: create_bookings_table.php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->string('booking_number', 20)->unique();
    $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('service_id')->constrained()->onDelete('cascade');
    $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
    $table->date('booking_date');
    $table->time('booking_time')->nullable();
    $table->integer('guest_count')->nullable();
    $table->decimal('total_amount', 10, 2);
    $table->decimal('commission_amount', 10, 2);
    $table->decimal('commission_rate', 5, 2);
    $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
    $table->enum('booking_status', [
        'pending', 'confirmed', 'completed', 'cancelled', 'no_show'
    ])->default('pending');
    $table->enum('booking_source', ['online', 'manual', 'pos'])->default('online');
    $table->text('special_requests')->nullable();
    $table->text('cancellation_reason')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
    $table->string('qr_code')->unique()->nullable();
    $table->timestamps();
    
    $table->index(['booking_date', 'booking_status']);
    $table->index(['merchant_id', 'payment_status']);
    $table->index('booking_number');
});
```

### Eloquent Model Relationships

```php
// User Model
class User extends Authenticatable implements FilamentUser
{
    use HasRoles, HasPermissions;
    
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar', 
        'user_type', 'status', 'language', 'timezone'
    ];
    
    public function merchant(): HasOne
    {
        return $this->hasOne(Merchant::class);
    }
    
    public function partner(): HasOne
    {
        return $this->hasOne(Partner::class);
    }
    
    public function customerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }
    
    public function canAccessFilament(): bool
    {
        return in_array($this->user_type, ['admin', 'merchant', 'partner']);
    }
}

// Merchant Model
class Merchant extends Model
{
    protected $fillable = [
        'user_id', 'business_name', 'business_type', 'cr_number',
        'business_address', 'city', 'verification_status', 
        'commission_rate', 'partner_id', 'account_manager_id', 'settings'
    ];
    
    protected $casts = [
        'settings' => 'array',
        'commission_rate' => 'decimal:2'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
    
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}

// Service Model
class Service extends Model
{
    protected $fillable = [
        'merchant_id', 'name', 'description', 'category', 'service_type',
        'pricing_model', 'base_price', 'currency', 'duration_hours',
        'capacity', 'features', 'images', 'status', 'online_booking_enabled'
    ];
    
    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'base_price' => 'decimal:2',
        'online_booking_enabled' => 'boolean'
    ];
    
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
    
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    
    public function availability(): HasMany
    {
        return $this->hasMany(ServiceAvailability::class);
    }
}
```

## 5. BACKEND SERVICES & BUSINESS LOGIC

### Controller Structure

#### Customer Controllers
```php
class CustomerBookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private PaymentService $paymentService
    ) {}
    
    public function index(Request $request)
    {
        $bookings = auth()->user()->customerBookings()
            ->with(['service', 'merchant'])
            ->latest()
            ->paginate(10);
            
        return view('customer.bookings.index', compact('bookings'));
    }
    
    public function store(BookingRequest $request)
    {
        $booking = $this->bookingService->createBooking($request->validated());
        
        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with('success', 'تم إنشاء الحجز بنجاح');
    }
}
```

#### Merchant Controllers
```php
class MerchantDashboardController extends Controller
{
    public function dashboard()
    {
        $merchant = auth()->user()->merchant;
        
        $stats = [
            'total_bookings' => $merchant->bookings()->count(),
            'today_revenue' => $merchant->bookings()
                ->whereDate('created_at', today())
                ->sum('total_amount'),
            'pending_bookings' => $merchant->bookings()
                ->where('booking_status', 'pending')
                ->count(),
        ];
        
        return view('merchant.dashboard', compact('stats'));
    }
}
```

### Service Layer Implementation

#### Booking Service
```php
class BookingService
{
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            // 1. Create booking
            $booking = Booking::create([
                'booking_number' => $this->generateBookingNumber(),
                'customer_id' => auth()->id(),
                'service_id' => $data['service_id'],
                'merchant_id' => $data['merchant_id'],
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'guest_count' => $data['guest_count'],
                'total_amount' => $data['total_amount'],
                'commission_amount' => $this->calculateCommission($data['total_amount']),
                'commission_rate' => config('app.default_commission_rate', 3.0),
                'special_requests' => $data['special_requests'] ?? null,
                'qr_code' => $this->generateQrCode(),
            ]);
            
            // 2. Update service availability
            $this->updateServiceAvailability($booking);
            
            // 3. Generate contract if required
            if ($booking->service->requires_contract) {
                $this->contractService->generateContract($booking);
            }
            
            // 4. Send notifications
            $this->notificationService->sendBookingNotifications($booking);
            
            return $booking;
        });
    }
    
    private function calculateCommission(float $amount): float
    {
        $rate = config('app.default_commission_rate', 3.0);
        return ($amount * $rate) / 100;
    }
}
```

#### Payment Service
```php
class PaymentService
{
    public function __construct(private StripeService $stripe) {}
    
    public function processPayment(Booking $booking, array $paymentData): Payment
    {
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => $paymentData['method'],
            'payment_gateway' => 'stripe',
            'amount' => $booking->total_amount,
            'currency' => 'SAR',
            'status' => 'pending',
        ]);
        
        try {
            $stripePayment = $this->stripe->createPaymentIntent([
                'amount' => $booking->total_amount * 100, // Convert to halalas
                'currency' => 'sar',
                'metadata' => [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                ],
            ]);
            
            $payment->update([
                'gateway_transaction_id' => $stripePayment->id,
                'status' => 'completed',
                'processed_at' => now(),
            ]);
            
            $booking->update(['payment_status' => 'paid']);
            
            return $payment;
        } catch (Exception $e) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => ['error' => $e->getMessage()],
            ]);
            
            throw $e;
        }
    }
}
```

## 6. AUTHENTICATION & AUTHORIZATION

### Laravel Authentication Implementation

#### Multi-Role Authentication
```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'merchant' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
```

#### Permission System using Spatie Laravel Permission
```php
// Database Seeder
class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $customerRole = Role::create(['name' => 'customer']);
        $merchantRole = Role::create(['name' => 'merchant']);
        $partnerRole = Role::create(['name' => 'partner']);
        $adminRole = Role::create(['name' => 'admin']);
        
        // Customer permissions
        $customerPermissions = [
            'view_own_bookings',
            'create_bookings',
            'cancel_own_bookings',
            'view_own_profile',
            'update_own_profile',
        ];
        
        // Merchant permissions
        $merchantPermissions = [
            'manage_own_services',
            'view_own_bookings',
            'manage_calendar',
            'view_financial_reports',
            'manage_team_members',
            'access_merchant_dashboard',
        ];
        
        // Create and assign permissions
        foreach ($customerPermissions as $permission) {
            $perm = Permission::create(['name' => $permission]);
            $customerRole->givePermissionTo($perm);
        }
        
        foreach ($merchantPermissions as $permission) {
            $perm = Permission::create(['name' => $permission]);
            $merchantRole->givePermissionTo($perm);
        }
    }
}
```

#### Middleware for Role-Based Access
```php
class EnsureMerchantAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'merchant') {
            abort(403, 'Access denied');
        }
        
        if (auth()->user()->merchant->verification_status !== 'approved') {
            return redirect()->route('merchant.verification-pending');
        }
        
        return $next($request);
    }
}
```

## 7. FILAMENT ADMIN PANEL SPECIFICATIONS

### Resource Definitions

#### Booking Resource
```php
class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'إدارة الحجوزات';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('معلومات الحجز')
                ->schema([
                    Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->required()
                        ->searchable(),
                    Select::make('service_id')
                        ->relationship('service', 'name')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => 
                            $set('merchant_id', Service::find($state)?->merchant_id)
                        ),
                    DatePicker::make('booking_date')
                        ->required()
                        ->minDate(now()),
                    TimePicker::make('booking_time'),
                    TextInput::make('guest_count')
                        ->numeric()
                        ->minValue(1),
                ]),
            Section::make('تفاصيل الدفع')
                ->schema([
                    TextInput::make('total_amount')
                        ->numeric()
                        ->prefix('ر.س')
                        ->required(),
                    Select::make('payment_status')
                        ->options([
                            'pending' => 'بانتظار الدفع',
                            'paid' => 'مدفوع',
                            'failed' => 'فشل الدفع',
                            'refunded' => 'مسترد',
                        ]),
                ]),
            Section::make('ملاحظات')
                ->schema([
                    Textarea::make('special_requests')
                        ->maxLength(1000),
                ]),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable(),
                TextColumn::make('service.name')
                    ->label('الخدمة')
                    ->limit(30),
                TextColumn::make('booking_date')
                    ->label('تاريخ الحجز')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('المبلغ')
                    ->money('SAR'),
                BadgeColumn::make('booking_status')
                    ->label('حالة الحجز')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                SelectFilter::make('booking_status')
                    ->options([
                        'pending' => 'بانتظار التأكيد',
                        'confirmed' => 'مؤكد',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                    ]),
                DateFilter::make('booking_date'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_qr')
                    ->label('إنشاء QR')
                    ->icon('heroicon-o-qr-code')
                    ->action(fn (Booking $record) => $record->generateQrCode()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('export')
                    ->label('تصدير')
                    ->action(fn (Collection $records) => Excel::download(
                        new BookingsExport($records), 
                        'bookings.xlsx'
                    )),
            ]);
    }
}
```

#### Merchant Resource
```php
class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'إدارة التجار';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('معلومات التاجر')
                ->schema([
                    TextInput::make('business_name')
                        ->label('اسم النشاط التجاري')
                        ->required(),
                    TextInput::make('business_type')
                        ->label('نوع النشاط')
                        ->required(),
                    TextInput::make('cr_number')
                        ->label('رقم السجل التجاري')
                        ->unique(ignoreRecord: true)
                        ->required(),
                    Textarea::make('business_address')
                        ->label('عنوان النشاط'),
                    TextInput::make('city')
                        ->label('المدينة')
                        ->required(),
                ]),
            Section::make('إعدادات الحساب')
                ->schema([
                    Select::make('verification_status')
                        ->label('حالة التحقق')
                        ->options([
                            'pending' => 'بانتظار المراجعة',
                            'approved' => 'موافق عليه',
                            'rejected' => 'مرفوض',
                        ])
                        ->required(),
                    TextInput::make('commission_rate')
                        ->label('نسبة العمولة (%)')
                        ->numeric()
                        ->step(0.01)
                        ->minValue(0)
                        ->maxValue(100),
                    Select::make('partner_id')
                        ->label('الشريك المرتبط')
                        ->relationship('partner.user', 'name')
                        ->searchable(),
                ]),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business_name')
                    ->label('اسم النشاط')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('اسم المالك')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('المدينة')
                    ->sortable(),
                BadgeColumn::make('verification_status')
                    ->label('حالة التحقق')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                TextColumn::make('commission_rate')
                    ->label('العمولة')
                    ->suffix('%'),
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('verification_status')
                    ->options([
                        'pending' => 'بانتظار المراجعة',
                        'approved' => 'موافق عليه',
                        'rejected' => 'مرفوض',
                    ]),
                SelectFilter::make('city'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Merchant $record) => $record->verification_status === 'pending')
                    ->action(function (Merchant $record) {
                        $record->update(['verification_status' => 'approved']);
                        Notification::make()
                            ->title('تم قبول التاجر')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
```

### Dashboard Widgets

#### Revenue Chart Widget
```php
class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'الإيرادات الشهرية';
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        $data = Booking::selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات (ر.س)',
                    'data' => $data->pluck('revenue')->toArray(),
                    'backgroundColor' => '#f97316',
                ],
            ],
            'labels' => $data->map(fn ($item) => 
                now()->month($item->month)->format('F')
            )->toArray(),
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
}
```

#### Booking Stats Widget
```php
class BookingStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الحجوزات', Booking::count())
                ->description('جميع الحجوزات في النظام')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('حجوزات اليوم', Booking::whereDate('created_at', today())->count())
                ->description('الحجوزات الجديدة اليوم')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('الإيرادات الشهرية', 
                'ر.س ' . number_format(
                    Booking::whereMonth('created_at', now()->month)
                        ->where('payment_status', 'paid')
                        ->sum('total_amount'), 2
                )
            )
                ->description('إيرادات الشهر الحالي')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
```

### Custom Filament Pages

#### Merchant Verification Page
```php
class MerchantVerificationPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static string $view = 'filament.pages.merchant-verification';
    protected static ?string $title = 'مراجعة التجار';
    
    public function mount(): void
    {
        abort_unless(auth()->user()->can('manage_merchants'), 403);
    }
    
    public function approveMerchant(int $merchantId): void
    {
        $merchant = Merchant::findOrFail($merchantId);
        $merchant->update(['verification_status' => 'approved']);
        
        // Send approval notification
        $merchant->user->notify(new MerchantApprovedNotification($merchant));
        
        Notification::make()
            ->title('تم قبول التاجر بنجاح')
            ->success()
            ->send();
    }
    
    public function rejectMerchant(int $merchantId, string $reason): void
    {
        $merchant = Merchant::findOrFail($merchantId);
        $merchant->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
        
        $merchant->user->notify(new MerchantRejectedNotification($merchant, $reason));
        
        Notification::make()
            ->title('تم رفض التاجر')
            ->warning()
            ->send();
    }
}
```

## 8. TECHNICAL REQUIREMENTS & DEPLOYMENT

### Required Composer Packages

```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.0",
        "filament/filament": "^3.0",
        "spatie/laravel-permission": "^5.0",
        "spatie/laravel-media-library": "^10.0",
        "stripe/stripe-php": "^10.0",
        "laravel/cashier": "^14.0",
        "barryvdh/laravel-dompdf": "^2.0",
        "pusher/pusher-php-server": "^7.0",
        "laravel/horizon": "^5.0",
        "spatie/laravel-backup": "^8.0",
        "spatie/laravel-activitylog": "^4.0",
        "filament/spatie-laravel-media-library-plugin": "^3.0",
        "doctrine/dbal": "^3.0",
        "maatwebsite/excel": "^3.1",
        "livewire/livewire": "^3.0",
        "alpinejs/alpine": "^3.0"
    },
    "require-dev": {
        "pestphp/pest": "^2.0",
        "pestphp/pest-plugin-laravel": "^2.0",
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "nunomaduro/collision": "^7.0",
        "spatie/laravel-ignition": "^2.0"
    }
}
```

### Environment Configuration

```env
# Application
APP_NAME="Shubak Tickets"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://shubaktickets.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shubak_tickets
DB_USERNAME=shubak_user
DB_PASSWORD=secure_password

# Payment Gateway
STRIPE_KEY=pk_live_your_stripe_key
STRIPE_SECRET=sk_live_your_stripe_secret
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key

# SMS
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=+1234567890

# Queue
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# File Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=shubak-tickets-storage
```

### Laravel Artisan Commands

#### Setup Command
```php
class SetupPlatformCommand extends Command
{
    protected $signature = 'shubak:setup';
    protected $description = 'Setup the Shubak Tickets platform';
    
    public function handle()
    {
        $this->info('Setting up Shubak Tickets Platform...');
        
        // Run migrations
        $this->call('migrate');
        
        // Seed roles and permissions
        $this->call('db:seed', ['--class' => 'RolePermissionSeeder']);
        
        // Create admin user
        $this->call('shubak:create-admin');
        
        // Setup storage links
        $this->call('storage:link');
        
        // Clear caches
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        
        $this->info('Platform setup completed successfully!');
    }
}
```

#### Create Admin Command
```php
class CreateAdminCommand extends Command
{
    protected $signature = 'shubak:create-admin {--email=} {--password=}';
    protected $description = 'Create an admin user';
    
    public function handle()
    {
        $email = $this->option('email') ?: $this->ask('Admin email');
        $password = $this->option('password') ?: $this->secret('Admin password');
        
        $user = User::create([
            'name' => 'Platform Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'user_type' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        $user->assignRole('admin');
        
        $this->info("Admin user created successfully: {$email}");
    }
}
```

### Critical Dependencies
1. **Payment Gateway Setup**: Stripe account and Saudi Arabia compliance
2. **SMS Provider**: Twilio or local Saudi SMS provider
3. **Email Service**: SendGrid or similar reliable provider
4. **File Storage**: AWS S3 or similar cloud storage
5. **SSL Certificate**: For secure payment processing

### Risk Assessment & Mitigation

#### High Risk
- **Payment Integration Complexity**: Mitigation - Start with Stripe, add local gateways later
- **Multi-tenancy Performance**: Mitigation - Implement proper indexing and caching strategy
- **Arabic RTL Support**: Mitigation - Use proven RTL CSS frameworks and test extensively

#### Medium Risk
- **File Upload Security**: Mitigation - Implement strict validation and virus scanning
- **Database Performance**: Mitigation - Proper indexing and query optimization
- **Third-party API Dependencies**: Mitigation - Implement fallback mechanisms

#### Low Risk
- **UI Component Compatibility**: Mitigation - Filament provides most required components
- **Authentication System**: Mitigation - Laravel's built-in auth is robust and well-tested

### Development Team Requirements
- **1 Senior Laravel Developer** (Lead)
- **1 Filament Specialist** (Admin Panel)
- **1 Frontend Developer** (Blade/Alpine.js)
- **1 DevOps Engineer** (Deployment & Infrastructure)
- **1 QA Engineer** (Testing & Quality Assurance)

### Estimated Total Development Time: 23-26 weeks

This specification provides a complete blueprint for implementing the Shubak Tickets platform using Laravel v10 and Filament v3, maintaining all the sophisticated functionality while leveraging Laravel's robust ecosystem and Filament's powerful admin interface capabilities.