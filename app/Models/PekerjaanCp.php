<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PekerjaanCp extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'divisi_id',
        'kerjasama_id',
        'name',
        'type_check'
    ];
    
    public function Kerjasama()
    {
        return $this->belongsTo(Kerjasama::class);
    }
    
    public function User()
    {
        return $this->belongsTo(User::class);
    }
    
    public function Divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}
