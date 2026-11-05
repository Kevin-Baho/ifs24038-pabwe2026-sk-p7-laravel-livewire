<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Profile\Services\ProfileService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProfileService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProfileService();
    }

    public function test_update_profile_info_and_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $updated = $this->service->update($user, [
            'name'     => 'New Name',
            'username' => 'newuser',
            'email'    => 'new@example.com',
            'password' => 'new-secret-password',
        ]);

        $this->assertSame('New Name', $updated->name);
        $this->assertSame('newuser', $updated->username);
        $this->assertSame('new@example.com', $updated->email);
        $this->assertTrue(Hash::check('new-secret-password', $updated->password));
    }

    public function test_update_profile_photo_and_deletes_old(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'profile_photo_path' => 'avatars/old.jpg',
        ]);
        Storage::disk('public')->put('avatars/old.jpg', 'old-content');

        $newPhoto = UploadedFile::fake()->image('new.jpg');
        $updated  = $this->service->update($user, ['name' => 'Updated'], $newPhoto);

        $this->assertNotNull($updated->profile_photo_path);
        Storage::disk('public')->assertMissing('avatars/old.jpg');
        Storage::disk('public')->assertExists($updated->profile_photo_path);
    }
}

