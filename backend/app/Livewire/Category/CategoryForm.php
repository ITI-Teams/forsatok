<?php

namespace App\Livewire\Category;

use Livewire\Component;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Actions\CreateCategoryAction;
use App\Domains\Jobs\Actions\UpdateCategoryAction;

class CategoryForm extends Component
{
    public $categoryId, $name, $description;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000'
    ];

    public function save(CreateCategoryAction $create, UpdateCategoryAction $update)
    {
            // ✅ 1. نفّذ الفاليديشن باستخدام FormRequest
        if ($this->categoryId) {
            // Update حالة
            $request = new UpdateCategoryRequest();
            $request->merge([
                'name'        => $this->name,
                'description' => $this->description,
                'category'    => $this->categoryId, // مهم عشان unique rule تستثني نفسها
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        } else {
            // Create حالة
            $request = new StoreCategoryRequest();
            $request->merge([
                'name'        => $this->name,
                'description' => $this->description,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        }

        // ✅ 2. تنفيذ Create أو Update Action
        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $update->execute($category, $validated);
            session()->flash('message', '✅ Category updated successfully!');
        } else {
            $create->execute($validated);
            session()->flash('message', '✅ Category created successfully!');
        }

        // ✅ 3. Reset + Emit
        $this->reset(['categoryId', 'name', 'description']);
        $this->dispatch('refreshCategories');
    }

    public function render()
    {
        return view('livewire.category.category-form');
    }
}

