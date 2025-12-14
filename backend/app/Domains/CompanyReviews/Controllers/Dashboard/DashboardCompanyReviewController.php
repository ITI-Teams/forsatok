<?php

namespace App\Domains\CompanyReviews\Controllers\Dashboard;

use App\Domains\CompanyReviews\Actions\DeleteCompanyReview;
use App\Domains\CompanyReviews\Actions\RestoreCompanyReview;
use App\Domains\CompanyReviews\Actions\SoftDeleteCompanyReview;
use App\Domains\CompanyReviews\Models\CompanyReview;
use App\Http\Controllers\Controller;
use App\Notifications\RatingApprovedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dashboard Company Review Controller
 *
 * Handles company review operations in the employer dashboard.
 */
class DashboardCompanyReviewController extends Controller
{
    /**
     * List all reviews for the employer's company.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = CompanyReview::with(['company', 'candidate'])
            ->where('company_id', $user->id)
            ->latest();

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('review', 'like', "%{$search}%");
            });
        }

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $reviews = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $reviews->items(),
            'meta' => $this->paginationMeta($reviews),
        ]);
    }

    /**
     * Approve a review.
     */
    public function approve(CompanyReview $review): JsonResponse
    {
        $review->update(['status' => 'approved']);
        $review->candidate?->notify(new RatingApprovedNotification($review));

        return response()->json([
            'status' => true,
            'message' => 'Review approved successfully.',
        ]);
    }

    /**
     * Reject a review.
     */
    public function reject(CompanyReview $review): JsonResponse
    {
        $review->update(['status' => 'rejected']);
        $review->candidate?->notify(new RatingApprovedNotification($review));

        return response()->json([
            'status' => true,
            'message' => 'Review rejected successfully.',
        ]);
    }

    /**
     * Soft delete a review.
     */
    public function destroy(CompanyReview $review, SoftDeleteCompanyReview $delete): JsonResponse
    {
        $delete->execute($review);

        return response()->json([
            'status' => true,
            'message' => 'Review moved to trash.',
        ]);
    }

    /**
     * List trashed reviews.
     */
    public function trashed(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $reviews = CompanyReview::onlyTrashed()
            ->where('company_id', $user->id)
            ->with(['company', 'candidate'])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $reviews->items(),
            'meta' => $this->paginationMeta($reviews),
        ]);
    }

    /**
     * Restore a trashed review.
     */
    public function restore($id, RestoreCompanyReview $restore): JsonResponse
    {
        $restore->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Review restored successfully.',
        ]);
    }

    /**
     * Permanently delete a review.
     */
    public function forceDelete($id, DeleteCompanyReview $delete): JsonResponse
    {
        $delete->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Review deleted permanently.',
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
