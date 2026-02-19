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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
