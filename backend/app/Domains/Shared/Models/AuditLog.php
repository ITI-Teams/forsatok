<?php

namespace App\Domains\Shared\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'model_type', 'model_id', 'old_values', 'new_values'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
