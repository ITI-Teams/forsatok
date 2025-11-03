<?php


namespace App\Application\Users\Actions;


use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;


class CreateUserAction
{
    public function execute(array $data): User
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];


        return User::create($payload);
    }
}

