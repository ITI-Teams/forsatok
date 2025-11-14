<?php

namespace App\Domains\Home\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Location\Models\Locationable;
use App\Domains\Candidates\Models\CandidateInfo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {

        $jobs = JobPost::select([
                'id', 'title', 'experience', 'description',
                'salary_min', 'salary_max', 'work_type', 'deadline', 'is_active'
            ])
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get();


        $topCities = Locationable::query()
            ->join('cities', 'locationables.city_id', '=', 'cities.id')
            ->join('job_posts', function ($join) {
                $join->on('locationables.locationable_id', '=', 'job_posts.id')
                    ->where('locationables.locationable_type', JobPost::class);
            })
            ->select('cities.id', 'cities.name', DB::raw('COUNT(job_posts.id) as job_count'))
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('job_count')
            ->take(5)
            ->get()
            ->map(function ($city, $index) {
                $city->image = asset('images/Location/Location' . ($index + 1) . '.jpeg');
                return $city;
            });


        $candidates = CandidateInfo::with(['user:id,name','location.city:id,name', 'location.country:id,name'])
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

        $carouselCandidates = $candidates;

        return response()->json([
            'status' => true,
            'data' => [
                'jobs' => $jobs,
                'top_cities' => $topCities,
                'candidates_carousel' => $carouselCandidates
            ]
        ]);
    }
}
