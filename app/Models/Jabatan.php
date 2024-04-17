<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'divisi_id',
        'code_jabatan',
        'type_jabatan',
        'name_jabatan',
        'kerjasama_id'
    ];
    
      /**
     * Get the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // protected function Kerjasama(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => json_decode($value, true),
    //         set: fn ($value) => json_encode($value),
    //     );
    // } 
    
    protected $casts = [
        'kerjasama_id' => 'array'
    ];

    public function Divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    
    public function Shift()
    {
        return $this->hasMany(Shift::class);
    }
    
    public function User()
    {
        return $this->hasMany(User::class);
    }
    
    public function Kerjasama(): MorphMany
    {
        return $this->morphMany(Kerjasama::class, 'kerjasama');
    }
    
}
