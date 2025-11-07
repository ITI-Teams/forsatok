<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\SavedJob;
use App\Domains\Jobs\Requests\Api\SaveJobRequest;
use Illuminate\Http\JsonResponse;

class SaveJobController extends Controller
{
    /**
     * Store (toggle) a saved job for the candidate.
     */
    public function store(SaveJobRequest $request): JsonResponse
    {
        $user = auth()->user();

        $existing = SavedJob::where('candidate_id', $user->id)
            ->where('job_post_id', $request->job_post_id)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'message' => 'Job unsaved successfully.',
                'saved' => false,
            ]);
        }

        SavedJob::create([
            'candidate_id' => $user->id,
            'job_post_id' => $request->job_post_id,
        ]);

        return response()->json([
            'message' => 'Job saved successfully.',
            'saved' => true,
        ]);
    }

    /**
     * Get all saved jobs for the authenticated candidate.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $savedJobs = SavedJob::with('job')
            ->where('candidate_id', $user->id)
            ->get();

        return response()->json([
            'data' => $savedJobs,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $user = auth()->user();

        // Find the saved job belonging to this user
        $savedJob = SavedJob::where('candidate_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $savedJob->delete();

        return response()->json([
            'message' => 'Job removed from saved list successfully.'
        ]);
    }
}
