<?php

declare(strict_types=1);

namespace App\Features\Admin\Livewire;

use App\Features\Admin\Services\AdminUserService;
use App\Features\Stock\Services\StockExcelService;
use App\Models\User;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserStockList extends Component
{
    public User   $targetUser;
    public string $search = '';

    public function mount(User|int|string $user, AdminUserService $service): void
    {
        if ($user instanceof User) {
            $this->targetUser = $user;
        } else {
            $this->targetUser = $service->getUserById((int) $user);
        }
    }

    public function exportExcel(StockExcelService $excelService): StreamedResponse
    {
        return $excelService->export($this->targetUser->stocks()->get());
    }

    public function render(AdminUserService $service): \Illuminate\View\View
    {
        return view('livewire.admin.admin-user-stock-list', [
            'stocks' => $service->getUserStocks($this->targetUser, $this->search),
        ]);
    }
}
