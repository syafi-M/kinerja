<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedTraining extends Model
{
    use HasFactory;

    protected $appends = [
        'masa_training_hari',
    ];

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

    public function getMasaTrainingHariAttribute(): ?int
    {
        if (empty($this->date_in) || empty($this->date_finish_train)) {
            return null;
        }

        return \Carbon\Carbon::parse($this->date_in)
            ->startOfDay()
            ->diffInDays(\Carbon\Carbon::parse($this->date_finish_train)->startOfDay());
    }
}
