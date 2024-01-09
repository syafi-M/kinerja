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

    public function Ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function Kerjasama()
    {
        return $this->belongsTo(Kerjasama::class);
    }
}
