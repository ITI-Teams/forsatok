<?php

namespace App\Domains\Users\Actions;

use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;


class DeleteUserAction
{
    public function execute(int $userId): void
    {
        $user = User::withTrashed()->findOrFail($userId);

        if (Auth::id() === $user->id) {
            throw new UnauthorizedException('You cannot delete your own account.');
        }

        if ($user->hasAnyRole(['super admin', 'admin'])) {
            throw new UnauthorizedException('Cannot delete users with administrative roles (super admin or admin).');
        }

        if (! $user->trashed()) {
            throw new \LogicException('User must be soft-deleted before permanent deletion.');
        }

        $user->forceDelete();
    }
}
