<?php

namespace App\Domains\Jobs\Controllers\Api ;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\JobPost;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::query()
            ->with(['category:id,name', 'employer:id,name,email'])
            ->where('is_active', true)
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($location = $request->input('location')) {
            $query->where('location', 'like', "%{$location}%");
        }

        if ($category = $request->input('category_id')) {
            $query->where('category_id', $category);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($min = $request->input('min_salary')) {
            $query->where('salary_min', '>=', $min);
        }
        if ($max = $request->input('max_salary')) {
            $query->where('salary_max', '<=', $max);
        }

        $jobs = $query->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $jobs,
        ]);
    }

    public function show($id)
    {
        $job = JobPost::with(['category:id,name', 'employer:id,name,email'])
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $job,
        ]);
    }
}
