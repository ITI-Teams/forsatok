<?php

namespace App\Domains\CompanyReviews\Controllers\Api;

use App\Domains\Employers\Models\EmployerInfo;
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
     public function showCompanyReviews($employerId)
    {
        $company_id = EmployerInfo::where('id',$employerId)->first()->user_id;;
        $reviews = CompanyReview::where('company_id', $company_id)
                    ->where('status','approved')
                    ->with('candidate')
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }


    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'company_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $validated['candidate_id'] = auth()->id();



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
            
            // Reload with relationships for response
            $review->load('candidate');

            event(new CompanyRated($review));

            // Notify employer
            $employer = User::find($validated['company_id']);
            if ($employer) {
                $employer->notify(new CompanyRatedNotification($review));
            }
            
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
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $review = CompanyReview::findOrFail($id);

        if ($review->candidate_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

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
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $review = CompanyReview::findOrFail($id);

        if ($review->candidate_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully'
        ]);
    }
}
