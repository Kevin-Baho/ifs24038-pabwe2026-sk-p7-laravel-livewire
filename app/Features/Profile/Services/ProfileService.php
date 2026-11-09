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

            $croppedPng = $this->cropToCenterPng($photo->getRealPath());
            if ($croppedPng !== null) {
                $filename = 'avatars/' . uniqid('avatar_', true) . '.png';
                Storage::disk('public')->put($filename, $croppedPng);
                $data['profile_photo_path'] = $filename;
            } else {
                $data['profile_photo_path'] = $photo->store('avatars', 'public');
            }
        }

        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh() ?? $user;
    }

    public function cropToCenterPng(string $filePath): ?string
    {
        $raw = @file_get_contents($filePath);
        if ($raw === false) {
            return null;
        }

        $src = @imagecreatefromstring($raw);
        if ($src === false) {
            return null;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);
        $size  = min($origW, $origH);
        $srcX  = (int) (($origW - $size) / 2);
        $srcY  = (int) (($origH - $size) / 2);

        $dst = imagecreatetruecolor(400, 400);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, 400, 400, $size, $size);

        ob_start();
        imagepng($dst);
        $pngData = (string) ob_get_clean();

        imagedestroy($dst);
        imagedestroy($src);

        return $pngData;
    }
}
