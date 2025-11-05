<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Models\User;


class DeleteUserAction
{
    public function execute(int $userId): void
    {
        $user = User::onlTrashed()->findOrFail($userId);
        $user->forceDelete();
    }
}