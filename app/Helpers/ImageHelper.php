<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Str;

class ImageHelper
{
    public static function saveProfilePicture($file, User $user): void
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        $path = $file->storeAs('uploads', $fileName, 'public');
        $user->profile_picture = $path;
        $user->save();
    }
}
