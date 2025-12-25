<?php

namespace App\Domains\Users\Controllers\Dashboard;

use App\Domains\Users\Actions\CreateUserAction;
use App\Domains\Users\Actions\DeleteUserAction;
use App\Domains\Users\Actions\RestoreUserAction;
use App\Domains\Users\Actions\SoftDeleteUserAction;
use App\Domains\Users\Actions\UpdateUserAction;
use App\Domains\Users\Models\User;
use App\Domains\Users\Requests\StoreUserRequest;
use App\Domains\Users\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;
use App\Mail\AccountApproved;
use App\Mail\AccountRejected;
use App\Mail\AccountBanned;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Dashboard User Controller
 *
 * Handles CRUD operations for users in the admin dashboard.
 */
class DashboardUserController extends Controller
{
    /**
     * List all users with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->latest();
        $search = $request->input('search');
        $fields = $request->input('fields', ['name', 'email']);

        if ($search) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $index => $field) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $q->{$method}($field, 'like', "%{$search}%");
                }
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $users = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $users->items(),
            'meta' => $this->paginationMeta($users),
        ]);
    }

    /**
     * Create a new user.
     */
    public function store(Request $request, CreateUserAction $create): JsonResponse
    {
        $form = new StoreUserRequest();

        // Security Check: Only Super Admin can create admins
        if ($request->input('type') === 'admin' || in_array('admin', $request->input('roles', []))) {
            if (auth()->user()->email !== 'superadmin@jobhub.com' && !auth()->user()->hasRole('super-admin')) {
                return response()->json(['message' => 'Only Super Admin can create new admins.'], 403);
            }
        }

        $validated = Validator::make(
            $request->all(),
            $form->rules()
        )->validate();

        $user = $create->execute($validated);

        if ($request->filled('roles')) {
            $user->syncRoles($request->input('roles'));
        }

        if ($request->filled('permissions')) {
            $user->syncPermissions($request->input('permissions'));
        }

        return response()->json([
            'status' => true,
            'data' => $user->load('roles', 'permissions'),
        ], 201);
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user, UpdateUserAction $update): JsonResponse
    {
        $form = new UpdateUserRequest();
        $payload = array_merge($request->all(), ['user_id' => $user->id]);
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $updated = $update->execute($user, $validated);

        if ($request->filled('roles')) {
            $updated->syncRoles($request->input('roles'));
        }

        if ($request->filled('permissions')) {
            $updated->syncPermissions($request->input('permissions'));
        }

        return response()->json([
            'status' => true,
            'data' => $updated->load('roles', 'permissions'),
        ]);
    }

    /**
     * Soft delete a user.
     */
    public function destroy(User $user, SoftDeleteUserAction $delete): JsonResponse
    {
        // Security Check: Super Admin protection (Cannot delete Super Admin user or role holder)
        if ($user->email === 'superadmin@jobhub.com' || $user->hasRole('super-admin')) {
            return response()->json(['message' => 'Cannot delete Super Admin.'], 403);
        }
        // Only Super Admin can delete other admins
        if (($user->type === 'admin' || $user->hasRole('admin')) && (auth()->user()->email !== 'superadmin@jobhub.com' && !auth()->user()->hasRole('super-admin'))) {
            return response()->json(['message' => 'Only Super Admin can delete admins.'], 403);
        }

        $delete->execute($user);

        return response()->json([
            'status' => true,
            'message' => 'User moved to trash.',
        ]);
    }

    /**
     * List trashed users.
     */
    public function trashed(Request $request): JsonResponse
    {
        $users = User::onlyTrashed()->latest()->paginate(
            min(100, max(1, (int) $request->input('per_page', 10)))
        );

        return response()->json([
            'status' => true,
            'data' => $users->items(),
            'meta' => $this->paginationMeta($users),
        ]);
    }

    /**
     * Restore a trashed user.
     */
    public function restore($id, RestoreUserAction $restore): JsonResponse
    {
        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'User restored successfully.',
        ]);
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete($id, DeleteUserAction $delete): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        // Security Check: Super Admin protection
        if ($user->email === 'superadmin@jobhub.com' || $user->hasRole('super-admin')) {
            return response()->json(['message' => 'Cannot delete Super Admin.'], 403);
        }
        // Only Super Admin can delete other admins
        if (($user->type === 'admin' || $user->hasRole('admin')) && (auth()->user()->email !== 'superadmin@jobhub.com' && !auth()->user()->hasRole('super-admin'))) {
            return response()->json(['message' => 'Only Super Admin can delete admins.'], 403);
        }

        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'User deleted permanently.',
        ]);
    }

    /**
     * Get pagination meta data.
     */
    private function paginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * Approve a user (specifically employer).
     */
    public function approve(User $user): JsonResponse
    {
        if ($user->status === 'approved') {
            return response()->json([
                'status' => false,
                'message' => 'User is already approved.',
            ], 400);
        }

        // Admin approval logic - Record who approved and when
        $user->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send approval email notification
        Mail::to($user->email)->send(new AccountApproved($user));

        return response()->json([
            'status' => true,
            'message' => 'User approved successfully.',
            'data' => $user->load('approver'),
        ]);
    }

    /**
     * Reject a user (specifically employer).
     */
    public function reject(User $user, Request $request): JsonResponse
    {
        if ($user->status === 'approved') {
            return response()->json([
                'status' => false,
                'message' => 'Cannot reject a user who has already been approved.',
            ], 400);
        }

        // Validate reason is required
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $reason = $request->input('reason');
        $userName = $user->name;
        $userEmail = $user->email;

        // 1. Archive to rejected_users
        \Illuminate\Support\Facades\DB::table('rejected_users')->insert([
            'email' => $user->email,
            'name' => $user->name,
            'type' => $user->type,
            'rejection_reason' => $reason,
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Force Delete User (before sending email so we don't block on email)
        $user->forceDelete();

        // 3. Send Rejection Email
        Mail::to($userEmail)->send(new AccountRejected($userName, $userEmail, $reason));

        return response()->json([
            'status' => true,
            'message' => 'User rejected and moved to archive.',
        ]);
    }

    /**
     * Ban an approved user.
     */
    public function ban(User $user, Request $request): JsonResponse
    {
        if ($user->status === 'banned') {
            return response()->json([
                'status' => false,
                'message' => 'User is already banned.',
            ], 400);
        }

        // Validate reason is required
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $reason = $request->input('reason');

        // Update status to banned
        $user->update(['status' => 'banned']);

        // Send Ban Email
        Mail::to($user->email)->send(new AccountBanned($user, $reason));

        return response()->json([
            'status' => true,
            'message' => 'User has been banned successfully.',
            'data' => $user,
        ]);
    }

    /**
     * Unban a banned user.
     */
    public function unban(User $user): JsonResponse
    {
        if ($user->status !== 'banned') {
            return response()->json([
                'status' => false,
                'message' => 'User is not banned.',
            ], 400);
        }

        $user->update(['status' => 'approved']);

        return response()->json([
            'status' => true,
            'message' => 'User has been unbanned successfully.',
            'data' => $user,
        ]);
    }

    /**
     * Get all rejected users archive.
     */
    public function rejectedUsers(Request $request): JsonResponse
    {
        $query = \Illuminate\Support\Facades\DB::table('rejected_users')
            ->orderByDesc('rejected_at');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $rejectedUsers = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => true,
            'message' => 'Rejected users retrieved successfully.',
            'data' => $rejectedUsers,
        ]);
    }

    /**
     * Get rejection history for a specific email.
     */
    public function rejectionHistory(string $email): JsonResponse
    {
        $history = \Illuminate\Support\Facades\DB::table('rejected_users')
            ->where('email', $email)
            ->orderByDesc('rejected_at')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Rejection history retrieved successfully.',
            'data' => $history,
        ]);
    }
}
