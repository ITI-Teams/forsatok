<?php

namespace App\Domains\Jobs\Controllers\Dashboard;

use App\Domains\Jobs\Actions\job\CreateJobAction;
use App\Domains\Jobs\Actions\job\DeleteJobAction;
use App\Domains\Jobs\Actions\job\RestoreJobAction;
use App\Domains\Jobs\Actions\job\SoftDeleteJobAction;
use App\Domains\Jobs\Actions\job\UpdateJobAction;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Requests\Job\StoreJobRequest;
use App\Domains\Jobs\Requests\Job\UpdateJobRequest;
use App\Domains\Users\Models\User;
use App\Events\JobApproved;
use App\Events\JobCreated;
use App\Events\JobRejected;
use App\Http\Controllers\Controller;
use App\Notifications\JobApprovedNotification;
use App\Notifications\JobCreatedNotification;
use App\Notifications\JobRejectedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

/**
 * Dashboard Job Controller
 *
 * Handles CRUD operations for job posts in the dashboard.
 */
class DashboardJobController extends Controller
{
    /**
     * List all jobs with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = JobPost::with(['category', 'employer', 'location.country', 'location.city', 'skills'])
            ->latest();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            $query->where('employer_id', $user->id);
        }

        $search = $request->input('search');
        $fields = $request->input('fields', ['title', 'experience']);
        if ($search) {
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $index => $field) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $q->{$method}($field, 'like', "%{$search}%");
                }
            });
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $jobs = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $jobs->items(),
            'meta' => $this->paginationMeta($jobs),
        ]);
    }

    /**
     * Show a single job.
     */
    public function show(JobPost $job): JsonResponse
    {
        $job->load(['category', 'employer', 'location.country', 'location.city', 'skills']);

        return response()->json([
            'status' => true,
            'data' => $job,
        ]);
    }

    /**
     * Create a new job.
     */
    public function store(Request $request, CreateJobAction $create): JsonResponse
    {
        $payload = $request->all();
        if (isset($payload['qualifications']) && !isset($payload['qualification'])) {
            $payload['qualification'] = $payload['qualifications'];
        }

        $form = new StoreJobRequest();
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $job = $create->execute($validated);
        $job->refresh()->load(['category', 'employer', 'location.country', 'location.city', 'skills']);

        event(new JobCreated($job));
        $admins = User::role('admin')->get();
        Notification::send($admins, new JobCreatedNotification($job));

        return response()->json([
            'status' => true,
            'data' => $job,
        ], 201);
    }

    /**
     * Update an existing job.
     */
    public function update(Request $request, JobPost $job, UpdateJobAction $update): JsonResponse
    {
        $payload = $request->all();
        if (isset($payload['qualifications']) && !isset($payload['qualification'])) {
            $payload['qualification'] = $payload['qualifications'];
        }

        $form = new UpdateJobRequest();
        $validated = Validator::make(
            $payload,
            $form->rules()
        )->validate();

        $updated = $update->execute($job, $validated);
        $updated->refresh()->load(['category', 'employer', 'location.country', 'location.city', 'skills']);

        event(new JobCreated($updated));
        $admins = User::role('admin')->get();
        Notification::send($admins, new JobCreatedNotification($updated));

        return response()->json([
            'status' => true,
            'data' => $updated,
        ]);
    }

    /**
     * Soft delete a job.
     */
    public function destroy(JobPost $job, SoftDeleteJobAction $delete): JsonResponse
    {
        $delete->execute($job);

        return response()->json([
            'status' => true,
            'message' => 'Job moved to trash.',
        ]);
    }

    /**
     * List trashed jobs.
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $jobs = JobPost::onlyTrashed()
            ->with(['category', 'employer', 'location.country', 'location.city', 'skills'])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $jobs->items(),
            'meta' => $this->paginationMeta($jobs),
        ]);
    }

    /**
     * Restore a trashed job.
     */
    public function restore($id, RestoreJobAction $restore): JsonResponse
    {
        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Job restored successfully.',
        ]);
    }

    /**
     * Permanently delete a job.
     */
    public function forceDelete($id, DeleteJobAction $delete): JsonResponse
    {
        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Job deleted permanently.',
        ]);
    }

    /**
     * Approve a job.
     */
    public function approve(JobPost $job): JsonResponse
    {
        $job->update(['is_active' => 1]);

        event(new JobApproved($job));
        $employer = $job->employer;
        if ($employer) {
            $employer->notify(new JobApprovedNotification($job));
        }

        return response()->json([
            'status' => true,
            'message' => 'Job approved successfully.',
        ]);
    }

    /**
     * Reject a job.
     */
    public function reject(JobPost $job): JsonResponse
    {
        $job->update(['is_active' => 0]);

        event(new JobRejected($job));
        $employer = $job->employer;
        if ($employer) {
            $employer->notify(new JobRejectedNotification($job));
        }

        return response()->json([
            'status' => true,
            'message' => 'Job rejected successfully.',
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
