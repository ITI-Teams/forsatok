<?php

namespace App\Domains\Jobs\Actions;

use App\Domains\Jobs\Models\Category;

class CreateCategoryAction
{
    public function execute(array $data): Category
    {
        return Category::create($data);
    }
}
