<?php

namespace App\Modules\User\Policies;

use App\Modules\ACL\Enums\Permission;
use App\Modules\User\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::USER_READ);
    }

    public function view(User $user, User $model): bool
    {
        return $this->sameTenant($user, $model)
            && $user->hasPermission(Permission::USER_READ);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::USER_CREATE);
    }

    public function update(User $user, User $model): bool
    {
        return $this->sameTenant($user, $model)
            && $user->hasPermission(Permission::USER_UPDATE);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->sameTenant($user, $model)
            && $user->isNot($model)
            && $user->hasPermission(Permission::USER_DELETE);
    }

    private function sameTenant(User $user, User $model): bool
    {
        return $user->tenant_id === $model->tenant_id;
    }
}
