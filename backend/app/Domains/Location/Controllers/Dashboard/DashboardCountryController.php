<?php

namespace App\Domains\Location\Controllers\Dashboard;

use App\Domains\Location\Actions\Country\CreateCountryAction;
use App\Domains\Location\Actions\Country\DeleteCountryAction;
use App\Domains\Location\Actions\Country\RestoreCountryAction;
use App\Domains\Location\Actions\Country\SoftDeleteCountryAction;
use App\Domains\Location\Actions\Country\UpdateCountryAction;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Requests\Country\StoreCountryRequest;
use App\Domains\Location\Requests\Country\UpdateCountryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Dashboard Country Controller
 *
 * Handles CRUD operations for countries in the admin dashboard.
 */
class DashboardCountryController extends Controller
{
    /**
     * List all countries with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Country::query()->latest();
        $search = $request->input('search');
        $fields = $request->input('fields', ['name', 'code']);

        if ($search) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $index => $field) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $q->{$method}($field, 'like', "%{$search}%");
                }
            });
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $countries = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $countries->items(),
            'meta' => $this->paginationMeta($countries),
        ]);
    }

    /**
     * Create a new country.
     */
    public function store(Request $request, CreateCountryAction $create): JsonResponse
    {
        $form = new StoreCountryRequest();
        $validated = Validator::make(
            $request->all(),
            $form->rules()
        )->validate();

        $country = $create->execute($validated);

        return response()->json([
            'status' => true,
            'data' => $country,
        ], 201);
    }

    /**
     * Update an existing country.
     */
    public function update(Request $request, Country $country, UpdateCountryAction $update): JsonResponse
    {
        $form = new UpdateCountryRequest();
        $payload = array_merge($request->all(), ['country_id' => $country->id]);
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $updated = $update->execute($country, $validated);

        return response()->json([
            'status' => true,
            'data' => $updated,
        ]);
    }

    /**
     * Soft delete a country.
     */
    public function destroy(Country $country, SoftDeleteCountryAction $delete): JsonResponse
    {
        $delete->execute($country);

        return response()->json([
            'status' => true,
            'message' => 'Country moved to trash.',
        ]);
    }

    /**
     * List trashed countries.
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $countries = Country::onlyTrashed()->latest()->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $countries->items(),
            'meta' => $this->paginationMeta($countries),
        ]);
    }

    /**
     * Restore a trashed country.
     */
    public function restore($id, RestoreCountryAction $restore): JsonResponse
    {
        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Country restored successfully.',
        ]);
    }

    /**
     * Permanently delete a country.
     */
    public function forceDelete($id, DeleteCountryAction $delete): JsonResponse
    {
        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Country deleted permanently.',
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
