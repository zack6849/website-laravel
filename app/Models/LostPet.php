<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostPet extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'intake_date' => 'datetime',
    ];
}
