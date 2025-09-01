<?php

namespace App\Filament\Merchant\Pages\Auth;

use App\Models\Merchant;
use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getPersonalInformationFormComponent(),
                        $this->getBusinessInformationFormComponent(),
                        $this->getPasswordFormComponent(),
                    ])
                    ->statePath('data')
            ),
        ];
    }

    protected function getPersonalInformationFormComponent(): Component
    {
        return Section::make('Personal Information')
            ->description('Your personal details for the merchant account')
            ->schema([
                TextInput::make('f_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('l_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                $this->getEmailFormComponent(),
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(20),
            ]);
    }

    protected function getBusinessInformationFormComponent(): Component
    {
        return Section::make('Business Information')
            ->description('Details about your business')
            ->schema([
                TextInput::make('business_name')
                    ->label('Business Name')
                    ->required()
                    ->maxLength(255),
                
                Select::make('business_type')
                    ->label('Business Type')
                    ->required()
                    ->options([
                        'restaurant' => 'Restaurant',
                        'hotel' => 'Hotel',
                        'spa' => 'Spa & Wellness',
                        'entertainment' => 'Entertainment',
                        'events' => 'Events & Venues',
                        'tours' => 'Tours & Activities',
                        'retail' => 'Retail',
                        'services' => 'Professional Services',
                        'other' => 'Other',
                    ]),
                
                TextInput::make('cr_number')
                    ->label('Commercial Registration Number')
                    ->required()
                    ->maxLength(50)
                    ->unique(Merchant::class, 'cr_number'),
                
                Textarea::make('business_address')
                    ->label('Business Address')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                
                TextInput::make('city')
                    ->label('City')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return Section::make('Account Security')
            ->description('Choose a secure password for your account')
            ->schema([
                TextInput::make('password')
                    ->label(__('filament-panels::pages/auth/register.form.password.label'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->rule(Password::default())
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->same('passwordConfirmation')
                    ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                
                TextInput::make('passwordConfirmation')
                    ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->dehydrated(false)
                    ->validationAttribute(__('filament-panels::pages/auth/register.form.password_confirmation.validation_attribute')),
            ]);
    }

    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        // Create the user first
        $user = User::create([
            'name' => $data['f_name'] . ' ' . $data['l_name'], // Combine first and last name
            'f_name' => $data['f_name'],
            'l_name' => $data['l_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'user_type' => 'merchant',
            'role' => 'merchant', // Set the role field as well
            'status' => 'active', // User account is active, but merchant status is pending
            'language' => 'ar', // Default language
            'timezone' => 'Asia/Riyadh', // Default timezone
            'is_accepted' => false, // Not accepted until merchant is approved
        ]);

        // Assign merchant role
        $user->assignRole('merchant');

        // Create the merchant record with pending status
        Merchant::create([
            'user_id' => $user->id,
            'business_name' => $data['business_name'],
            'business_type' => $data['business_type'],
            'cr_number' => $data['cr_number'],
            'business_address' => $data['business_address'],
            'city' => $data['city'],
            'verification_status' => 'pending',
            'commission_rate' => 3.00, // Default commission rate
        ]);

        event(new Registered($user));

        // Show success notification instead of logging in
        Notification::make()
            ->title('Registration Successful!')
            ->body('Your merchant application has been submitted successfully. Please wait for admin approval before you can access your dashboard.')
            ->success()
            ->persistent()
            ->send();

        // Return a custom registration response that redirects to login
        return new class implements RegistrationResponse {
            public function toResponse($request)
            {
                return redirect('/merchant/login');
            }
        };
    }

    protected function getLoginUrl(): string
    {
        return '/merchant/login';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Registration submitted for approval';
    }

    public function getTitle(): string
    {
        return 'Register as Merchant';
    }

    public function getHeading(): string
    {
        return 'Register as Merchant';
    }

    public function getSubheading(): ?string
    {
        return 'Join our platform and start offering your services to customers. Your account will be reviewed by our team.';
    }
}
