<?php

namespace App\Domains\Candidates\Models;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CandidateInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bio', 'phone', 'location', 'experience_years', 'education', 'cv_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'candidate_id');
    }
}
