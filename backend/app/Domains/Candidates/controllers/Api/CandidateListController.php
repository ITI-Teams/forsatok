<?php

namespace App\Domains\Candidates\contollers\Api;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CandidateListController extends Controller
{
    // to show candidates with their info and pagination
    public function index(Request $request)
    {
        $cadidates = CandidateInfo::with(['user', 'skills', 'applications'])
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => CandidateInfo::collection($cadidates),
            'meta' => [
                'current_page' => $cadidates->currentPage(),
                'last_page' => $cadidates->lastPage(),
                'per_page' => $cadidates->perPage(),
                'total' => $cadidates->total(),
                'from' => $cadidates->firstItem(),
                'to' => $cadidates->lastItem(),
            ],
            'message' => 'Candidates retrieved successfully.'
        ]);
    }

    //   show single candidate
    public function show($id)
    {
        $candidate = CandidateInfo::with(['user', 'skills', 'applications'])
            ->findOrFail($id);

        if (!$candidate) {
            return response()->json([
                'success' => false,
                'message' => 'candidate not found.'
            ], 404);
        }
    }

    



}
