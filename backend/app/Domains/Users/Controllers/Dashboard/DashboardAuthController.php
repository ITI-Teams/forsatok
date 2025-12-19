<?php

namespace App\Domains\Users\Controllers\Dashboard;

use App\Domains\Users\Models\User;
use App\Domains\Users\Requests\Api\ForgotPasswordRequest;
use App\Domains\Users\Requests\Api\LoginRequest;
use App\Domains\Users\Requests\Api\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Verified;

/**
 * Dashboard Authentication Controller
 *
 * Handles authentication operations for the dashboard (admin/employer).
 */
class DashboardAuthController extends Controller
{
    /**
     * Register a new dashboard user (admin or employer).
     */
    public function register(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|in:admin,employer',
        ])->validate();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => $data['type'],
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole($data['type']);
        }

        $token = $user->createToken('dashboard-' . $data['type'] . '-token')->plainTextToken;

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Registration successful. Please verify your email.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login to the dashboard.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if (!in_array($user->type, ['admin', 'employer'])) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this user type.',
            ], 403);
        }

        $token = $user->createToken('dashboard-' . $user->type . '-token')->plainTextToken;

        $userData = $user->only(['id', 'name', 'email', 'type']);
        $userData['avatar'] = $user->avatar ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) : null;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => $userData,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout from the dashboard.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Send a password reset link.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->validated()['email'];
        $user = User::where('email', $email)->first();

        if (!$user || !in_array($user->type, ['admin', 'employer'])) {
            return response()->json([
                'status' => false,
                'message' => 'User not allowed for dashboard reset.',
            ], 404);
        }

        $status = Password::broker()->sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => __($status),
        ], 500);
    }

    /**
     * Reset password with token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user || !in_array($user->type, ['admin', 'employer'])) {
            return response()->json([
                'status' => false,
                'message' => 'User not allowed for dashboard reset.',
            ], 404);
        }

        $status = Password::broker()->reset(
            $data,
            function ($userInstance, $password) {
                $userInstance->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => 'Password reset successful.',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => __($status),
        ], 500);
    }

    /**
     * Get the current user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('roles', 'permissions');

        return response()->json([
            'status' => true,
            'data' => [
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getPermissionNames(),
            ],
        ]);
    }

    /**
     * Resend email verification link.
     */
    public function resendVerificationLink(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email already verified.',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification link sent to your email.',
        ]);
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification link.'
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified.'
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully.'
        ]);
    }
}
