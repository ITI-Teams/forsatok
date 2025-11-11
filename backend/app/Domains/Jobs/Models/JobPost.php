<?php

namespace App\Domains\Jobs\Models;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Location\Models\Locationable;

class JobPost extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title', 'description', 'category_id', 'employer_id',
        'location', 'salary_min', 'salary_max', 'type', 'status',
        'experince', 'deadline', 'is_active'
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
        return $this->belongsToMany(Skill::class, 'job_skill');
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
}
