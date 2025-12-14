<?php

namespace App\Domains\Applications\Controllers\Dashboard;

use App\Domains\Applications\Actions\application\CreateApplicationAction;
use App\Domains\Applications\Actions\application\DeleteApplicationAction;
use App\Domains\Applications\Actions\application\RestoreApplicationAction;
use App\Domains\Applications\Actions\application\SoftDeleteApplicationAction;
use App\Domains\Applications\Actions\application\UpdateApplicationAction;
use App\Domains\Applications\Models\JobApplication;
use App\Domains\Applications\Requests\StoreApplicationRequest;
use App\Domains\Applications\Requests\UpdateApplicationRequest;
use App\Domains\Jobs\Models\JobPost;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Dashboard Application Controller
 *
 * Handles CRUD operations for job applications in the employer dashboard.
 */
class DashboardApplicationController extends Controller
{
    /**
     * List all applications for the employer's jobs.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = JobApplication::with(['candidate', 'jobPost.employer', 'jobPost.skills'])
            ->latest();

        $query->whereHas('jobPost', function ($q) use ($user) {
            $q->where('employer_id', $user->id);
        });

        $search = $request->input('search');
        $fields = $request->input('fields', []);
        if ($search && $fields) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $index => $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $col] = explode('.', $field);
                        $method = $index === 0 ? 'whereHas' : 'orWhereHas';
                        $q->{$method}($relation, function ($rel) use ($col, $search) {
                            $rel->where($col, 'like', "%{$search}%");
                        });
                    } else {
                        $method = $index === 0 ? 'where' : 'orWhere';
                        $q->{$method}($field, 'like', "%{$search}%");
                    }
                }
            });
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $applications = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $applications->items(),
            'meta' => $this->paginationMeta($applications),
        ]);
    }

    /**
     * Show a single application.
     */
    public function show(Request $request, JobApplication $application): JsonResponse
    {
        $user = $request->user();
        if ($application->jobPost->employer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'status' => true,
            'data' => $application->load(['candidate', 'jobPost.skills']),
        ]);
    }

    /**
     * Create a new application.
     */
    public function store(Request $request, CreateApplicationAction $create): JsonResponse
    {
        $user = $request->user();
        $payload = $request->all();

        if (isset($payload['resume']) && $request->file('resume')) {
            $payload['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        $form = new StoreApplicationRequest();
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $job = JobPost::findOrFail($validated['job_post_id']);
        if ($job->employer_id !== $user->id) {
            abort(403, 'You cannot add applications to this job.');
        }

        $exists = JobApplication::where('candidate_id', $validated['candidate_id'])
            ->where('job_post_id', $validated['job_post_id'])
            ->exists();
        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This candidate has already applied to this job.',
            ], 422);
        }

        $application = $create->execute($validated)->load(['candidate', 'jobPost.skills']);

        return response()->json([
            'status' => true,
            'data' => $application,
        ], 201);
    }

    /**
     * Update an existing application.
     */
    public function update(Request $request, JobApplication $application, UpdateApplicationAction $update): JsonResponse
    {
        $user = $request->user();
        if ($application->jobPost->employer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $payload = $request->all();
        $payload['application_id'] = $application->id;

        if ($request->file('resume')) {
            $newPath = $request->file('resume')->store('resumes', 'public');
            $thisPath = $application->resume_path;
            if ($thisPath) {
                $path = str_starts_with($thisPath, 'storage/') ? substr($thisPath, 8) : $thisPath;
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $payload['resume_path'] = $newPath;
        }

        $form = new UpdateApplicationRequest();
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $updated = $update->execute($application, $validated)->load(['candidate', 'jobPost.skills']);

        return response()->json([
            'status' => true,
            'data' => $updated,
        ]);
    }

    /**
     * Soft delete an application.
     */
    public function destroy(Request $request, JobApplication $application, SoftDeleteApplicationAction $delete): JsonResponse
    {
        $user = $request->user();
        if ($application->jobPost->employer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $delete->execute($application);

        return response()->json([
            'status' => true,
            'message' => 'Application moved to trash.',
        ]);
    }

    /**
     * List trashed applications.
     */
    public function trashed(Request $request): JsonResponse
    {
        $user = $request->user();
        $applications = JobApplication::onlyTrashed()
            ->with(['candidate', 'jobPost.skills'])
            ->whereHas('jobPost', function ($q) use ($user) {
                $q->where('employer_id', $user->id);
            })
            ->get();

        return response()->json([
            'status' => true,
            'data' => $applications,
        ]);
    }

    /**
     * Restore a trashed application.
     */
    public function restore(Request $request, $id, RestoreApplicationAction $restore): JsonResponse
    {
        $user = $request->user();
        $application = JobApplication::onlyTrashed()->findOrFail($id);
        if ($application->jobPost->employer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Application restored successfully.',
        ]);
    }

    /**
     * Permanently delete an application.
     */
    public function forceDelete(Request $request, $id, DeleteApplicationAction $delete): JsonResponse
    {
        $user = $request->user();
        $application = JobApplication::onlyTrashed()->findOrFail($id);
        if ($application->jobPost->employer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $path = $application->resume_path;
        if ($path) {
            $diskPath = str_starts_with($path, 'storage/') ? substr($path, 8) : $path;
            if (Storage::disk('public')->exists($diskPath)) {
                Storage::disk('public')->delete($diskPath);
            }
        }

        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Application deleted permanently.',
        ]);
    }

    /**
     * Empty all trashed applications.
     */
    public function emptyTrash(Request $request): JsonResponse
    {
        $user = $request->user();
        $applications = JobApplication::onlyTrashed()
            ->whereHas('jobPost', function ($q) use ($user) {
                $q->where('employer_id', $user->id);
            })
            ->get();

        foreach ($applications as $application) {
            $path = $application->resume_path;
            if ($path) {
                $diskPath = str_starts_with($path, 'storage/') ? substr($path, 8) : $path;
                if (Storage::disk('public')->exists($diskPath)) {
                    Storage::disk('public')->delete($diskPath);
                }
            }
            $application->forceDelete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Trash emptied successfully.',
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
