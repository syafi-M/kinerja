<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableUsername extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];
}
