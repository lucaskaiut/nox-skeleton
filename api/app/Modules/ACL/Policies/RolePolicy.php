<?php

namespace App\Modules\ACL\Policies;

use App\Modules\ACL\Enums\Permission;
use App\Modules\ACL\Models\Role;
use App\Modules\User\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ROLE_READ);
    }

    public function view(User $user, Role $role): bool
    {
        return $this->sameTenant($user, $role)
            && $user->hasPermission(Permission::ROLE_READ);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::ROLE_CREATE);
    }

    public function update(User $user, Role $role): bool
    {
        return $this->sameTenant($user, $role)
            && ! $role->isDefault()
            && $user->hasPermission(Permission::ROLE_UPDATE);
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->sameTenant($user, $role)
            && ! $role->isDefault()
            && $user->hasPermission(Permission::ROLE_DELETE);
    }

    private function sameTenant(User $user, Role $role): bool
    {
        return $user->tenant_id === $role->tenant_id;
    }
}
