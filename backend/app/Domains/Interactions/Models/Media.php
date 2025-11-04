<?php

namespace App\Domains\Interactions\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mediable_id', 'mediable_type', 'path', 'type', 'size'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediable()
    {
        return $this->morphTo();
    }
}
