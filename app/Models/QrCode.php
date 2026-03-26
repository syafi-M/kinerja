<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;
    
     protected $fillable = [
        'qr_code',
        'ruangan_id',
        'kerjasama_id'
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class);
    }
}
