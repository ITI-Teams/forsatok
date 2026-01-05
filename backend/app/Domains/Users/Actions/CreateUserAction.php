<?php


namespace App\Domains\Users\Actions;


use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;


class CreateUserAction
{
    public function execute(array $data): User
    {
        if (!isset($data['status'])) {
            $data['status'] = 'approved';
        }
        return User::create($data);
    }
}

