<?php

namespace App\Domains\Jobs\Models;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Location\Models\Locationable;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
    'employer_id',
    'title',
    'category_id',
    'is_active',
    'deadline',
    'experience',
    'location',
    'salary_min',
    'salary_max',
    'description',
    'responsibilities',
    'qualification',
    'benefits',
    'work_type',
    'work_place',
    'views',
];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id')
            ->withTimestamps();
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function savedByCandidates()
    {
        return $this->hasMany(SavedJob::class);
    }

    public function locationable()
    {
        return $this->morphOne(Locationable::class, 'locationable');
    }

    public function location()
    {
        return $this->locationable();
    }
}
