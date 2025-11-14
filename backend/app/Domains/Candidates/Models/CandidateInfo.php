<?php

namespace App\Domains\Candidates\Models;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Location\Models\Locationable;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CandidateInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'phone',
        'resume',
        'education',
        'experience',
        'bio',
        'gender',
        'date_of_birth',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'candidate_skill');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'candidate_id');
    }

    public function location()
    {
        return $this->morphOne(Locationable::class, 'locationable');
    }
}
