<?php

namespace App\Domains\Candidates\Controllers\Api;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domains\Applications\Requests\Api\UpdateCandidateInfoRequest;
use App\Domains\Applications\Resources\CandidateInfoResource;

class CandidateInfoController extends Controller
{
    public function show(){
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
        $candidateInfo->load('user');

        return response()->json([
            'success' => true,
            'data' => new CandidateInfoResource($candidateInfo),
            'message' => 'Candidate info updated successfully.'
        ]);
    }
}
