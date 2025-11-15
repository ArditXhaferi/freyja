<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle a registration request
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:entrepreneur,advisor,investor'],
            'language' => ['sometimes', 'string', 'max:10'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'has_business_experience' => ['sometimes', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'entrepreneur',
            'language' => $validated['language'] ?? 'en',
            'country_of_origin' => $validated['country_of_origin'] ?? null,
            'has_business_experience' => isset($validated['has_business_experience']) && $validated['has_business_experience'] == '1',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(match ($user->role) {
            'advisor' => '/advisor/dashboard',
            default => '/voice-roadmap',
        });
    }
}

