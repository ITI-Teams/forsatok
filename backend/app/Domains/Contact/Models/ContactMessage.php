<?php

namespace App\Domains\Contact\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;

class ContactMessage extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'subject',
        'message',
        'user_id',
        'contactable_id',
        'contactable_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}