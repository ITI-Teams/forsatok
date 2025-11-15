<?php

namespace App\Domains\Employers\Models;

use App\Domains\Jobs\Models\JobPost;
use App\Domains\CompanyReviews\Models\CompanyReview;
use App\Domains\Location\Models\Locationable;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class EmployerInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_name', 'website', 'industry', 'about', 'logo_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        // employer_id in JobPost refers to user_id (employer user), not employer_info id
        return $this->hasMany(JobPost::class, 'employer_id', 'user_id');
    }

    public function reviews()
    {

        // company_id in CompanyReview refers to user_id (employer user), not employer_info id
        return $this->hasMany(CompanyReview::class, 'company_id', 'user_id');
    }

    /**
     * Calculate average rating for this company
     */
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get total reviews count
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function location()
    {
        return $this->morphOne(Locationable::class, 'locationable');
    }
}
