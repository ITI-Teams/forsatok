<?php

namespace App\Domains\CompanyReviews\Actions;

use App\Domains\CompanyReviews\Models\CompanyReview;

class RestoreCompanyReview
{
    public function execute(int $reviewId): void
    {
        $review = CompanyReview::onlyTrashed()->findOrFail($reviewId);
        $review->restore();
    }
}