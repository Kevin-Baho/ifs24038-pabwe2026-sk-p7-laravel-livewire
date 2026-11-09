<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_roles(): void
    {
        $admin = User::factory()->admin()->create();
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isMember());

        $member = User::factory()->member()->create();
        $this->assertTrue($member->isMember());
        $this->assertFalse($member->isAdmin());
    }

    public function test_user_stocks_relation(): void
    {
        $user = User::factory()->create();
        Stock::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->stocks);
        $this->assertInstanceOf(Stock::class, $user->stocks->first());
    }

    public function test_profile_photo_url(): void
    {
        Storage::fake('public');

        // Fallback UI Avatars
        $user = User::factory()->create(['name' => 'Alice Bob', 'profile_photo_path' => null]);
        $url = $user->profilePhotoUrl();
        $this->assertStringContainsString('ui-avatars.com', $url);
        $this->assertStringContainsString('Alice+Bob', $url);

        // Fallback with empty name
        $userNoName = User::factory()->create(['name' => '', 'profile_photo_path' => null]);
        $this->assertStringContainsString('User', $userNoName->profilePhotoUrl());

        // File path set but file missing from storage -> fallback
        $user->profile_photo_path = 'avatars/missing.png';
        $this->assertStringContainsString('ui-avatars.com', $user->profilePhotoUrl());

        // File exists in disk
        Storage::disk('public')->put('avatars/avatar.png', 'image-content');
        $user->profile_photo_path = 'avatars/avatar.png';
        $this->assertStringContainsString('avatars/avatar.png', $user->profilePhotoUrl());
    }
}
