<?php

namespace App\Livewire\Category;

use App\Domains\Jobs\Actions\Category\CreateCategoryAction;
use App\Domains\Jobs\Actions\Category\UpdateCategoryAction;
use App\Domains\Jobs\Requests\Category\StoreCategoryRequest;
use App\Domains\Jobs\Requests\Category\UpdateCategoryRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use App\Domains\Jobs\Models\Category;

class CategoryForm extends Component
{
    public $categoryId, $name;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function mount($category = null)
    {
        if ($category) {
            $model = Category::findOrFail($category);
            $this->categoryId = $model->id;
            $this->name = $model->name;
        }
    }

    public function save(CreateCategoryAction $create, UpdateCategoryAction $update)
    {
        if ($this->categoryId) {
            $request = new UpdateCategoryRequest();
            $request->merge([
                'name'        => $this->name,
                'category_id'    => $this->categoryId,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        } else {
            $request = new StoreCategoryRequest();
            $request->merge([
                'name'        => $this->name,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        }

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $update->execute($category, $validated);
            session()->flash('message', '✅ Category updated successfully!');
        } else {
            $create->execute($validated);
            session()->flash('message', '✅ Category created successfully!');
        }
        return $this->redirectRoute('categories.index', navigate: true);
    }

    public function cancel()
    {
        return $this->redirectRoute('categories.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.category.category-form')->layout('layouts.app');
    }
}
