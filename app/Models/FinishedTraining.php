<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_in',
        'date_finish_train',
        'desc',
        'status',
        'created_by_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
