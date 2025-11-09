<?php

namespace App\Livewire\CompanyReviews;

use Livewire\Component;
use App\Domains\CompanyReviews\Actions\GetAllCompanyReview;
use App\Domains\CompanyReviews\Actions\SoftDeleteCompanyReview;
use App\Domains\CompanyReviews\Actions\DeleteCompanyReview;
use App\Domains\CompanyReviews\Models\CompanyReview;
use Livewire\WithPagination;


class ListCompanyReviews extends Component
{
    use WithPagination;

    public $perPage = 10;

    public function delete($id, SoftDeleteCompanyReviewAction $delete)
    {
        $review = CompanyReview::findOrFail($id);
        $delete->execute($review);

        session()->flash('message', 'Review moved to trash!');
        $this->resetPage(); 
    }

    public function render()
    {
        $reviews = CompanyReview::with(['company', 'candidate'])
                        ->latest()
                        ->paginate($this->perPage);

        return view('livewire.company-reviews.list-company-reviews', [
            'reviews' => $reviews
        ])->layout('layouts.app');
    }
}
