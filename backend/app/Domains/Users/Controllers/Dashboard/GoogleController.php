<?php

namespace App\Domains\Users\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            $error = $request->get('error');
            $errorDescription = $request->get('error_description', 'Authentication failed');
            return redirect("http://localhost:4200/auth/callback?error=" . urlencode($errorDescription));
        }

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            // Find user by google_id or email
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'type' => 'candidate',
                ]);

                // Assign candidate role if method exists
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('candidate');
                }

                // Create candidate info
                $user->candidateInfo()->create([
                    'phone' => null,
                    'resume' => null,
                    'education' => null,
                    'experience' => null,
                    'bio' => null,
                ]);
            } else {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName() ?? $user->name,
                    'avatar' => $googleUser->getAvatar() ?? $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }

            // Create token
            $token = $user->createToken('GoogleToken')->plainTextToken;

            return redirect("http://localhost:4200/auth/callback?token=" . urlencode($token) . "&user=" . urlencode(json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'type' => $user->type,
            ])));

        } catch (\Exception $e) {
            return redirect("http://localhost:4200/auth/callback?error=auth_failed&message=" . urlencode($e->getMessage()));
        }
    }
}
