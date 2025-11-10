<?php

namespace App\Domains\CompanyReviews\Actions;

use App\Domains\CompanyReviews\Models\CompanyReview;


class GetAllCompanyReview
{
     public function execute(): Collection
    {
        return CompanyReview::with(['company', 'candidate'])
                            ->orderByDesc('created_at')
                            ->get();
    }
}
