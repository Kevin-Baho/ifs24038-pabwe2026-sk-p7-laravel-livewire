<?php

declare(strict_types=1);

namespace App\Features\Profile\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function update(User $user, array $data, ?UploadedFile $photo = null): User
    {
        if ($photo !== null) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $photo->store('avatars', 'public');
        }

        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh() ?? $user;
    }
}
