<?php

namespace App\Policies;

use App\Models\RoyaltyAudit;
use App\Models\User;

class RoyaltyAuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPusat();
    }

    public function view(User $user, RoyaltyAudit $audit): bool
    {
        return $user->isPusat();
    }

    public function create(User $user): bool
    {
        return $user->isPusat();
    }
}
