<?php

namespace App\Domains\Jobs\Actions\Category;

use App\Domains\Jobs\Models\Category;

class SoftDeleteCategoryAction
{
    public function execute(Category $category): void
    {
        $category->delete();
    }
}
