<?php

namespace App\Domains\Users\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $table = 'email_verifications';

    protected $fillable = ['user_id','code','expires_at','used'];

    protected $casts = ['expires_at' => 'datetime', 'used' => 'boolean'];

    public function isExpired(): bool
    {
        return $this->expires_at->lt(Carbon::now());
    }
}
