<?php

namespace App\Domains\Jobs\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SavedJob extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'job_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(JobPost::class);
    }
}
