<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPelanggan()
            || ($user->isOutlet() && $user->outlet !== null)
            || $user->isPusat();
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->isPusat()) {
            return true;
        }

        if ($user->isOutlet()) {
            return $user->outlet !== null && (int) $order->outlet_id === (int) $user->outlet->id;
        }

        if ($user->isPelanggan()) {
            return (int) $order->user_id === (int) $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isPelanggan();
    }

    public function complete(User $user, Order $order): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $order->outlet_id === (int) $user->outlet->id
            && $order->isDiproses();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $order->outlet_id === (int) $user->outlet->id
            && $order->isDiproses();
    }
}
