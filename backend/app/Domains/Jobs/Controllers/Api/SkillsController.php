<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\Skill;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    // GET /api/skills
    public function index(Request $request)
    {
        $skills = Skill::query()
            ->select('id', 'name', 'slug', 'category_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $skills,
            'message' => 'Skills retrieved successfully.'
        ]);
    }
}
