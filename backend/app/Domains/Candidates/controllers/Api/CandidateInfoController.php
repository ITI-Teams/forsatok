<?php

namespace App\Domains\Candidates\Controllers\Api;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domains\Candidates\Requests\Api\UpdateCandidateInfoRequest;
use App\Domains\Candidates\Resources\CandidateInfoResource;

class CandidateInfoController extends Controller
{
    // show current candidate profile
    public function showProfile()
    {
        $user = Auth::user();

        if ($user->type !== 'candidate') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only candidates can access this resource.'
            ], 403);
        }

        $candidateInfo = CandidateInfo::with(['user', 'skills', 'applications'])
            ->where('user_id', $user->id)
            ->first();

        if (!$candidateInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate info not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CandidateInfoResource($candidateInfo),
            'message' => 'Candidate info retrieved successfully.'
        ]);
    }



    // update current candidate profile
    public function update(UpdateCandidateInfoRequest $request)
    {
        $user = Auth::user();

        $candidateInfo = CandidateInfo::firstOrCreate(['user_id' => $user->id]);

        $validated = $request->validated();

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $validated['resume'] = $path;
        }

        $candidateInfo->update($validated);

        // Sync skills if provided
        if ($request->has('skills')) {
            $candidateInfo->skills()->sync($request->skills);
        }

        $candidateInfo->load(['user', 'skills', 'applications']);

        return response()->json([
            'success' => true,
            'data' => new CandidateInfoResource($candidateInfo),
            'message' => 'Candidate info updated successfully.'
        ]);
    }


    // show all candidates
    public function index(Request $request)
    {
        $candidates = CandidateInfo::with(['user', 'applications', 'skills'])
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => CandidateInfoResource::collection($candidates),
            'meta' => [
                'current_page' => $candidates->currentPage(),
                'last_page' => $candidates->lastPage(),
                'per_page' => $candidates->perPage(),
                'total' => $candidates->total(),
                'from' => $candidates->firstItem(),
                'to' => $candidates->lastItem(),
            ],
            'message' => 'Candidates retrieved successfully.'
        ]);
    }


    // show single candidate info
     public function show($id)
    {
        $candidate = CandidateInfo::with(['user', 'applications', 'skills'])
            ->find($id);

        if (!$candidate) {
            return response()->json([
                'success' => false,
                'message' => 'candidate not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CandidateInfoResource($candidate),
            'message' => 'Candidate retrieved successfully.'
        ]);
    }
}
