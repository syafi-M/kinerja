<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $casts = [
        'tanggal' => 'date',
        'has_read' => 'boolean',
        'has_complete' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'tanggal',
        'deskripsi',
        'has_read',
        'has_complete'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkPoints()
    {
        return $this->hasMany(CheckPoint::class, 'work_order_id');
    }
}
