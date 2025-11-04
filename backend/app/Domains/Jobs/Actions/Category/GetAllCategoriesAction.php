<?php
namespace App\Domains\Jobs\Actions\Category;

use App\Domains\Jobs\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class GetAllCategoriesAction
{
    public function execute(): Collection
    {
        return Category::orderBy('name')->get();
    }
}
