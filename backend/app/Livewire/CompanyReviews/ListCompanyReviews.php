<?php

namespace App\Livewire\CompanyReviews;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\CompanyReviews\Models\CompanyReview;
use App\Domains\CompanyReviews\Actions\SoftDeleteCompanyReviewAction;
use Livewire\Attributes\On;

class ListCompanyReviews extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = ['review', 'candidate.name', 'company.name'];
    public $perPage = 10;

    #[On('companyReviewSearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->resetPage();
    }

    public function delete($id, SoftDeleteCompanyReviewAction $delete)
    {
        $review = CompanyReview::findOrFail($id);
        $delete->execute($review);

        session()->flash('message', 'Review moved to trash!');
        $this->resetPage();
    }

    public function render()
    {
        $query = CompanyReview::with(['company', 'candidate'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $col] = explode('.', $field);
                        $q->orWhereHas($relation, fn($q2) => $q2->where($col, 'like', "%{$this->search}%"));
                    } else {
                        $q->orWhere($field, 'like', "%{$this->search}%");
                    }
                }
            });
        }

        $reviews = $query->paginate($this->perPage);

        return view('livewire.company-reviews.list-company-reviews', [
            'reviews' => $reviews
        ])->layout('layouts.app');
    }
}
