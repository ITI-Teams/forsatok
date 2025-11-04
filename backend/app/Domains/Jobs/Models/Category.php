<?php

namespace App\Domains\Jobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Jobs\Models\JobPost;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['name'];

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function jobs()
    {
        return $this->hasMany(JobPost::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
