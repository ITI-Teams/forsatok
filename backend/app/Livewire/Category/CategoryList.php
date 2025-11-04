<?php

namespace App\Livewire\Category;

use App\Domains\Jobs\Actions\Category\DeleteCategoryAction;
use App\Domains\Jobs\Actions\Category\SoftDeleteCategoryAction;
use App\Domains\Jobs\Models\Category;
use Livewire\Component;

class CategoryList extends Component
{
    public $categories;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::latest()->get();
    }

    public function delete($id, SoftDeleteCategoryAction $delete)
    {
        $category = Category::findOrFail($id);
        $delete->execute($category);

        session()->flash('message', '🗑️ Category moved to trash!');
        $this->loadCategories();
    }

    public function render()
    {
        return view('livewire.category.category-list')->layout('layouts.app');
    }
}
