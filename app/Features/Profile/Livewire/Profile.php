<?php

declare(strict_types=1);

namespace App\Features\Profile\Livewire;

use App\Features\Profile\Services\ProfileService;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string  $name                 = '';
    public string  $username             = '';
    public string  $email                = '';
    public string  $password             = '';
    public string  $password_confirmation = '';
    public         $photo                = null;
    public User    $user;

    public function mount(): void
    {
        $this->user     = auth()->user();
        $this->name     = $this->user->name;
        $this->username = $this->user->username;
        $this->email    = $this->user->email;
    }

    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $this->user->id],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save(ProfileService $service): void
    {
        $this->validate();

        $data = [
            'name'     => $this->name,
            'username' => $this->username,
            'email'    => $this->email,
            'password' => $this->password,
        ];

        $this->user = $service->update($this->user, $data, $this->photo);

        $this->password              = '';
        $this->password_confirmation = '';
        $this->photo                 = null;

        session()->flash('status', 'Profil berhasil diperbarui.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profile.profile');
    }
}
