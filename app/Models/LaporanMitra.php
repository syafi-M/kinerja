<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMitra extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'kerjasama_id',
        'file_pdf'
        ];
    
    public function Kerjasama()
    {
        return $this->belongsTo(Kerjasama::class);
    }    
}
