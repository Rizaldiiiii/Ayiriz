<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return ($user->isOutlet() && $user->outlet !== null) || $user->isPusat();
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->isPusat()) {
            return true;
        }

        if ($user->isOutlet()) {
            return $user->outlet !== null && (int) $order->outlet_id === (int) $user->outlet->id;
        }

        return false;
    }

    public function complete(User $user, Order $order): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $order->outlet_id === (int) $user->outlet->id
            && $order->isDiproses();
    }
}
