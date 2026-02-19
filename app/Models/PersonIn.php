<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'client_id',
        'jabatan_id',
        'date_in',
        'method_salary',
        'method_salary_manual',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}
