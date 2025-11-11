<?php

namespace App\Domains\Users\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Applications\Models\JobApplication;
use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Employers\Models\CompanyReview;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Models\SavedJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'type',
        'password',
        'linkedin_id',
        'avatar',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'candidate_id');
    }

    public function savedJobs()
    {
        return $this->hasMany(SavedJob::class);
    }

    public function companyReviews()
    {
        return $this->hasMany(CompanyReview::class, 'user_id');
    }
    public function jobPosts()
    {
        return $this->hasMany(JobPost::class, 'employer_id');
    }
    public function candidateInfo()
    {
        return $this->hasOne(CandidateInfo::class, 'user_id');
    }
}
