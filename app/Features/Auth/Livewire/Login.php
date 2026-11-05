<?php

declare(strict_types=1);

namespace App\Features\Auth\Livewire;

use App\Features\Auth\Actions\LoginUserAction;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Login extends Component
{
    public string $username = '';
    public string $password = '';
    public bool   $remember = false;

    protected function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function login(LoginUserAction $action): void
    {
        $this->validate();

        $success = $action->execute(
            ['username' => $this->username, 'password' => $this->password],
            $this->remember,
        );

        if (! $success) {
            $this->addError('username', 'Username atau password salah.');
            return;
        }

        Session::regenerate();

        $user = auth()->user();

        if ($user->isAdmin()) {
            $this->redirectRoute('admin.users', navigate: true);
        } else {
            $this->redirectRoute('stocks.index', navigate: true);
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.login');
    }
}
