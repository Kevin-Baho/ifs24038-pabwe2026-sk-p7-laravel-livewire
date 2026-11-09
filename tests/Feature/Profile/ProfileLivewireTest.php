<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

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

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name'     => 'Alice Smith',
            'username' => 'alicesmith',
            'email'    => 'alice@example.com',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_profile_page_renders(): void
    {
        $this->actingAs($this->user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSeeLivewire(Profile::class)
            ->assertSee('Alice Smith');
    }

    public function test_update_profile_info_and_password(): void
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('name', 'Alice Johnson')
            ->set('username', 'alicejohnson')
            ->set('email', 'alice.j@example.com')
            ->set('password', 'newsecret123')
            ->set('password_confirmation', 'newsecret123')
            ->call('save')
            ->assertHasNoErrors();

        $fresh = $this->user->fresh();
        $this->assertSame('Alice Johnson', $fresh->name);
        $this->assertSame('alicejohnson', $fresh->username);
        $this->assertSame('alice.j@example.com', $fresh->email);
        $this->assertTrue(Hash::check('newsecret123', $fresh->password));
    }

    public function test_update_avatar_photo(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image('profile.jpg', 300, 300);

        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('photo', $photo)
            ->call('save')
            ->assertHasNoErrors();

        $fresh = $this->user->fresh();
        $this->assertNotNull($fresh->profile_photo_path);
        Storage::disk('public')->assertExists($fresh->profile_photo_path);
    }
}
