<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kerjasama_id',
        'shift_id',
        'alasan_izin',
        'img',
        'status_aprrove'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
