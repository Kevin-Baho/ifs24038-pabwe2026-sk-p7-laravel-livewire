<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

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

    public function test_update_name_username_email_and_password(): void
    {
        $user = User::factory()->create([
            'name'     => 'Original Name',
            'username' => 'original',
            'email'    => 'orig@example.com',
            'password' => Hash::make('old_pass'),
        ]);

        $updated = $this->service->update($user, [
            'name'     => 'New Name',
            'username' => 'newuser',
            'email'    => 'new@example.com',
            'password' => 'secret123',
        ]);

        $this->assertSame('New Name', $updated->name);
        $this->assertSame('newuser', $updated->username);
        $this->assertSame('new@example.com', $updated->email);
        $this->assertTrue(Hash::check('secret123', $updated->password));

        // Update without changing password
        $updated2 = $this->service->update($updated, [
            'name'     => 'Another Name',
            'password' => '',
        ]);
        $this->assertSame('Another Name', $updated2->name);
        $this->assertTrue(Hash::check('secret123', $updated2->password));
    }

    public function test_update_avatar_with_gd_cropping(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['profile_photo_path' => null]);

        $file = UploadedFile::fake()->image('avatar.jpg', 600, 400);

        $updated = $this->service->update($user, ['name' => 'User With Photo'], $file);

        $this->assertNotNull($updated->profile_photo_path);
        $this->assertStringEndsWith('.png', $updated->profile_photo_path);
        Storage::disk('public')->assertExists($updated->profile_photo_path);

        // Update with another photo -> old photo should be deleted
        $oldPath = $updated->profile_photo_path;
        $file2 = UploadedFile::fake()->image('avatar2.png', 500, 500);
        $updated2 = $this->service->update($updated, [], $file2);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($updated2->profile_photo_path);

        // Fallback when photo cannot be cropped via GD
        $invalidPhoto = UploadedFile::fake()->create('invalid.txt', 10);
        $updatedFallback = $this->service->update($updated2, [], $invalidPhoto);
        $this->assertNotNull($updatedFallback->profile_photo_path);
        Storage::disk('public')->assertExists($updatedFallback->profile_photo_path);
    }

    public function test_crop_to_center_png_handles_invalid_files(): void
    {
        // Non-existent file
        $this->assertNull($this->service->cropToCenterPng('/path/to/nothing.png'));

        // Non-image file
        $temp = tempnam(sys_get_temp_dir(), 'test_not_img');
        file_put_contents($temp, 'Not an image data');
        $this->assertNull($this->service->cropToCenterPng($temp));
        @unlink($temp);
    }
}
