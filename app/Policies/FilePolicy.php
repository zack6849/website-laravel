<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    public function delete(User $user, File $file): bool
    {
        return $file->user_id === $user->id;
    }
}
