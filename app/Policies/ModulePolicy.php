<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Authorization\FitControlPermissions;

abstract class ModulePolicy
{
    protected string $module;

    public function before(User $user): ?bool
    {
        return $user->hasRole(FitControlPermissions::SUPER_ADMIN_ROLE) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'viewAny');
    }

    public function view(User $user): bool
    {
        return $this->allows($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'create');
    }

    public function update(User $user): bool
    {
        return $this->allows($user, 'update');
    }

    public function delete(User $user): bool
    {
        return $this->allows($user, 'delete');
    }

    public function changeStatus(User $user): bool
    {
        return $this->allows($user, 'changeStatus');
    }

    public function manage(User $user): bool
    {
        return $this->allows($user, 'manage');
    }

    protected function allows(User $user, string $action): bool
    {
        return $user->hasPermissionTo(FitControlPermissions::name($this->module, $action));
    }
}
