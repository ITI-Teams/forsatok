<?php

namespace App\Livewire\User;

use App\Domains\Users\Actions\CreateUserAction;
use App\Domains\Users\Actions\UpdateUserAction;
use App\Domains\Users\Requests\UpdateUserRequest;
use App\Domains\Users\Requests\StoreUserRequest;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class UserForm extends Component
{
    public $userId, $name, $email, $password, $password_confirmation, $type;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'type' => 'required|in:admin,employer,candidate',
    ];

    public function mount($user = null)
    {
        if ($user) {
            $model = User::findOrFail($user);
            $this->userId = $model->id;
            $this->name = $model->name;
            $this->email = $model->email;
            $this->password = '';
            $this->password_confirmation = '';
            $this->type = $model->type;
        }
    }

    public function save(CreateUserAction $create, UpdateUserAction $update)
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
        ];

        if ($this->userId) {
            $data['user_id'] = $this->userId;

            if ($this->password) {
                $data['password'] = $this->password;
                $data['password_confirmation'] = $this->password_confirmation;
            }

            $rules = (new UpdateUserRequest())->rules();
            $messages = (new UpdateUserRequest())->messages() ?? [];
            $attributes = (new UpdateUserRequest())->attributes() ?? [];

            $validated = Validator::make($data, $rules, $messages, $attributes)->validate();
        } else {
            $data['password'] = $this->password;
            $data['password_confirmation'] = $this->password_confirmation;

            $rules = (new StoreUserRequest())->rules();
            $messages = (new StoreUserRequest())->messages() ?? [];
            $attributes = (new StoreUserRequest())->attributes() ?? [];

            $validated = Validator::make($data, $rules, $messages, $attributes)->validate();
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $update->execute($user, $validated);
            session()->flash('message', 'User updated successfully!');
        } else {
            $create->execute($validated);
            session()->flash('message', 'User created successfully!');
        }

        return $this->redirectRoute('users.index', navigate: true);
    }

    public function cancel()
    {
        return $this->redirectRoute('users.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.users.user-form')->layout('layouts.app');
    }
}
