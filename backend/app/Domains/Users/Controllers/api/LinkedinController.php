<?php

namespace App\Domains\Users\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class LinkedinController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('linkedin')->redirect();

    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            $error = $request->get('error');
            $errorDescription = $request->get('error_description');
            return redirect("http://localhost:4200/auth/callback?error=" . urlencode($errorDescription));
        }

        try {
            $linkedinUser = Socialite::driver('linkedin')
                ->scopes(['openid', 'profile', 'email'])
                ->stateless()
                ->user();
            $user = User::where('linkedin_id', $linkedinUser->getId())
                        ->orWhere('email', $linkedinUser->getEmail())
                        ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $linkedinUser->getName() ?? 'LinkedIn User',
                    'email' => $linkedinUser->getEmail(),
                    'linkedin_id' => $linkedinUser->getId(),
                    'avatar' => $linkedinUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
                $user->candidateInfo()->create([
                    'phone' => null,
                    'resume' => null,
                    'education' => null,
                    'experience' => null,
                    'bio' => null,
                ]);
            } else {
                $user->update([
                    'linkedin_id' => $linkedinUser->getId(),
                    'name' => $linkedinUser->getName() ?? $user->name,
                    'avatar' => $linkedinUser->getAvatar() ?? $user->avatar,
                ]);
            }

            $token = $user->createToken('LinkedinToken')->plainTextToken;

            return redirect("http://localhost:4200/auth/callback?token=" . urlencode($token) . "&user=" . urlencode(json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ])));

        } catch (\Exception $e) {
            return redirect("http://localhost:4200/auth/callback?error=auth_failed&message=" . urlencode($e->getMessage()));
        }
    }
}
