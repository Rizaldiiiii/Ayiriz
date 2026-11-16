<?php

namespace App\Policies;

use App\Models\Outlet;
use App\Models\User;

class OutletPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPusat();
    }

    public function view(User $user, Outlet $outlet): bool
    {
        if ($user->isPusat()) {
            return true;
        }

        return $user->isOutlet() && (int) $outlet->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isPusat();
    }

    public function update(User $user, Outlet $outlet): bool
    {
        return $user->isPusat();
    }

    public function changeStatus(User $user, Outlet $outlet): bool
    {
        return $user->isPusat();
    }
}
