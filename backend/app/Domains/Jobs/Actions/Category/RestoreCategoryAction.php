<?php

namespace App\Domains\Jobs\Actions\Category;

use App\Domains\Jobs\Models\Category;

class RestoreCategoryAction
{
    public function execute(int $categoryId): void
    {
        $category = Category::onlyTrashed()->findOrFail($categoryId);
        $category->restore();
    }
}
