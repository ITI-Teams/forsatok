<?php

namespace App\Domains\Candidates\controllers\Api;

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

        $candidateInfo = CandidateInfo::with('user')->where('user_id', $user->id)->first();

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

        // Update user info (name, email, password)
        $userData = [];
        if (isset($validated['name'])) {
            $userData['name'] = $validated['name'];
        }
        if (isset($validated['email'])) {
            $userData['email'] = $validated['email'];
        }
        if (isset($validated['password'])) {
            $userData['password'] = bcrypt($validated['password']);
        }
        
        if (!empty($userData)) {
            $user->update($userData);
        }

        // Remove user fields from validated array
        unset($validated['name'], $validated['email'], $validated['password']);

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $validated['resume'] = $path;
        }

        $candidateInfo->update($validated);
        $candidateInfo->load('user');

        return response()->json([
            'success' => true,
            'data' => new CandidateInfoResource($candidateInfo),
            'message' => 'Candidate info updated successfully.'
        ]);
    }


    // show all candidates
    public function index(Request $request)
    {
        $candidates = CandidateInfo::with(['user', 'applications'])
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
        $candidate = CandidateInfo::with(['user', 'applications'])
            ->find($id);

        if (!$candidate) {
            return response()->json([
                'success' => false,
                'message' => 'candidate not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $candidate,
            'message' => 'Candidate retrieved successfully.'
        ]);
    }
}
