<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'jabatan_id',
        'client_id',
        'shift_name',
        'jam_start',
        'jam_end',
        'is_overnight',
        'hari',
    ];

    protected $casts = [
        'is_overnight' => 'boolean',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
