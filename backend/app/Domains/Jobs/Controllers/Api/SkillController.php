<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\Skill;

class SkillController extends Controller
{
    /**
     * Get all skills
     */
    public function index()
    {
        $skills = Skill::with('category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $skills,
        ]);
    }
}


