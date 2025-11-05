<?php
namespace App\Domains\Users\Actions;

use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
       $user->update($data);
       return $user;
    }
}
