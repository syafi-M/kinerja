<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A single message in the two-way conversation attached to one
 * `check_point_item`: management's review notes and the employee's replies.
 */
class CheckPointMessage extends Model
{
    public const ROLE_MANAGEMENT = 'management';

    public const ROLE_EMPLOYEE = 'employee';

    /** Maximum reply attachments accepted per message. */
    public const MAX_IMAGES = 5;

    /** Longest voice note accepted, in seconds (2 minutes). */
    public const MAX_AUDIO_SECONDS = 120;

    /** Folder inside the `public` disk that holds recorded voice notes. */
    public const VOICE_DIRECTORY = 'voices';

    protected $fillable = [
        'check_point_item_id',
        'user_id',
        'sender_role',
        'body',
        'retracted_at',
        'audio_path',
        'audio_duration',
    ];

    protected $casts = [
        'retracted_at' => 'datetime',
        'audio_duration' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(CheckPointItem::class, 'check_point_item_id');
    }

    /** Attachments, in the order the sender picked them. */
    public function images(): HasMany
    {
        return $this->hasMany(CheckPointMessageImage::class)->orderBy('urutan');
    }

    /**
     * Sender. Users are soft-deleted, so the author of an audit-trail message
     * must stay resolvable after their account is deactivated.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function isFromManagement(): bool
    {
        return $this->sender_role === self::ROLE_MANAGEMENT;
    }

    /**
     * Whether the sender pulled this message back. A retracted message keeps
     * its row — so the thread still shows where it sat — but has no body, no
     * attachments and no voice note left.
     */
    public function isRetracted(): bool
    {
        return $this->retracted_at !== null;
    }
}
