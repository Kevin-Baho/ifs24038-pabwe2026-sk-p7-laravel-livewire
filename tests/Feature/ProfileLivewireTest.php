<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Features\Profile\Livewire\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_loads_and_updates_profile(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'name'     => 'Original Name',
            'username' => 'original',
            'email'    => 'orig@example.com',
        ]);

        $this->actingAs($user);

        $this->get(route('profile'))->assertStatus(200);

        $photo = UploadedFile::fake()->image('avatar.jpg');

        Livewire::test(Profile::class)
            ->assertSet('name', 'Original Name')
            ->assertSet('username', 'original')
            ->assertSet('email', 'orig@example.com')
            ->set('name', 'Updated Name')
            ->set('password', 'newsecret123')
            ->set('password_confirmation', 'newsecret123')
            ->set('photo', $photo)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Profil berhasil diperbarui.');

        $fresh = $user->fresh();
        $this->assertSame('Updated Name', $fresh->name);
        $this->assertTrue(Hash::check('newsecret123', $fresh->password));
        $this->assertNotNull($fresh->profile_photo_path);
    }
}

