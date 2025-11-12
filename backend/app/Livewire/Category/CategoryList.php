<?php

namespace App\Livewire\Category;

use App\Domains\Jobs\Actions\Category\SoftDeleteCategoryAction;
use App\Domains\Jobs\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class CategoryList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = [];

    #[On('categorySearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? [];
        $this->resetPage();
    }

    public function delete($id, SoftDeleteCategoryAction $delete)
    {
        $category = Category::findOrFail($id);
        $delete->execute($category);

        session()->flash('message', '🗑️ Category moved to trash!');
    }

    public function render()
    {
        $query = Category::latest();

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    $method = $i === 0 ? 'where' : 'orWhere';
                    $q->$method($field, 'like', "%{$this->search}%");
                }
            });
        }

        $categories = $query->paginate(10);

        return view('livewire.category.category-list', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
