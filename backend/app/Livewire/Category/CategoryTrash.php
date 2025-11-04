<?php

namespace App\Livewire\Category;

use App\Domains\Jobs\Actions\Category\DeleteCategoryAction;
use App\Domains\Jobs\Actions\Category\RestoreCategoryAction;
use App\Domains\Jobs\Models\Category;
use Livewire\Component;

class CategoryTrash extends Component
{
    public $trashedCategories;

    public function mount()
    {
        $this->loadTrashed();
    }

    public function loadTrashed()
    {
        $this->trashedCategories = Category::onlyTrashed()->latest()->get();
    }

    public function restore($id, RestoreCategoryAction $restore)
    {
        $restore->execute($id);
        session()->flash('message', '✅ Category restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteCategoryAction $forceDelete)
    {
        $forceDelete->execute($id);
        session()->flash('message', '❌ Category permanently deleted!');
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.category.category-trash')->layout('layouts.app');
    }
}
