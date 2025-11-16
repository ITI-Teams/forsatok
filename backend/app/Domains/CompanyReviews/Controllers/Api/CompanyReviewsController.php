<?php

namespace App\Domains\CompanyReviews\Controllers\Api;

use App\Domains\Users\Models\User;
use App\Events\CompanyRated;
use App\Notifications\CompanyRatedNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\CompanyReviews\Models\CompanyReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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



          // Check if the user has already make review for this company
        $existingReview = CompanyReview::where('company_id', $validated['company_id'])
            ->where('candidate_id', $validated['candidate_id'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already reviewed this company. Only one review per company is allowed.'
            ], 409); // 409 Conflict
        }

        try {
            // Create the review if not exist
            $review = CompanyReview::create($validated);
            $review->load('candidate','employerInfo');

            event(new CompanyRated($review));

            // Notify employer
            $employer = User::find($review->employerInfo->user_id);
            $employer->notify(new CompanyRatedNotification($review));
            return response()->json([
                'status' => 'success',
                'data' => $review,
                'message' => 'Review submitted successfully'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating review: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit review. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
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
