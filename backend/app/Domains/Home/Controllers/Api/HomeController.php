<?php

namespace App\Domains\Home\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Location\Models\Location;
use App\Domains\Candidates\Models\CandidateInfo;

class HomeController extends Controller
{
    public function index()
    {
        
        $jobs = JobPost::select([
                'id', 'title', 'experince', 'description',
                'salary_min', 'salary_max', 'type',
                'location', 'deadline', 'is_active'
            ])
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        
        // $locations = Location::select(['id', 'name'])->take(5)->get();

        
        $candidates = CandidateInfo::with('user:id,name')
            ->latest()
            ->take(32)
            ->get([
                'id',
                'user_id',
                'bio',
                'phone',
                'experience',
                'education',
            ]);

        $carouselCandidates = $candidates->chunk(4)->values();

        return response()->json([
            'status' => true,
            'data' => [
                'jobs' => $jobs,
                // 'locations' => $locations,
                'candidates_carousel' => $carouselCandidates
            ]
        ]);
    }
}
