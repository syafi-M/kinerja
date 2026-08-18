<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    
    protected $fillable = ['image', 'tanggal_lihat', 'tanggal_tutup', 'tanggal_muncul'];

    protected $casts = [
        'tanggal_muncul' => 'array',
    ];
}
