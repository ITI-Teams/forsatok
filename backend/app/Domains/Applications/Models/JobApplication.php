<?php

namespace App\Domains\Applications\Models;

use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['job_id', 'candidate_id', 'status', 'cv_path', 'cover_letter'];

    public function job()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }
}
