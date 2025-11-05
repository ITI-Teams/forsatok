<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermission extends Component
{
    public $roleId;
    public $selectedPermissions = [];

    public function render()
    {
        return view('livewire.admin.roles.role-permission', [
            'roles' => Role::all(),
            'permissions' => Permission::all(),
        ])->layout('layouts.app');
    }

    public function updatedRoleId($value)
    {
        $role = Role::find($value);
        if ($role) {
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        } else {
            $this->selectedPermissions = [];
        }
    }

    public function updateRolePermissions()
    {
        if (!$this->roleId) {
            session()->flash('message', 'Please select a role first.');
            return;
        }

        $role = Role::findOrFail($this->roleId);
        $role->syncPermissions($this->selectedPermissions);

        session()->flash('message', 'Permissions updated successfully for this role.');
    }
}
