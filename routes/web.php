<?php

use App\Features\Admin\Livewire\AdminUserStockList;
use App\Features\Admin\Livewire\UserList;
use App\Features\Auth\Livewire\Login;
use App\Features\Auth\Livewire\Register;
use App\Features\Profile\Livewire\Profile;
use App\Features\Stock\Livewire\StockDetail;
use App\Features\Stock\Livewire\StockList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->isAdmin()) {
        return redirect()->route('admin.users');
    }

    return redirect()->route('stocks.index');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:member', 'throttle:1000,1'])->group(function (): void {
    Route::get('/stocks', StockList::class)->name('stocks.index');
    Route::get('/stocks/{stock}', StockDetail::class)->name('stocks.detail')->middleware('owns.stock');
    Route::get('/profile', Profile::class)->name('profile');
});

Route::middleware(['auth', 'role:admin', 'throttle:1000,1'])->group(function (): void {
    Route::get('/admin/users', UserList::class)->name('admin.users');
    Route::get('/admin/users/{user}/stocks', AdminUserStockList::class)->name('admin.users.stocks');
});
