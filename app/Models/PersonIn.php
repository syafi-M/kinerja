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
        'user_id',
        'jabatan_id',
        'date_in',
        'total_mk',
        'method_salary',
        'method_salary_manual',
        'additional_reason',
        'status',
        'created_by_user_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
