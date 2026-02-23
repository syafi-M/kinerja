<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PersonOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_mk',
        'out_date',
        'reason',
        'reason_manual',
        'img',
        'status'
    ];

    protected static function booted()
    {

        static::updating(function ($personOut) {
            DB::transaction(function () use ($personOut) {

                if (! $personOut->isDirty('status')) {
                    return;
                }

                $user = User::withTrashed()
                    ->where('id', $personOut->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $isSubmitted = strcasecmp(trim((string) $personOut->status), 'Di Ajukan') === 0;

                if ($isSubmitted) {

                    if ($user->trashed()) {
                        throw new \Exception('User already inactive');
                    }

                    $user->delete(); // soft delete

                } else {

                    if ($user->trashed()) {
                        $user->restore(); // restore kalau status berubah
                    }
                }
            });
        });


        static::deleting(function ($personOut) {

            DB::transaction(function () use ($personOut) {

                // lock user including trashed
                $user = User::withTrashed()
                    ->where('id', $personOut->user_id)
                    ->lockForUpdate()
                    ->first();

                // restore only if user is soft deleted
                if ($user && $user->trashed()) {
                    $user->restore();
                }
            });
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
