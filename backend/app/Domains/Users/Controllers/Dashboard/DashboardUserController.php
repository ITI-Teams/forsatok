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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
}
