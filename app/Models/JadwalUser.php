<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'tanggal',
        'area_id',
        'status'
    ];

    public function User() {
        return $this->belongsTo(User::class);
    }

    public function Shift()
    {
        return $this->belongsTo(Shift::class);
    }
    
    public function Area()
    {
        return $this->belongsTo(Area::class);
    }
}
