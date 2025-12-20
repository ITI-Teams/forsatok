<?php

namespace App\Domains\Users\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    protected FrontendUrlService $frontendUrlService;

    public function __construct(FrontendUrlService $frontendUrlService)
    {
        $this->frontendUrlService = $frontendUrlService;
    }

    public function redirect(Request $request)
    {
        $source = $request->get('source', 'jobhub');
        $userType = $request->get('type', 'candidate');

        $state = base64_encode(json_encode([
            'source' => $source,
            'user_type' => $userType,
        ]));

        return Socialite::driver('google')
            ->with(['state' => $state])
            ->redirect();
    }

    public function callback(Request $request)
    {
        $stateData = ['source' => 'jobhub', 'user_type' => 'candidate'];
        if ($request->has('state')) {
            $stateData = json_decode(base64_decode($request->get('state')), true) ?? $stateData;
        }

        $source = $stateData['source'] ?? 'jobhub';
        $userType = $stateData['user_type'] ?? 'candidate';

        $this->frontendUrlService->setSource($source);

        if ($request->has('error')) {
            $errorDescription = $request->get('error_description', 'Authentication failed');
            return $this->redirectWithError($source, $errorDescription);
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
                    'type' => $userType,
                ]);

                // Assign role if method exists
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole($userType);
                }

                // Create candidate info if type is candidate
                if ($userType === 'candidate' && method_exists($user, 'candidateInfo')) {
                    $user->candidateInfo()->create([
                        'phone' => null,
                        'resume' => null,
                        'education' => null,
                        'experience' => null,
                        'bio' => null,
                    ]);
                }
            } else {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar() ?? $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }

            // Handle Response based on Source
            if ($source === 'web' || $source === 'livewire') {
                Auth::login($user);
                return redirect()->route('dashboard');
            } else {
                // API-based frontend (React/Angular)
                $token = $user->createToken('GoogleToken')->plainTextToken;

                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'type' => $user->type,
                ];

                $redirectUrl = $this->frontendUrlService->makeUrl('/auth/callback', [
                    'token' => $token,
                    'user' => json_encode($userData)
                ]);

                return redirect($redirectUrl);
            }

        } catch (\Exception $e) {
            return $this->redirectWithError($source, $e->getMessage());
        }
    }

    protected function redirectWithError(string $source, string $message)
    {
        if ($source === 'web' || $source === 'livewire') {
            return redirect()->route('login')->withErrors(['google' => $message]);
        }

        $redirectUrl = $this->frontendUrlService->makeUrl('/auth/callback', [
            'error' => 'auth_failed',
            'message' => $message
        ]);

        return redirect($redirectUrl);
    }
}
