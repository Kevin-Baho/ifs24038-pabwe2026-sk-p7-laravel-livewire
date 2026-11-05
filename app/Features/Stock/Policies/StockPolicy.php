<?php

declare(strict_types=1);

namespace App\Features\Stock\Policies;

use App\Features\Stock\Models\Stock;
use App\Models\User;

class StockPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin can view all; member can view own only.
     */
    public function view(User $user, Stock $stock): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $stock->user_id;
    }

    /**
     * Only the owner can update.
     */
    public function update(User $user, Stock $stock): bool
    {
        return $user->id === $stock->user_id;
    }

    /**
     * Only the owner can delete.
     */
    public function delete(User $user, Stock $stock): bool
    {
        return $user->id === $stock->user_id;
    }

    /**
     * Only members can create stocks (admin is read-only).
     */
    public function create(User $user): bool
    {
        return $user->isMember();
    }
}
