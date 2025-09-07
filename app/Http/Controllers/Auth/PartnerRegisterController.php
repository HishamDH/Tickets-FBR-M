<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PartnerRegisterController extends Controller
{
    /**
     * Display the partner registration view.
     */
    public function create(): View
    {
        return view('auth.partner.register');
    }

    /**
     * Handle an incoming partner registration request.
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
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        // Split the name into first and last name
        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Create user
        $user = User::create([
            'name' => $request->name,
            'f_name' => $firstName,
            'l_name' => $lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'partner',
            'phone' => $request->phone,
        ]);

        // Generate unique partner code
        $partnerCode = 'P'.str_pad($user->id, 6, '0', STR_PAD_LEFT);

        // Create partner profile
        Partner::create([
            'user_id' => $user->id,
            'partner_code' => $partnerCode,
            'business_name' => $request->business_name,
            'contact_person' => $request->contact_person,
            'commission_rate' => $request->commission_rate,
            'status' => 'active', // Partners are active by default
        ]);

        event(new Registered($user));

        Auth::guard('partner')->login($user);

        return redirect('/partner/dashboard')
            ->with('success', 'تم إنشاء حسابك بنجاح! مرحباً بك في منصة الشركاء');
    }
}
