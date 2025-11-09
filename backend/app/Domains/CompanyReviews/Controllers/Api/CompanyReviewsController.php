<?php

namespace App\Domains\CompanyReviews\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\CompanyReviews\Models\CompanyReview;
use Illuminate\Http\JsonResponse;

Class CompanyReviewsController extends Controller
{
    /**
     * Get all company reviews.
     */
     public function showCompanyReviews($companyId)
    {
        $reviews = CompanyReview::where('company_id', $companyId)
                    ->with('candidate') 
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:users,id',
            'candidate_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review = CompanyReview::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $review
        ], 201);
    }

    
    public function update(Request $request, $id)
    {
        $review = CompanyReview::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'review' => 'sometimes|string|nullable',
        ]);

        $review->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $review
        ]);
    }

  
    public function destroy($id)
    {
        $review = CompanyReview::findOrFail($id);

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully'
        ]);
    }
}