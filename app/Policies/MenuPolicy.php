<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOutlet() && $user->outlet !== null;
    }

    public function view(User $user, Menu $menu): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $menu->outlet_id === (int) $user->outlet->id;
    }

    public function create(User $user): bool
    {
        return $user->isOutlet() && $user->outlet !== null;
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $menu->outlet_id === (int) $user->outlet->id;
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $menu->outlet_id === (int) $user->outlet->id;
    }
}
