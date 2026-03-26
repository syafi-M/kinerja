<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Kerjasama extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'value',
        'experied',
        'approve1',
        'approve2',
        'approve3',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }
    
    public function jabatan(): MorphTo
    {
        return $this->morphTo();
    }

}
