<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class MerchantRegisterController extends Controller
{
    /**
     * Display the merchant registration view.
     */
    public function create(): View
    {
        return view('auth.merchant.register');
    }

    /**
     * Handle an incoming merchant registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'commercial_registration_number' => ['required', 'string', 'max:100'],
            'tax_number' => ['required', 'string', 'max:100'],
            'business_city' => ['required', 'string', 'max:100'],
        ]);

        // Split the name into first and last name
        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        $user = User::create([
            'name' => $request->name,
            'f_name' => $firstName,
            'l_name' => $lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'merchant',
            'user_type' => 'merchant',
            'business_name' => $request->business_name,
            'phone' => $request->phone,
            'commercial_registration_number' => $request->commercial_registration_number,
            'tax_number' => $request->tax_number,
            'business_city' => $request->business_city,
            'merchant_status' => 'pending', // Default status for new merchants
        ]);

        // Create merchant profile in the merchants table
        Merchant::create([
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'business_type' => 'other', // Default business type
            'cr_number' => $request->commercial_registration_number,
            'city' => $request->business_city,
            'verification_status' => 'pending', // Needs admin approval
        ]);

        event(new Registered($user));

        Auth::guard('merchant')->login($user); // Use merchant guard

        // Redirect to merchant status page instead of dashboard
        return redirect('/merchant/status');
    }
}
