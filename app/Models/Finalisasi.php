<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finalisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'approve'
    ];
    
    public function Checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
