<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Models\Skill;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class JobFilterController extends Controller
{
    /**
     * Get all available filter options for jobs
     */
    public function getFilterOptions()
    {
        $hasTypeColumn = Schema::hasColumn('job_posts', 'type');

        $workTypeField = $hasTypeColumn ? 'COALESCE(work_type, `type`)' : 'work_type';

        $workTypeCounts = JobPost::query()
            ->where('is_active', true)
            ->whereRaw("{$workTypeField} IS NOT NULL")
            ->selectRaw("
                LOWER(
                    REPLACE(
                        REPLACE({$workTypeField}, ' ', '-'),
                        '_', '-'
                    )
                ) as filter_key,
                COUNT(*) as total
            ")
            ->groupBy('filter_key')
            ->pluck('total', 'filter_key');

        $types = collect($this->workTypeDefinitions())
            ->map(function ($label, $value) use ($workTypeCounts) {
                return [
                    'value' => $value,
                    'name' => $label,
                    'count' => (int) ($workTypeCounts->get($value, 0)),
                ];
            });

        $workTypeCounts->each(function ($count, $value) use (&$types) {
            $normalized = $this->normalizeKey($value);
            if (!$types->contains('value', $normalized)) {
                $types->push([
                    'value' => $normalized,
                    'name' => $this->formatTypeName($value),
                    'count' => (int) $count,
                ]);
            }
        });

        $workPlaceCounts = JobPost::query()
            ->where('is_active', true)
            ->whereNotNull('work_place')
            ->select(DB::raw("
                LOWER(
                    REPLACE(
                        REPLACE(work_place, ' ', '-'),
                        '_', '-'
                    )
                ) as filter_key
            "), DB::raw('COUNT(*) as total'))
            ->groupBy('filter_key')
            ->pluck('total', 'filter_key');

        $workPlaces = collect($this->workPlaceDefinitions())
            ->map(function ($label, $value) use ($workPlaceCounts) {
                return [
                    'value' => $value,
                    'name' => $label,
                    'count' => (int) ($workPlaceCounts->get($value, 0)),
                ];
            });

        $workPlaceCounts->each(function ($count, $value) use (&$workPlaces) {
            $normalized = $this->normalizeKey($value);
            if (!$workPlaces->contains('value', $normalized)) {
                $workPlaces->push([
                    'value' => $normalized,
                    'name' => $this->formatWorkPlaceName($value),
                    'count' => (int) $count,
                ]);
            }
        });

        $experienceCounts = JobPost::where('is_active', true)
            ->whereNotNull('experience')
            ->select('experience', DB::raw('COUNT(*) as total'))
            ->groupBy('experience')
            ->orderBy('experience')
            ->pluck('total', 'experience');

        // Get experience levels directly from database (no hardcoded values)
        $experienceLevels = $experienceCounts->map(function ($count, $label) {
            return [
                'value' => $label,
                'name' => $label,
                'count' => (int) $count,
            ];
        })->values();

        // Get salary range
        $salaryRange = JobPost::where('is_active', true)
            ->select(
                DB::raw('MIN(COALESCE(salary_min, 0)) as min_salary'),
                DB::raw('MAX(COALESCE(salary_max, 0)) as max_salary')
            )
            ->first();

        // Get skills with job counts
        $skills = Skill::query()
            ->withCount(['jobs' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'slug' => $skill->slug,
                    'category_id' => $skill->category_id,
                    'jobs_count' => $skill->jobs_count ?? 0,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'types' => $types->values(),
                'experience_levels' => $experienceLevels->values(),
                'work_places' => $workPlaces->values(),
                'skills' => $skills,
                'salary_range' => [
                    'min' => (int)($salaryRange->min_salary ?? 0),
                    'max' => (int)($salaryRange->max_salary ?? 0)
                ]
            ]
        ]);
    }

    private function formatTypeName(?string $type): string
    {
        if (!$type) {
            return 'Not specified';
        }

        $definitions = $this->workTypeDefinitions();
        return $definitions[$type] ?? ucfirst(str_replace('-', ' ', $type));
    }

    private function formatWorkPlaceName(?string $workPlace): string
    {
        if (!$workPlace) {
            return 'Not specified';
        }

        $definitions = $this->workPlaceDefinitions();
        return $definitions[$workPlace] ?? ucfirst(str_replace('-', ' ', $workPlace));
    }

    private function workTypeDefinitions(): array
    {
        return [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'freelance' => 'Freelance',
        ];
    }

    private function workPlaceDefinitions(): array
    {
        return [
            'on-site' => 'On-site',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
        ];
    }


    private function normalizeKey(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return Str::slug(Str::lower($value));
    }
}

