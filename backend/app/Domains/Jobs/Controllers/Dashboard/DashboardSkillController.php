<?php

namespace App\Domains\Jobs\Controllers\Dashboard;

use App\Domains\Jobs\Actions\Skills\CreateSkillAction;
use App\Domains\Jobs\Actions\Skills\DeleteSkillAction;
use App\Domains\Jobs\Actions\Skills\RestoreSkillAction;
use App\Domains\Jobs\Actions\Skills\SoftDeleteSkillAction;
use App\Domains\Jobs\Actions\Skills\UpdateSkillAction;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Jobs\Requests\Skill\StoreSkillRequest;
use App\Domains\Jobs\Requests\Skill\UpdateSkillRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Dashboard Skill Controller
 *
 * Handles CRUD operations for skills in the admin dashboard.
 */
class DashboardSkillController extends Controller
{
    /**
     * List all skills with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Skill::with('category')->latest();
        $search = $request->input('search');
        $fields = $request->input('fields', ['name']);

        if ($search) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $index => $field) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    if (str_contains($field, '.')) {
                        [$relation, $col] = explode('.', $field);
                        $q->$method(function ($sub) use ($relation, $col, $search) {
                            $sub->whereHas($relation, fn($rel) => $rel->where($col, 'like', "%{$search}%"));
                        });
                    } else {
                        $q->$method($field, 'like', "%{$search}%");
                    }
                }
            });
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $skills = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $skills->items(),
            'meta' => $this->paginationMeta($skills),
        ]);
    }

    /**
     * Create a new skill.
     */
    public function store(Request $request, CreateSkillAction $create): JsonResponse
    {
        $form = new StoreSkillRequest();
        $validated = Validator::make(
            $request->all(),
            $form->rules()
        )->validate();

        $skill = $create->execute($validated);

        return response()->json([
            'status' => true,
            'data' => $skill,
        ], 201);
    }

    /**
     * Update an existing skill.
     */
    public function update(Request $request, Skill $skill, UpdateSkillAction $update): JsonResponse
    {
        $form = new UpdateSkillRequest();
        $payload = array_merge($request->all(), ['skill_id' => $skill->id]);
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $updated = $update->execute($skill, $validated);

        return response()->json([
            'status' => true,
            'data' => $updated,
        ]);
    }

    /**
     * Soft delete a skill.
     */
    public function destroy(Skill $skill, SoftDeleteSkillAction $delete): JsonResponse
    {
        $delete->execute($skill);

        return response()->json([
            'status' => true,
            'message' => 'Skill moved to trash.',
        ]);
    }

    /**
     * List trashed skills.
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $skills = Skill::onlyTrashed()->with('category')->latest()->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $skills->items(),
            'meta' => $this->paginationMeta($skills),
        ]);
    }

    /**
     * Restore a trashed skill.
     */
    public function restore($id, RestoreSkillAction $restore): JsonResponse
    {
        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Skill restored successfully.',
        ]);
    }

    /**
     * Permanently delete a skill.
     */
    public function forceDelete($id, DeleteSkillAction $delete): JsonResponse
    {
        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Skill deleted permanently.',
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
