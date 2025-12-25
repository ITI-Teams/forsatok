<?php

namespace App\Domains\Users\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Applications\Models\JobApplication;
use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Employers\Models\CompanyReview;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Models\SavedJob;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;
use App\Notifications\Auth\CustomResetPasswordNotification;
use App\Notifications\Auth\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $source = app(FrontendUrlService::class)->getSource();
        $this->notify(new CustomResetPasswordNotification($token, $source));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $source = app(FrontendUrlService::class)->getSource();
        $this->notify(new CustomVerifyEmail($source));
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url'];

    /**
     * Get the user's avatar URL.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return asset('storage/avatars/avatar.svg');
        }

        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        return Storage::disk('public')->url($this->avatar);
    }

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
        'google_id',
        'avatar',
        'email_verified_at',
        'status',
        'approved_by',
        'approved_at',
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
            'approved_at' => 'datetime',
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

    public function employerInfo()
    {
        return $this->hasOne(EmployerInfo::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
