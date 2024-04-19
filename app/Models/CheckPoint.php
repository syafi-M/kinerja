<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckPoint extends Model
{
    use HasFactory;
    
    protected $casts = [
        'pekerjaan_cp_id' => 'array',
        'img' => 'array',
        'approve_status' => 'array',
        'note' => 'array',
        'deskripsi' => 'array'
        ];

    protected $fillable = [
        'user_id',
        'divisi_id',
        'pekerjaan_cp_id',
        'type_check',
        'img',
        'deskripsi',
        'approve_status',
        'latitude',
        'longtitude',
        'note'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    
    public function PekerjaanCp()
    {
        return $this->belongsTo(PekerjaanCp::class);
    }
}
