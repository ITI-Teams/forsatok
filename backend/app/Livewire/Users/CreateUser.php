<?php

namespace App\Http\Livewire\Users;

use Livewire\Component;
use App\Domains\Users\Requests\CreateUserRequest;
use App\Domains\Users\Actions\CreateUserAction;

class CreateUser extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected function rules(): array
    {
        // reuse the Request rules so validation stays DRY
        return CreateUserRequest::creationRules();
    }

    public function submit(CreateUserAction $action)
    {
        // validate livewire data against the same rules
        $data = $this->validate();

        // call the application action to create the user
        $user = $action->execute($data);

        // optionally emit an event or redirect
        session()->flash('success', 'User created successfully.');

        return redirect()->route('users.show', $user->id);
    }

    public function render()
    {
        return view('livewire.users.create-user');
    }
}
