<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaSub extends Model
{
    use HasFactory;
    protected $table = 'area_sub';
    
    protected $fillable = [
        'area_id',
        'subarea_id'
    ];
    
    public function Area()
    {
        return $this->belongsTo(Area::class);
    }
    
    public function Subarea()
    {
        return $this->belongsTo(Subarea::class);
    }
}
