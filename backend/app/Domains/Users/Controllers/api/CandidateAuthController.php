<?php

namespace App\Domains\Users\Controllers\api;

use App\Http\Controllers\Controller;
use App\Domains\Users\Requests\Api\RegisterRequest;
use App\Domains\Users\Requests\Api\LoginRequest;
use App\Domains\Users\Requests\Api\ForgotPasswordRequest;
use App\Domains\Users\Requests\Api\ResetPasswordRequest;
use App\Domains\Users\Requests\Api\VerifyCodeRequest;
use App\Domains\Users\Models\User;
use App\Domains\Users\Mails\EmailVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Carbon\Carbon;
use App\Domains\Users\Models\EmailVerification;

class CandidateAuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $data = $request->only(['name','email','password']);
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

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! \Hash::check($credentials['password'], $user->password)) {
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

    public function sendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->firstOrFail();

        $code = mt_rand(100000, 999999);

        $expiresAt = Carbon::now()->addMinutes(15);

        \DB::table('email_verifications')->insert([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($user->email)->send(new EmailVerificationCode($code, $user->name));

        return response()->json(['message' => 'Verification code sent']);
    }

    public function verifyCode(VerifyCodeRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $record = \DB::table('email_verifications')
            ->where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->orderByDesc('id')
            ->first();

        if (! $record) {
            return response()->json(['message' => 'Invalid or expired code'], 422);
        }

        \DB::table('email_verifications')->where('id', $record->id)->update([
            'used' => true,
            'updated_at' => now(),
        ]);

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully']);
    }
}
