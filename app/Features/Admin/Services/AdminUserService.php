<?php

declare(strict_types=1);

namespace App\Features\Admin\Services;

use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminUserService
{
    /**
     * @return Collection<int, User>
     */
    public function getAllUsersWithMetrics(): Collection
    {
        return User::withCount('stocks')
            ->withSum('stocks', 'price')
            ->orderBy('name')
            ->get();
    }

    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Get stocks belonging to a user (admin read-only view).
     *
     * @return Collection<int, Stock>
     */
    public function getUserStocks(User $user, string $search = ''): Collection
    {
        $query = $user->stocks();

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->get();
    }
}
