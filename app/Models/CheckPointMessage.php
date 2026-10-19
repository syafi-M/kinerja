<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * The confirmation checklist an employee answers a management reply with.
     * The keys are what is stored; the labels are what both sides read. The
     * answers are independent — one, two or all three may be ticked.
     */
    public const CONFIRM_CHOICES = [
        'paham' => 'Telah Dipahami/Dimengerti',
        'siap' => 'Siap Dilaksanakan',
        'lainnya' => 'Lainnya',
    ];

    /**
     * A Remix Icon name per answer. The checklist is rendered as icons, so the
     * mapping lives here once: the Blade partial, the JSON payload the thread
     * renders a fresh bubble from, and the tests all read it from here instead
     * of repeating it. Every name is one the built stylesheet really ships.
     */
    public const CONFIRM_CHOICE_ICONS = [
        'paham' => 'ri-checkbox-circle-fill',
        'siap' => 'ri-rocket-2-fill',
        'lainnya' => 'ri-more-fill',
    ];

    /** The icon in front of the free text the "Lainnya" answer carries. */
    public const CONFIRM_NOTE_ICON = 'ri-chat-1-line';

    /** Longest free text the "Lainnya" answer may carry. */
    public const MAX_CONFIRM_NOTE = 1000;

    protected $fillable = [
        'check_point_item_id',
        'user_id',
        'sender_role',
        'requires_confirmation',
        'body',
        'retracted_at',
        'audio_path',
        'audio_duration',
    ];

    protected $casts = [
        'retracted_at' => 'datetime',
        'audio_duration' => 'integer',
        'requires_confirmation' => 'boolean',
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
     * The read/confirmation bookkeeping for this message. Only management
     * replies carry one, because only they are asked to be confirmed.
     */
    public function acknowledgement(): HasOne
    {
        return $this->hasOne(CheckPointMessageAcknowledgement::class, 'check_point_message_id');
    }

    /**
     * Whether this message awaits the employee's checklist: it asks for a
     * confirmation and has not been answered yet. A retracted message can never
     * be pending — there is nothing left to read or confirm.
     */
    public function awaitsConfirmation(): bool
    {
        return $this->requires_confirmation
            && ! $this->isRetracted()
            && ! $this->acknowledgement?->isConfirmed();
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

    /**
     * Human labels for a stored answer list, skipping keys that no longer exist.
     *
     * @param  array<int, string>  $choices
     * @return array<int, string>
     */
    public static function confirmChoiceLabels(array $choices): array
    {
        return array_values(array_filter(array_map(
            fn (string $choice): ?string => self::CONFIRM_CHOICES[$choice] ?? null,
            $choices,
        )));
    }

    /**
     * A stored answer list as the view and the JSON payload both want it: the
     * key, the label a human reads, and the icon that stands in front of it.
     *
     * @param  array<int, string>  $choices
     * @return array<int, array{key: string, label: string, icon: string}>
     */
    public static function confirmChoiceDetails(array $choices): array
    {
        return array_values(array_filter(array_map(function (string $choice): ?array {
            if (! isset(self::CONFIRM_CHOICES[$choice])) {
                return null;
            }

            return [
                'key' => $choice,
                'label' => self::CONFIRM_CHOICES[$choice],
                'icon' => self::CONFIRM_CHOICE_ICONS[$choice] ?? 'ri-checkbox-circle-fill',
            ];
        }, $choices)));
    }
}
