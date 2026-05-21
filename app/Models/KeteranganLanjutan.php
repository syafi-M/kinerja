<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeteranganLanjutan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'keterangan',
    ];

    protected $casts = [
        'keterangan' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
