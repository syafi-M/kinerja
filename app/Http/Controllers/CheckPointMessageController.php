<?php

namespace App\Http\Controllers;

use App\Models\CheckPointItem;
use App\Models\CheckPointMessage;
use App\Models\User;
use App\Notifications\CheckPointReplyNotification;
use App\Services\CheckPointImageUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Two-way conversation on a single checkpoint item. One endpoint serves both
 * sides because the per-item thread is the same resource for both: the owner
 * (employee) replies to a reviewer's note, and DIREKSI / Manager CS reply back.
 */
class CheckPointMessageController extends Controller
{
    /** Jabatan codes that may speak from the management side of a thread. */
    private const MANAGEMENT_CODES = ['DIREKSI', 'MCS'];

    /** Bytes a voice note may take (2 minutes of Opus/AAC stays far below). */
    private const MAX_VOICE_KB = 6144;

    /**
     * What a browser recorder can hand us, mapped to the extension the file is
     * stored under. finfo reports an audio-only WebM as `video/webm` and a
     * Safari recording as `audio/mp4`/`video/mp4`, so all of them are listed.
     * The extension comes from here, never from the uploaded file name.
     */
    private const VOICE_TYPES = [
        'audio/webm' => 'webm',
        'video/webm' => 'webm',
        'audio/ogg' => 'ogg',
        'video/ogg' => 'ogg',
        'audio/opus' => 'ogg',
        'audio/mp4' => 'm4a',
        'video/mp4' => 'm4a',
        'audio/x-m4a' => 'm4a',
        'audio/aac' => 'aac',
        'audio/mpeg' => 'mp3',
        'audio/wav' => 'wav',
        'audio/wave' => 'wav',
        'audio/x-wav' => 'wav',
    ];

    public function __construct(
        private readonly CheckPointImageUploader $uploader,
    ) {}

    public function store(Request $request, CheckPointItem $item): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $checkPoint = $item->checkPoint;
        $isManagement = $this->isManagement($user);
        $isOwner = $checkPoint !== null && (int) $checkPoint->user_id === (int) $user->id;

        abort_unless($isManagement || $isOwner, 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'images' => ['nullable', 'array', 'max:' . CheckPointMessage::MAX_IMAGES],
            'images.*' => ['image'],
            'audio' => ['nullable', 'file', 'max:' . self::MAX_VOICE_KB],
            // The browser measures the recording and reports it with the blob;
            // a MediaRecorder file carries no duration the server can read.
            'audio_duration' => [
                'nullable',
                'integer',
                'min:1',
                'max:' . CheckPointMessage::MAX_AUDIO_SECONDS,
                'required_with:audio',
            ],
        ], [
            'audio.max' => 'Voice note terlalu besar (maks. ' . intdiv(self::MAX_VOICE_KB, 1024) . ' MB).',
            'audio_duration.max' => 'Voice note maksimal ' . CheckPointMessage::MAX_AUDIO_SECONDS . ' detik.',
            'audio_duration.required_with' => 'Durasi voice note tidak terbaca. Coba rekam ulang.',
        ]);

        $uploads = $this->uploads($request);
        $audio = $request->file('audio');
        $voiceExtension = $audio instanceof UploadedFile ? $this->voiceExtension($audio) : null;

        // Laravel's `mimetypes` rule cannot see an audio-only WebM through
        // finfo, so the container is checked here against the same map that
        // decides the stored extension.
        if ($audio instanceof UploadedFile && $voiceExtension === null) {
            throw ValidationException::withMessages([
                'audio' => 'Voice note harus berupa rekaman suara (webm, ogg, mp4, mp3).',
            ]);
        }

        $body = trim((string) ($data['body'] ?? ''));

        // A message with no words, no proof and no recording is not a reply.
        if ($body === '' && $uploads === [] && $audio === null) {
            throw ValidationException::withMessages([
                'body' => 'Tulis balasan, lampirkan minimal satu foto, atau rekam voice note.',
            ]);
        }

        $message = DB::transaction(function () use ($item, $user, $isManagement, $body, $uploads, $audio, $voiceExtension, $data): CheckPointMessage {
            $message = $item->messages()->create([
                'user_id' => $user->id,
                'sender_role' => $isManagement
                    ? CheckPointMessage::ROLE_MANAGEMENT
                    : CheckPointMessage::ROLE_EMPLOYEE,
                'body' => $body === '' ? null : $body,
            ]);

            if ($audio !== null && $voiceExtension !== null) {
                $message->forceFill([
                    'audio_path' => $this->storeVoice($audio, $voiceExtension),
                    'audio_duration' => (int) $data['audio_duration'],
                ])->save();
            }

            foreach ($uploads as $urutan => $file) {
                $message->images()->create([
                    'path' => $this->uploader->store($file),
                    'urutan' => $urutan,
                ]);
            }

            // Answering a rejected item resubmits it, so it returns to the
            // reviewer's queue. An accepted item stays accepted — a reply
            // there is clarification, not a new submission.
            if (! $isManagement && $item->approve_status === 'denied') {
                $item->approve_status = 'proccess';
                $item->save();
            }

            return $message;
        });

        $this->notifyCounterpart($message, $item, $isManagement);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $message->id,
                'sender_role' => $message->sender_role,
                'body' => $message->body,
                'approve_status' => $item->approve_status,
                'images' => $message->images->pluck('path')->all(),
                'audio_path' => $message->audio_path,
                'audio_duration' => $message->audio_duration,
            ], 201);
        }

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    /**
     * Pull a message back ("tarik pesan"). Only the person who wrote it may do
     * so — not the other side, and not management on someone else's behalf —
     * and they must still have access to the thread.
     *
     * The row survives as a tombstone: the thread keeps its shape and its order,
     * but the body, the photos and the voice note are gone for everyone. The
     * item's approve_status is left alone, so retracting a reply cannot be used
     * to pull a resubmitted row out of the reviewer's queue.
     */
    public function destroy(Request $request, CheckPointMessage $message): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $item = $message->item;
        $checkPoint = $item?->checkPoint;
        $isManagement = $this->isManagement($user);
        $isOwner = $checkPoint !== null && (int) $checkPoint->user_id === (int) $user->id;

        abort_unless($isManagement || $isOwner, 403);
        abort_unless((int) $message->user_id === (int) $user->id, 403);

        if (! $message->isRetracted()) {
            DB::transaction(function () use ($message): void {
                $message->images()->get()->each(function ($image): void {
                    Storage::disk('public')->delete('images/' . $image->path);
                    $image->delete();
                });

                if (filled($message->audio_path)) {
                    Storage::disk('public')->delete(
                        CheckPointMessage::VOICE_DIRECTORY . '/' . $message->audio_path,
                    );
                }

                $message->forceFill([
                    'body' => null,
                    'audio_path' => null,
                    'audio_duration' => null,
                    'retracted_at' => now(),
                ])->save();
            });
        }

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $message->id,
                'body' => null,
                'audio_path' => null,
                'retracted_at' => $message->retracted_at?->toIso8601String(),
                'approve_status' => $item?->approve_status,
            ]);
        }

        return back()->with('success', 'Pesan berhasil ditarik.');
    }

    /**
     * Mirror of the Direksi checkpoint check: a user is management when their
     * `jabatan` (or the jabatan of their division) carries a management code.
     */
    private function isManagement(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $code = $user->jabatan?->code_jabatan ?? $user->divisi?->jabatan?->code_jabatan;

        return in_array($code, self::MANAGEMENT_CODES, true);
    }

    /**
     * Recipients of an employee reply: everyone who reviews checkpoints.
     * The sender is skipped so nobody notifies themselves.
     *
     * @return Collection<int, User>
     */
    private function managementRecipients(?int $excludeUserId)
    {
        return User::query()
            ->when($excludeUserId, fn (Builder $query) => $query->whereKeyNot($excludeUserId))
            ->where(function (Builder $query): void {
                $query
                    ->whereHas('jabatan', fn (Builder $jabatan) => $jabatan->whereIn('code_jabatan', self::MANAGEMENT_CODES))
                    ->orWhereHas('divisi.jabatan', fn (Builder $jabatan) => $jabatan->whereIn('code_jabatan', self::MANAGEMENT_CODES));
            })
            ->get();
    }

    private function notifyCounterpart(CheckPointMessage $message, CheckPointItem $item, bool $fromManagement): void
    {
        $label = $item->pekerjaanCp?->name ?? $item->input_manual ?? 'Pekerjaan';

        if ($fromManagement) {
            $item->checkPoint?->user?->notify(new CheckPointReplyNotification(
                title: 'Catatan baru pada bukti pekerjaan',
                message: 'Atasan menanggapi "' . $label . '".',
                checkPointId: (int) $item->check_point_id,
                itemId: (int) $item->id,
            ));

            return;
        }

        Notification::send($this->managementRecipients($message->user_id), new CheckPointReplyNotification(
            title: 'Balasan baru dari karyawan',
            message: 'Karyawan membalas catatan pada "' . $label . '".',
            checkPointId: (int) $item->check_point_id,
            itemId: (int) $item->id,
        ));
    }

    /**
     * Attachments arrive either as a list (`images[]`) or, for a single-file
     * input, as one bare UploadedFile.
     *
     * @return array<int, UploadedFile>
     */
    private function uploads(Request $request): array
    {
        $files = $request->file('images', []);

        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        return array_values(array_filter(
            (array) $files,
            fn ($file) => $file instanceof UploadedFile,
        ));
    }

    /**
     * The extension a recording should be stored under, or null when the file
     * is not one of the containers a browser recorder produces. Both the
     * sniffed and the declared type are considered, because finfo reports an
     * audio-only WebM as `video/webm` and some Ogg streams as `application/ogg`.
     */
    private function voiceExtension(UploadedFile $file): ?string
    {
        foreach ([$file->getMimeType(), $file->getClientMimeType()] as $mime) {
            $extension = self::VOICE_TYPES[strtolower((string) $mime)] ?? null;

            if ($extension !== null) {
                return $extension;
            }
        }

        return null;
    }

    /**
     * Voice notes are stored as-is: they need no downscaling, and the browser
     * already picked the codec. The name is generated, so the extension comes
     * from the validated container and never from the uploaded file name.
     */
    private function storeVoice(UploadedFile $file, string $extension): string
    {
        $name = 'voice' . md5(uniqid((string) mt_rand(), true)) . '.' . $extension;

        $file->storeAs(CheckPointMessage::VOICE_DIRECTORY, $name, 'public');

        return $name;
    }
}
