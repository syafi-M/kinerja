<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPekerjaan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'ruangan_id'
    ];
    
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
