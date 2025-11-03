<?php

namespace App\Domains\Employers\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CompanyReview extends Model
{
    use HasFactory;

    protected $fillable = ['employer_id', 'candidate_id', 'rating', 'comment'];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }
}
