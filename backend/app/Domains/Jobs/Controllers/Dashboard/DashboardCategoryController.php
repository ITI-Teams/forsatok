<?php

namespace App\Domains\Jobs\Controllers\Dashboard;

use App\Domains\Jobs\Actions\Category\CreateCategoryAction;
use App\Domains\Jobs\Actions\Category\DeleteCategoryAction;
use App\Domains\Jobs\Actions\Category\RestoreCategoryAction;
use App\Domains\Jobs\Actions\Category\SoftDeleteCategoryAction;
use App\Domains\Jobs\Actions\Category\UpdateCategoryAction;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Requests\Category\StoreCategoryRequest;
use App\Domains\Jobs\Requests\Category\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Dashboard Category Controller
 *
 * Handles CRUD operations for job categories in the admin dashboard.
 */
class DashboardCategoryController extends Controller
{
    /**
     * List all categories with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query()->withCount('jobs')->latest();
        $search = $request->input('search');
        $fields = $request->input('fields', ['name']);

        if ($search) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $index => $field) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $q->{$method}($field, 'like', "%{$search}%");
                }
            });
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $categories = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $categories->items(),
            'meta' => $this->paginationMeta($categories),
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(Request $request, CreateCategoryAction $create): JsonResponse
    {
        $form = new StoreCategoryRequest();
        $validated = Validator::make(
            $request->all(),
            $form->rules()
        )->validate();

        $category = $create->execute($validated);

        return response()->json([
            'status' => true,
            'data' => $category,
        ], 201);
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category, UpdateCategoryAction $update): JsonResponse
    {
        $form = new UpdateCategoryRequest();
        $payload = array_merge($request->all(), ['category_id' => $category->id]);
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $updated = $update->execute($category, $validated);

        return response()->json([
            'status' => true,
            'data' => $updated,
        ]);
    }

    /**
     * Soft delete a category.
     */
    public function destroy(Category $category, SoftDeleteCategoryAction $delete): JsonResponse
    {
        $delete->execute($category);

        return response()->json([
            'status' => true,
            'message' => 'Category moved to trash.',
        ]);
    }

    /**
     * List trashed categories.
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $categories = Category::onlyTrashed()->latest()->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $categories->items(),
            'meta' => $this->paginationMeta($categories),
        ]);
    }

    /**
     * Restore a trashed category.
     */
    public function restore($id, RestoreCategoryAction $restore): JsonResponse
    {
        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Category restored successfully.',
        ]);
    }

    /**
     * Permanently delete a category.
     */
    public function forceDelete($id, DeleteCategoryAction $delete): JsonResponse
    {
        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Category deleted permanently.',
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
