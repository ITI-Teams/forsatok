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
     * Add (not sync) roles and permissions to user
     */
    public function updateUserRolesPermissions()
    {
        if (!$this->user_id) {
            session()->flash('message', 'Please select a user first.');
            return;
        }

        $user = User::findOrFail($this->user_id);

        // ✅ Add new roles only (without removing existing)
        foreach ($this->selectedRoles as $roleName) {
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }

        // ✅ Add new permissions only (without removing existing)
        foreach ($this->selectedPermissions as $permissionName) {
            if (!$user->hasPermissionTo($permissionName)) {
                $user->givePermissionTo($permissionName);
            }
        }

        session()->flash('message', 'New roles and permissions added successfully.');
    }

}
