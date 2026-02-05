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
        'img'
    ];

    protected static function booted()
    {
        static::creating(function ($personOut) {

            DB::transaction(function () use ($personOut) {

                // lock user row to prevent race conditions
                $user = User::where('id', $personOut->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // block if already soft deleted
                if ($user->trashed()) {
                    throw new \Exception('User already inactive');
                }

                // block duplicate PersonOut
                $exists = self::where('user_id', $personOut->user_id)->exists();
                if ($exists) {
                    throw new \Exception('PersonOut already exists for this user');
                }

                // soft delete user safely
                $user->delete();
            });
        });

        static::updating(function ($personOut) {
            DB::transaction(function () use ($personOut) {

                // only act if user_id actually changed
                if (! $personOut->isDirty('user_id')) {
                    return;
                }

                $oldUserId = $personOut->getOriginal('user_id');
                $newUserId = $personOut->user_id;

                // restore old user
                $oldUser = User::withTrashed()
                    ->where('id', $oldUserId)
                    ->lockForUpdate()
                    ->first();

                if ($oldUser && $oldUser->trashed()) {
                    $oldUser->restore();
                }

                // soft delete new user
                $newUser = User::where('id', $newUserId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($newUser->trashed()) {
                    throw new \Exception('New user already inactive');
                }

                if (self::where('user_id', $newUserId)
                    ->where('id', '!=', $personOut->id)
                    ->exists()
                ) {
                    throw new \Exception('PersonOut already exists for this user');
                }

                $newUser->delete();
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
