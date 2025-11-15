<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Domains\Jobs\Models\Skill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Get all Skills with job counts
     */
    public function index(Request $request)
    {
        $query = Skill::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $skills = $query->get();

        return response()->json([
            'success' => true,
            'data' => $skills
        ]);
    }
}

