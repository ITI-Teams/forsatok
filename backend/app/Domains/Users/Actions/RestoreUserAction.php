<?php

namespace App\Domains\Users\Actions;
use App\Domains\Users\Models\User;

class RestoreUserAction
{
    public function execute(int $userId) : void
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();
    }
}