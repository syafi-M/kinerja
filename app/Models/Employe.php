<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Employe extends Model
{
    use HasApiTokens;
    protected $connection = 'mysql2';
    protected $table = 'employes';
    
    protected $fillable = [
        'name',
        'ttl',
        'nik',
        'no_kk',
        'no_ktp',
        'client_id',
        'numbers',
        'initials',
        'date_real',
    ];
}
