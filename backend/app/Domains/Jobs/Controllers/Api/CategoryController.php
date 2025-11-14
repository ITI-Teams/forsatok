<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\Category;

class CategoryController extends Controller
{
    /**
     * Get all categories with job counts
     */
    public function index()
    {
        $categories = Category::withCount('jobs')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }
}

