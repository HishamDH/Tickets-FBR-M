<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class CustomerRegisterController extends Controller
{
    /**
     * Display the customer registration view.
     */
    public function create(): View
    {
        return view('auth.customer.register');
    }

    /**
     * Handle an incoming customer registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
        ]);

        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'name' => $request->f_name.' '.$request->l_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'customer',
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        event(new Registered($user));

        Auth::guard('customer')->login($user);

        return redirect()->intended('/customer/dashboard')
            ->with('success', 'مرحباً بك! تم إنشاء حسابك بنجاح');
    }
}
