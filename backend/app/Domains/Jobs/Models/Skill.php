<?php

namespace App\Domains\Jobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Domains\Candidates\Models\CandidateInfo;

class Skill extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['name', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(JobPost::class, 'job_skill');
    }

    public function candidates()
    {
        return $this->belongsToMany(CandidateInfo::class, 'candidate_skill');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($skill) {
            $skill->slug = Str::slug($skill->name);
        });
    }

}
