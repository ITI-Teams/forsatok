<?php
namespace App\Domains\Jobs\Actions\Category;

use App\Domains\Jobs\Models\Category;

class UpdateCategoryAction
{
    public function execute(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }
}
