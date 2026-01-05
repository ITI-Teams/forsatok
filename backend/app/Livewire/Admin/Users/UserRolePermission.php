<?php

namespace App\Livewire\Admin\Users;

use App\Domains\Users\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRolePermission extends Component
{
    public $user_id;
    public $selectedRoles = [];
    public $selectedPermissions = [];

    public function render()
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        return view('livewire.admin.users.user-role-permission', [
            'users' => User::all(),
            'roles' => Role::all(),
            'permissions' => Permission::all(),
        ])->layout('layouts.app');
    }

    /**
     * Fired when user_id changes (Livewire v3 syntax)
     */
    public function updatedUserId($value)
    {
        $user = User::find($value);
        if ($user) {
            $this->selectedRoles = $user->roles->pluck('name')->toArray();
            $this->selectedPermissions = $user->permissions->pluck('name')->toArray();
        }
    }

    /**
     * Sync roles and permissions for the selected user
     */
    public function updateUserRolesPermissions()
    {
        if (!$this->user_id) {
            session()->flash('message', 'Please select a user first.');
            return;
        }

        $user = User::findOrFail($this->user_id);

        // ✅ Sync roles (adds new, removes unchecked)
        $user->syncRoles($this->selectedRoles);

        // ✅ Sync permissions (adds new, removes unchecked)
        $user->syncPermissions($this->selectedPermissions);

        session()->flash('message', 'Roles and permissions updated successfully.');
    }

}
