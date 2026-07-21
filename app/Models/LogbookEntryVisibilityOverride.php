<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookEntryVisibilityOverride extends Model
{
    protected $guarded = [];

    protected $casts = [
        'hidden_from_public' => 'boolean',
    ];
}
