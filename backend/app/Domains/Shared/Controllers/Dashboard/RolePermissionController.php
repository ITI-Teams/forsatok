<?php

namespace App\Domains\Shared\Controllers\Dashboard;

use App\Domains\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Role Permission Controller
 *
 * Handles role-permission and user-access management in the admin dashboard.
 */
class RolePermissionController extends Controller
{
    /**
     * Get role permissions.
     */
    public function rolePermissions(Request $request): JsonResponse
    {
        $roleId = $request->input('role_id');
        $role = $roleId ? Role::with('permissions')->find($roleId) : null;

        return response()->json([
            'status' => true,
            'data' => [
                'roles' => Role::all(),
                'permissions' => Permission::all(),
                'selected_permissions' => $role
                    ? $role->permissions->pluck('name')
                    : [],
            ],
        ]);
    }

    /**
     * Update role permissions.
     */
    public function updateRolePermissions(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ])->validate();

        $role = Role::findOrFail($data['role_id']);
        $role->syncPermissions($data['permissions'] ?? []);

        return response()->json([
            'status' => true,
            'message' => 'Permissions updated successfully.',
        ]);
    }

    /**
     * Get user access (roles and permissions).
     */
    public function userAccess(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');
        $user = $userId
            ? User::with(['roles', 'permissions'])->find($userId)
            : null;

        return response()->json([
            'status' => true,
            'data' => [
                'users' => User::all(['id', 'name', 'email']),
                'roles' => Role::all(['id', 'name']),
                'permissions' => Permission::all(['id', 'name']),
                'selected_roles' => $user
                    ? $user->roles->pluck('name')
                    : [],
                'selected_permissions' => $user
                    ? $user->permissions->pluck('name')
                    : [],
            ],
        ]);
    }

    /**
     * Update user access (roles and permissions).
     */
    public function updateUserAccess(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ])->validate();

        $user = User::findOrFail($data['user_id']);

        foreach ($data['roles'] ?? [] as $roleName) {
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }

        foreach ($data['permissions'] ?? [] as $permissionName) {
            if (!$user->hasPermissionTo($permissionName)) {
                $user->givePermissionTo($permissionName);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Roles and permissions updated successfully.',
        ]);
    }
}
