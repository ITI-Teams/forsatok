<?php

namespace App\Domains\Jobs\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostDecision extends Model
{
    protected $fillable = [
        'job_post_id',
        'admin_id',
        'from_status',
        'to_status',
        'reason',
    ];
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }
}


