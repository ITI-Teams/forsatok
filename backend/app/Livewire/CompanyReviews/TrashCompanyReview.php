<?php

namespace App\Livewire\CompanyReviews;

use Livewire\Component;
use App\Domains\CompanyReviews\Actions\RestoreCompanyReview;
use App\Domains\CompanyReviews\Actions\DeleteCompanyReview;
use App\Domains\CompanyReviews\Actions\GetAllCompanyReview;
use App\Domains\CompanyReviews\Models\CompanyReview;
use Livewire\WithPagination;

class TrashCompanyReview extends Component
{
   use WithPagination;

    public $perPage = 10;

    public function restore($id, RestoreCompanyReview $restore)
    {
        $restore->execute($id);

        session()->flash('message', 'Review restored successfully!');
        $this->resetPage();
    }

    public function forceDelete($id, RestoreCompanyReview $forceDelete)
    {
        $forceDelete->execute($id);

        session()->flash('message', 'Review permanently deleted!');
        $this->resetPage();
    }

    public function render()
    {
        $reviews = CompanyReview::onlyTrashed()
                        ->with(['company', 'candidate'])
                        ->latest()
                        ->paginate($this->perPage);

        return view('livewire.company-reviews.trash-company-review', [
            'trashedReviews' => $reviews
        ])->layout('layouts.app');
    }
}
