<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monev extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'kerjasama_id',
        'foto',
    ];
    
    protected $guarded = ['user_id', 'kerjasama_id', 'foto'];
    
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'kerjasama_id', 'id');
    }
}
