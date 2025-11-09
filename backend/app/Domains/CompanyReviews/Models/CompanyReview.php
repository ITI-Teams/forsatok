<?php

namespace App\Domains\CompanyReviews\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Users\Models\User;
use App\Domains\Employers\Models\EmployerInfo;

class CompanyReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'company_reviews';

    protected $fillable = [
        'company_id',
        'candidate_id',
        'rating',
        'review',
    ];

    public function company()
    {
        return $this->belongsTo(EmployerInfo::class, 'company_id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }
}
