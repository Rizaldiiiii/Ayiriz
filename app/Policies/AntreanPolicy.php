<?php

namespace App\Policies;

use App\Models\Antrean;
use App\Models\User;

class AntreanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOutlet() && $user->outlet !== null;
    }

    public function view(User $user, Antrean $antrean): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $antrean->outlet_id === (int) $user->outlet->id;
    }

    public function confirmPayment(User $user, Antrean $antrean): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $antrean->outlet_id === (int) $user->outlet->id;
    }

    public function delete(User $user, Antrean $antrean): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $antrean->outlet_id === (int) $user->outlet->id;
    }
}
