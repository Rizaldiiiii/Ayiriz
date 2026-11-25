<?php

namespace App\Policies;

use App\Models\Ingredient;
use App\Models\User;

class IngredientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOutlet() && $user->outlet !== null;
    }

    public function view(User $user, Ingredient $ingredient): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $ingredient->outlet_id === (int) $user->outlet->id;
    }

    public function create(User $user): bool
    {
        return $user->isOutlet() && $user->outlet !== null;
    }

    public function update(User $user, Ingredient $ingredient): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $ingredient->outlet_id === (int) $user->outlet->id;
    }

    public function delete(User $user, Ingredient $ingredient): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $ingredient->outlet_id === (int) $user->outlet->id;
    }

    public function recordMovement(User $user, Ingredient $ingredient): bool
    {
        return $user->isOutlet()
            && $user->outlet !== null
            && (int) $ingredient->outlet_id === (int) $user->outlet->id;
    }
}
