<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\JobPost;
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
            ->pluck('total', 'experience');

        $experienceLabels = collect($this->defaultExperienceLevels())
            ->merge($experienceCounts->keys())
            ->filter()
            ->unique()
            ->values();

        $experienceLevels = $experienceLabels->map(function ($label) use ($experienceCounts) {
            return [
                'value' => $label,
                'name' => $label,
                'count' => (int) ($experienceCounts[$label] ?? 0),
            ];
        });

        // Get salary range
        $salaryRange = JobPost::where('is_active', true)
            ->select(
                DB::raw('MIN(COALESCE(salary_min, 0)) as min_salary'),
                DB::raw('MAX(COALESCE(salary_max, 0)) as max_salary')
            )
            ->first();

        return response()->json([
            'status' => true,
            'data' => [
                'types' => $types->values(),
                'experience_levels' => $experienceLevels->values(),
                'work_places' => $workPlaces->values(),
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
            'remote' => 'Remote',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'temporary' => 'Temporary',
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

    private function defaultExperienceLevels(): array
    {
        return [
            'Entry Level',
            'Junior',
            '1-3 years',
            '3-5 years',
            '5+ years',
            'Senior',
            'Lead',
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

