<?php

namespace App\Filament\Customer\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    protected static string $view = 'filament-panels::pages.auth.register';
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getFirstNameFormComponent(),
                $this->getLastNameFormComponent(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getFirstNameFormComponent(): Component
    {
        return TextInput::make('f_name')
            ->label(__('First Name'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('l_name')
            ->label(__('Last Name'))
            ->required()
            ->maxLength(255);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('Display Name'))
            ->maxLength(255)
            ->placeholder('Leave blank to use "First Last"')
            ->helperText('Optional: This will be shown as your display name');
    }

    protected function getUserData(): array
    {
        $data = $this->form->getState();
        
        // Auto-generate name from first and last name if not provided
        if (empty($data['name'])) {
            $data['name'] = trim($data['f_name'] . ' ' . $data['l_name']);
        }

        return [
            'f_name' => $data['f_name'],
            'l_name' => $data['l_name'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => 'customer',
        ];
    }
}