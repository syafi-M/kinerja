<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'sac_point'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
