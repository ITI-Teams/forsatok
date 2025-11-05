<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Models\User;

class SoftDeleteUserAction
{
    public function execute(User $user): void
    {
        $user->delete();
    }
}
