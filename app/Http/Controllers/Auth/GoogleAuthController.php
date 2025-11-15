<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    
    public function redirect(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:entrepreneur,advisor',
        ]);

        $request->session()->put('google_auth_role', $validated['role']);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'google' => 'Unable to authenticate with Google. Please try again.',
            ]);
        }

        $requestedRole = $request->session()->pull('google_auth_role', 'entrepreneur');

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
            }
            $user->save();
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? $googleUser->getEmail(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(40)),
                'role' => $requestedRole,
                'language' => 'en',
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);

        return redirect($user->role === 'advisor' ? '/advisor/dashboard' : '/voice-roadmap');
    }
}

