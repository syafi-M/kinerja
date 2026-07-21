<?php

namespace App\Models;

use Carbon\Carbon;
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
        'due_date' => 'datetime',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Monthly recurring deadline for the given month (default: current).
     * Day is clamped to days-in-month (e.g. 31 → 28/29 in Feb).
     */
    public function deadlineFor(?Carbon $at = null): Carbon
    {
        $at = ($at ?? now())->copy();
        $source = Carbon::parse($this->due_date);
        $day = min((int) $source->day, $at->daysInMonth);

        return $at->copy()
            ->startOfDay()
            ->day($day)
            ->setTime((int) $source->hour, (int) $source->minute, (int) $source->second);
    }

    public function isLockedAt(?Carbon $at = null): bool
    {
        $at = $at ?? now();

        return $at->gte($this->deadlineFor($at));
    }

    public function label(): string
    {
        $source = Carbon::parse($this->due_date);

        return 'Setiap tgl '.$source->format('j').' pukul '.$source->format('H:i');
    }
}
