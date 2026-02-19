<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapDueDateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'due_date',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
