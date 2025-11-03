<?php

namespace App\Domains\Employers\Models;

use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class EmployerInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_name', 'website', 'industry', 'description', 'location', 'phone', 'logo_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(JobPost::class, 'employer_id');
    }
}
