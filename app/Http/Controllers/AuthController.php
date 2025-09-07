<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,merchant,user,Customer,Merchant',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['name'] = $validated['f_name'] . ' ' . $validated['l_name'];
        
        // Map role to user_type
        $roleMapping = [
            'user' => 'customer',
            'Customer' => 'customer', 
            'merchant' => 'merchant',
            'Merchant' => 'merchant',
            'admin' => 'admin'
        ];
        
        $validated['user_type'] = $roleMapping[$validated['role']] ?? 'customer';
        unset($validated['role']); // Remove role from array since we're using user_type

        $user = User::create($validated);

        // Assign role based on user_type
        $spatie_role = ucfirst($validated['user_type'] === 'customer' ? 'Customer' : $validated['user_type']);
        $user->assignRole($spatie_role);

        // Check if this is an API request
        if ($request->expectsJson() || $request->is('api/*')) {
            $token = $user->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 201);
        } else {
            Auth::login($user);
            return response()->json(['message' => 'Registration successful', 'user' => $user]);
        }
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if this is an API request or web request
            if ($request->expectsJson() || $request->is('api/*')) {
                // For API requests, create and return a token
                $token = $user->createToken('auth-token')->plainTextToken;
                
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]);
            } else {
                // For web requests, use session
                $request->session()->regenerate();
                
                return response()->json(['message' => 'Login successful', 'user' => $user]);
            }
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        // Check if this is an API request or web request
        if ($request->expectsJson() || $request->is('api/*')) {
            // For API requests, revoke the current token
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            
            return response()->json(['message' => 'Logout successful']);
        } else {
            // For web requests, use session
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return response()->json(['message' => 'Logout successful']);
        }
    }

    /**
     * Show user profile.
     */
    public function showProfile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user->fresh()
        ]);
    }
}
