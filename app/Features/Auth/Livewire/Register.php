<?php

declare(strict_types=1);

namespace App\Features\Auth\Livewire;

use App\Features\Auth\Actions\RegisterUserAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public string $name     = '';
    public string $username = '';
    public string $email    = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function register(RegisterUserAction $action): void
    {
        $this->validate();

        $user = $action->execute([
            'name'     => $this->name,
            'username' => $this->username,
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        Auth::login($user);

        $this->redirectRoute('stocks.index', navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.register');
    }
}
