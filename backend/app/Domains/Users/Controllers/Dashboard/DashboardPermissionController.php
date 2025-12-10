<?php

namespace App\Domains\Users\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

/**
 * Dashboard Permission Controller
 *
 * Handles CRUD operations for permissions in the admin dashboard.
 */
class DashboardPermissionController extends Controller
{
    /**
     * List all permissions with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $permissions = Permission::orderByDesc('id')->paginate(
            min(100, max(1, (int) $request->input('per_page', 10)))
        );

        return response()->json([
            'status' => true,
            'data' => $permissions->items(),
            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total(),
            ],
        ]);
    }

    /**
     * Create a new permission.
     */
    public function store(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
        ])->validate();

        $permission = Permission::create($data);

        return response()->json([
            'status' => true,
            'data' => $permission,
        ], 201);
    }

    /**
     * Update an existing permission.
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ])->validate();

        $permission->update($data);

        return response()->json([
            'status' => true,
            'data' => $permission,
        ]);
    }

    /**
     * Delete a permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json([
            'status' => true,
            'message' => 'Permission deleted successfully.',
        ]);
    }
}
