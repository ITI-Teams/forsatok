<?php

namespace App\Domains\Location\Controllers\Dashboard;

use App\Domains\Location\Actions\City\CreateCityAction;
use App\Domains\Location\Actions\City\DeleteCityAction;
use App\Domains\Location\Actions\City\RestoreCityAction;
use App\Domains\Location\Actions\City\SoftDeleteCityAction;
use App\Domains\Location\Actions\City\UpdateCityAction;
use App\Domains\Location\Models\City;
use App\Domains\Location\Requests\City\StoreCityRequest;
use App\Domains\Location\Requests\City\UpdateCityRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Dashboard City Controller
 *
 * Handles CRUD operations for cities in the admin dashboard.
 */
class DashboardCityController extends Controller
{
    /**
     * List all cities with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = City::with('country')->latest();
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
        $cities = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $cities->items(),
            'meta' => $this->paginationMeta($cities),
        ]);
    }

    /**
     * Create a new city.
     */
    public function store(Request $request, CreateCityAction $create): JsonResponse
    {
        $form = new StoreCityRequest();
        $validated = Validator::make(
            $request->all(),
            $form->rules()
        )->validate();

        $city = $create->execute($validated);

        return response()->json([
            'status' => true,
            'data' => $city,
        ], 201);
    }

    /**
     * Update an existing city.
     */
    public function update(Request $request, City $city, UpdateCityAction $update): JsonResponse
    {
        $form = new UpdateCityRequest();
        $payload = array_merge($request->all(), ['city_id' => $city->id]);
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $updated = $update->execute($city, $validated);

        return response()->json([
            'status' => true,
            'data' => $updated,
        ]);
    }

    /**
     * Soft delete a city.
     */
    public function destroy(City $city, SoftDeleteCityAction $delete): JsonResponse
    {
        $delete->execute($city);

        return response()->json([
            'status' => true,
            'message' => 'City moved to trash.',
        ]);
    }

    /**
     * List trashed cities.
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $cities = City::onlyTrashed()->with('country')->latest()->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $cities->items(),
            'meta' => $this->paginationMeta($cities),
        ]);
    }

    /**
     * Restore a trashed city.
     */
    public function restore($id, RestoreCityAction $restore): JsonResponse
    {
        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'City restored successfully.',
        ]);
    }

    /**
     * Permanently delete a city.
     */
    public function forceDelete($id, DeleteCityAction $delete): JsonResponse
    {
        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'City deleted permanently.',
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
