<?php

namespace App\Domains\CompanyReviews\Actions;

use App\Domains\CompanyReviews\Models\CompanyReview;

class SoftDeleteCompanyReview
{
    public function execute(CompanyReview $review): void
    {
        $review->delete();
    }
}


