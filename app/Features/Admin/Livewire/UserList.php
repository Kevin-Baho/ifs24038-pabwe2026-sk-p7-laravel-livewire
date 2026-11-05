<?php

declare(strict_types=1);

namespace App\Features\Admin\Livewire;

use App\Features\Admin\Services\AdminUserService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class UserList extends Component
{
    public string $search = '';

    public function getUsers(AdminUserService $service): Collection
    {
        $all = $service->getAllUsersWithMetrics();

        if ($this->search !== '') {
            $term = mb_strtolower($this->search);
            $all  = $all->filter(function ($user) use ($term): bool {
                return str_contains(mb_strtolower($user->name), $term)
                    || str_contains(mb_strtolower($user->username), $term);
            })->values();
        }

        return $all;
    }

    public function render(AdminUserService $service): \Illuminate\View\View
    {
        return view('livewire.admin.user-list', [
            'users' => $this->getUsers($service),
        ]);
    }
}
