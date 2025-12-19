<?php

namespace App\Domains\Users\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Users\Requests\Api\RegisterRequest;
use App\Domains\Users\Requests\Api\LoginRequest;
use App\Domains\Users\Requests\Api\ForgotPasswordRequest;
use App\Domains\Users\Requests\Api\ResetPasswordRequest;
use App\Domains\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Carbon\Carbon;

use Illuminate\Auth\Events\Verified;

class CandidateAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->only(['name', 'email', 'password']);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 'candidate',
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('candidate');
        }

        $user->candidateInfo()->create([
            'phone' => null,
            'resume' => null,
            'education' => null,
            'experience' => null,
            'bio' => null,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        // Send email verification notification (Signed Link)
        $user->sendEmailVerificationNotification();

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Registration successful. Please verify your email.'
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !\Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password.'
            ], 401);
        }
        if ($user->type !== 'candidate') {
            return response()->json([
                'message' => 'Access denied for this user type.'
            ], 403);
        }
        $token = $user->createToken('candidate-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
                'avatar' => $user->avatar,
            ],
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 500);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful']);
        }

        return response()->json(['message' => __($status)], 500);
    }


    public function resendVerificationLink(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent to your email.']);
    }

    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully.']);
    }
}
