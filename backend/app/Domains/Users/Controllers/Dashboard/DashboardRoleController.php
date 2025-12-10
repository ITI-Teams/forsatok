<?php

namespace App\Domains\Users\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

/**
 * Dashboard Role Controller
 *
 * Handles CRUD operations for roles in the admin dashboard.
 */
class DashboardRoleController extends Controller
{
    /**
     * List all roles with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $roles = Role::orderByDesc('id')->paginate(
            min(100, max(1, (int) $request->input('per_page', 10)))
        );

        return response()->json([
            'status' => true,
            'data' => $roles->items(),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ],
        ]);
    }

    /**
     * Create a new role.
     */
    public function store(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
        ])->validate();

        $role = Role::create($data);

        return response()->json([
            'status' => true,
            'data' => $role,
        ], 201);
    }

    /**
     * Update an existing role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ])->validate();

        $role->update($data);

        return response()->json([
            'status' => true,
            'data' => $role,
        ]);
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully.',
        ]);
    }
}
