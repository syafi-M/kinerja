@php
    /**
     * Two-way conversation for one checkpoint item: the reviewer's note, the
     * exchange so far, and a composer that posts without reloading the page.
     *
     * A sender may pull their own message back ("tarik pesan"): the row survives
     * as a tombstone so the thread keeps its shape, while its text, photos and
     * voice note are wiped for everyone. A reply may carry photos, one voice
     * note, or both, and the "Tarik" link only ever renders on a message the
     * viewer wrote.
     *
     * Expects:
     *   $item       App\Models\CheckPointItem (messages.images + messages.user loaded)
     *   $viewerRole 'employee' | 'management' — decides which side "my message" sits on
     */
    $viewerRole = $viewerRole ?? 'employee';
    $messages = $item->messages;
    $status = $item->approve_status;
    $note = $item->note;
    $maxImages = \App\Models\CheckPointMessage::MAX_IMAGES;
    $imageBase = asset('storage/images');
    $voiceBase = asset('storage/' . \App\Models\CheckPointMessage::VOICE_DIRECTORY);
    $maxAudioSeconds = \App\Models\CheckPointMessage::MAX_AUDIO_SECONDS;

    // Durations are always shown as m:ss, whether it is the cap or one take.
    $clock = fn (int $seconds): string => intdiv($seconds, 60) . ':' . str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT);

    // A compact emoji set for the composer: a picker library would add a build
    // dependency for a handful of everyday reactions.
    $emojis = ['😀', '😄', '😁', '😅', '😊', '😉', '😐', '🙁', '😮', '😴', '🙏', '👍', '👌', '🙌', '👏', '💪', '🤝', '🙇', '✅', '❌', '⚠️', '❗', '❓', '📌', '📝', '📅', '🕒', '📎', '📷', '🎤', '💬', '🔧', '🔨', '🧰', '🧹', '🚧', '🧽', '🚿', '🔌', '🚪'];

    // Awaiting management = the employee had the last word (or the item came
    // back rejected), so the reviewer still has to act on this item.
    $awaitingManagement = $messages->isNotEmpty()
        ? ! $messages->last()->isFromManagement()
        : $status === 'denied';

    // Old rows carry their verdict only in `note`; show it as the opening
    // management message so the thread never looks like it starts nowhere.
    $bubbles = $messages->map(fn ($message) => [
        'id' => $message->id,
        // Authorship, not alignment: only the sender gets to retract.
        'mine' => (int) $message->user_id === (int) auth()->id(),
        'retracted' => $message->isRetracted(),
        'role' => $message->sender_role,
        'name' => $message->user?->nama_lengkap ?? 'Pengguna dihapus',
        'at' => $message->created_at,
        // A retracted message keeps its slot but none of its content.
        'body' => $message->isRetracted() ? null : $message->body,
        'images' => $message->isRetracted() ? [] : $message->images->pluck('path')->all(),
        'audio' => $message->isRetracted() ? null : $message->audio_path,
        'audio_duration' => $message->isRetracted() ? null : $message->audio_duration,
    ])->all();

    if ($messages->isEmpty() && filled($note)) {
        $bubbles[] = [
            'id' => null,
            'mine' => false,
            'retracted' => false,
            'role' => \App\Models\CheckPointMessage::ROLE_MANAGEMENT,
            'name' => 'Manajemen',
            'at' => $item->updated_at,
            'body' => $note,
            'images' => [],
            'audio' => null,
            'audio_duration' => null,
        ];
    }

    $chip = match ($status) {
        'accept' => ['label' => 'DITINJAU', 'class' => 'bg-emerald-100 text-emerald-700'],
        'denied' => ['label' => 'PERLU DIPERBAIKI', 'class' => 'bg-rose-100 text-rose-700'],
        default => ['label' => 'MENUNGGU TINJAUAN', 'class' => 'bg-amber-100 text-amber-700'],
    };

    $bannerClass = match ($status) {
        'denied' => 'border-rose-200 bg-rose-50 text-rose-700',
        'accept' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        default => 'border-slate-200 bg-slate-50 text-slate-600',
    };

    // Chat-panel plumbing: who the viewer is talking to, whose turn it is, which
    // day a message belongs to, and whether the other side has answered it —
    // the things a messaging app shows without being asked.
    $counterpartRole = $viewerRole === 'management'
        ? \App\Models\CheckPointMessage::ROLE_EMPLOYEE
        : \App\Models\CheckPointMessage::ROLE_MANAGEMENT;
    $counterpartLabel = $counterpartRole === \App\Models\CheckPointMessage::ROLE_MANAGEMENT ? 'Atasan' : 'Karyawan';
    $counterpartName = collect($bubbles)->reverse()->firstWhere('role', $counterpartRole)['name'] ?? $counterpartLabel;
    $counterpartInitial = mb_strtoupper(mb_substr($counterpartName ?: '?', 0, 1));

    $awaitingViewer = $awaitingManagement
        ? $viewerRole === 'management'
        : $viewerRole === 'employee';

    $emptyConversation = $messages->isEmpty() && blank($note);

    $statusLine = $emptyConversation
        ? 'Belum ada percakapan'
        : ($awaitingViewer ? 'Anda perlu menanggapi' : 'Menunggu tanggapan ' . $counterpartLabel);

    // "Hari Ini" / "Kemarin" / a full date, like the day dividers in a chat.
    $dayLabel = function ($date): ?string {
        if (! $date) {
            return null;
        }

        if ($date->isToday()) {
            return 'Hari Ini';
        }

        if ($date->isYesterday()) {
            return 'Kemarin';
        }

        return $date->locale('id')->translatedFormat('d F Y');
    };

    // A message is answered once the other side has said anything after it: the
    // closest honest stand-in for the read ticks a chat app shows.
    $answered = [];
    foreach ($bubbles as $i => $bubble) {
        $answered[$i] = collect(array_slice($bubbles, $i + 1))
            ->contains(fn ($later) => $later['role'] !== $bubble['role']);
    }
@endphp

{{-- Laid out like a chat panel: a header naming the other side, a scrollable
     history, and a composer pinned under it. The thread sits inside a bigger
     checkpoint form, so the history gets its own scroll area instead of pushing
     the form down the page. --}}
<div class="item-thread mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-thread
    data-endpoint="{{ route('checkpoint-item.messages.store', $item->id) }}"
    data-retract-endpoint="{{ route('checkpoint-item.messages.destroy', ['message' => '__ID__']) }}"
    data-viewer-role="{{ $viewerRole }}" data-image-base="{{ $imageBase }}" data-voice-base="{{ $voiceBase }}"
    data-csrf="{{ csrf_token() }}">

    @if (filled($note))
        <div class="flex items-start gap-2 border-b px-3 py-2 text-xs {{ $bannerClass }}">
            <i class="ri-error-warning-line mt-0.5 text-base"></i>
            <p class="leading-relaxed"><strong>Catatan Evaluasi:</strong> {{ $note }}</p>
        </div>
    @endif

    {{-- Header: who the viewer is talking to, and whether the ball is in their court. --}}
    <div class="flex items-center gap-3 bg-sky-700 px-3 py-2.5 text-white">
        <span
            class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white/20 text-sm font-bold">{{ $counterpartInitial }}</span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold leading-tight">{{ $counterpartName }}</p>
            <p class="truncate text-[11px] leading-tight {{ $awaitingViewer ? 'text-amber-300' : 'text-white/70' }}">
                {{ $statusLine }}
            </p>
        </div>
        <div class="flex shrink-0 items-center gap-1.5">
            <span data-thread-pending
                class="{{ $awaitingManagement ? '' : 'hidden' }} rounded-md bg-amber-300 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-900">Belum
                ditangani</span>
            <span data-thread-chip
                class="rounded-md px-2 py-0.5 text-[10px] font-bold uppercase {{ $chip['class'] }}">{{ $chip['label'] }}</span>
            <span class="rounded-md bg-white/15 px-2 py-0.5 text-[10px] font-semibold text-white/80"
                data-thread-count>{{ $messages->count() }} Pesan</span>
        </div>
    </div>

    {{-- A quiet strip naming the panel, the way a chat sidebar labels its
         conversation list. --}}
    <p class="border-b border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-400">
        Riwayat Percakapan</p>

    {{-- The message area scrolls on its own, painted with the faint dotted
         backdrop a chat app uses, and the jump button floats over its corner
         whenever the reader has scrolled away from the newest message. --}}
    <div class="relative">
        <div class="max-h-[60vh] min-h-[12rem] overflow-y-auto bg-slate-100 px-3 py-3 sm:max-h-[30rem] sm:px-4"
            style="background-image: radial-gradient(circle at 1px 1px, rgba(100,116,139,.18) 1px, transparent 0); background-size: 18px 18px;"
            data-thread-list>
        @forelse ($bubbles as $index => $bubble)
            @php
                $fromManagement = $bubble['role'] === \App\Models\CheckPointMessage::ROLE_MANAGEMENT;
                $mine = $fromManagement
                    ? $viewerRole === 'management'
                    : $viewerRole === 'employee';
                $initial = mb_strtoupper(mb_substr($bubble['name'] ?: '?', 0, 1));
                // Only the author may retract, and only while there is
                // something left to retract.
                $canRetract = $bubble['mine'] && ! $bubble['retracted'] && $bubble['id'] !== null;
                $at = $bubble['at'];
                $previous = $index > 0 ? $bubbles[$index - 1] : null;
                // A run breaks where the sender or the day changes — that is
                // where the avatar and the day divider belong.
                $newDay = $at && (! $previous || $previous['at']?->toDateString() !== $at->toDateString());
                $startsRun = $newDay || ! $previous || $previous['role'] !== $bubble['role'];
                $metaClass = $bubble['retracted'] || $fromManagement ? 'text-slate-400' : 'text-white/70';
                $bubbleClass = $bubble['retracted']
                    ? 'border border-dashed border-slate-300 bg-white italic text-slate-400'
                    : ($fromManagement ? 'bg-white text-slate-700 ring-1 ring-slate-200' : 'bg-sky-600 text-white');
            @endphp

            @if ($newDay)
                <div class="my-2 flex justify-center">
                    <span
                        class="rounded-lg bg-white/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-500 shadow-sm ring-1 ring-slate-200">
                        {{ $dayLabel($at) }}
                    </span>
                </div>
            @endif

            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }} {{ $startsRun ? 'mt-3' : 'mt-0.5' }}"
                @if ($bubble['id']) data-bubble="{{ $bubble['id'] }}" @endif>
                @unless ($mine)
                    @if ($startsRun)
                        <div
                            class="mr-2 grid h-8 w-8 shrink-0 place-items-center self-end rounded-full text-xs font-bold text-white {{ $fromManagement ? 'bg-slate-700' : 'bg-sky-600' }}">
                            {{ $initial }}</div>
                    @else
                        {{-- Holds the alignment of a run without repeating the avatar. --}}
                        <span class="mr-2 w-8 shrink-0"></span>
                    @endif
                @endunless

                <div class="max-w-[85%] sm:max-w-[75%]">
                    <div data-bubble-body
                        class="relative rounded-2xl px-3 py-2 text-sm leading-relaxed shadow-sm {{ $mine ? 'rounded-br-sm' : 'rounded-bl-sm' }} {{ $bubbleClass }}">
                        {{-- The tail that turns a flat rectangle into a chat bubble. --}}
                        <span aria-hidden="true"
                            class="absolute bottom-0 h-0 w-0 border-y-[6px] border-y-transparent {{ $mine ? '-right-2 border-l-[8px] border-l-sky-600' : '-left-2 border-r-[8px] border-r-white' }}"></span>

                        @if ($bubble['retracted'])
                            {{-- The tombstone: the slot is still here, the words are not. --}}
                            <p class="flex items-center gap-1.5"><i class="ri-eraser-line overflow-hidden"></i> Pesan ini ditarik</p>
                        @else
                            @unless ($mine)
                                <p
                                    class="mb-0.5 text-[11px] font-bold {{ $fromManagement ? 'text-slate-500' : 'text-sky-300' }}">
                                    {{ $fromManagement ? 'Atasan' : 'Karyawan' }} &middot; {{ $bubble['name'] }}
                                </p>
                            @endunless

                            @if (filled($bubble['body']))
                                <p class="whitespace-pre-line">{{ $bubble['body'] }}</p>
                            @endif

                            @if (filled($bubble['audio']))
                                @include('check.partials.thread-audio', [
                                    'src' => $voiceBase . '/' . $bubble['audio'],
                                    'duration' => (int) $bubble['audio_duration'],
                                    'tone' => $fromManagement ? 'incoming' : 'outgoing',
                                ])
                            @endif

                            @if (count($bubble['images']))
                                <div class="mt-1.5 grid grid-cols-2 gap-1.5 sm:grid-cols-3">
                                    @foreach ($bubble['images'] as $path)
                                        {{-- data-thread-photo makes the script below open a
                                             full-size lightbox; href stays as the no-JS
                                             fallback and as the modal's image source. --}}
                                        <a href="{{ $imageBase . '/' . $path }}" target="_blank" rel="noopener"
                                            data-thread-photo aria-label="Perbesar foto lampiran"
                                            class="group block cursor-zoom-in overflow-hidden rounded-lg ring-1 {{ $fromManagement ? 'ring-slate-200' : 'ring-white/40' }}">
                                            <img src="{{ $imageBase . '/' . $path }}" alt="Lampiran percakapan"
                                                class="h-20 w-full object-cover transition group-hover:scale-105">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        {{-- Time inside the bubble, bottom-right, the way a chat app
                             shows it. A brighter double check means the other side
                             has already answered. --}}
                        <p class="mt-0.5 flex items-center justify-end gap-1 text-[10px] leading-none overflow-hidden {{ $metaClass }}">
                            <span data-slot="time" class="overflow-hidden">{{ optional($at)->locale('id')->translatedFormat('H.i') }}</span>
                            @if ($bubble['mine'] && ! $bubble['retracted'])
                                @php $isAnswered = $answered[$index] ?? false; @endphp
                                <i class="{{ $isAnswered ? 'ri-check-double-line text-white' : 'ri-check-line' }}"
                                    title="{{ $isAnswered ? 'Sudah ditanggapi' : 'Terkirim' }}"></i>
                            @endif
                        </p>
                    </div>

                    @if ($canRetract)
                    <div class="mt-1 flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <button
                            type="button"
                            data-thread-retract="{{ $bubble['id'] }}"
                            title="Tarik pesan ini"
                            aria-label="Tarik pesan ini"
                            class="inline-flex min-h-9 items-center justify-center gap-1.5
                                rounded-full px-3 py-1.5
                                text-xs font-medium leading-none text-slate-500
                                transition-colors duration-200
                                hover:bg-rose-50 hover:text-rose-600
                                active:bg-rose-100 active:text-rose-700
                                focus-visible:outline-none focus-visible:ring-2
                                focus-visible:ring-rose-400 focus-visible:ring-offset-1
                                disabled:pointer-events-none disabled:opacity-40
                                sm:min-h-7 sm:px-2 sm:py-1 sm:text-[11px] overflow-hidden">

                            <i class="ri-delete-back-2-line inline-flex shrink-0 items-center justify-center
                                    text-base leading-none overflow-hidden"
                            aria-hidden="true"></i>

                            <span class="inline-flex items-center leading-none overflow-hidden">
                                Tarik pesan
                            </span>
                        </button>
                    </div>
                @endif
                </div>

                @if ($mine)
                    @if ($startsRun)
                        <div
                            class="ml-2 grid h-8 w-8 shrink-0 place-items-center self-end rounded-full bg-sky-600 text-xs font-bold text-white">
                            {{ $initial }}</div>
                    @else
                        <span class="ml-2 w-8 shrink-0"></span>
                    @endif
                @endif
            </div>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 bg-white/70 px-3 py-4 text-center text-xs text-slate-400"
                data-thread-empty>Belum ada percakapan. Balas catatan atasan untuk memulai diskusi.</p>
            @endforelse
        </div>

        {{-- One tap back to the newest message: the down arrow WhatsApp parks in
             the corner once you scroll up out of the last screen. --}}
        <button type="button" data-thread-jump aria-label="Ke pesan terbaru"
            class="absolute bottom-3 right-3 z-10 hidden h-10 w-10 place-items-center rounded-full bg-white text-lg text-slate-500 shadow-lg ring-1 ring-slate-200 transition hover:text-sky-600 hover:shadow-xl">
            <i class="ri-arrow-down-line"></i>
        </button>
    </div>

    {{-- Composer. Deliberately a <div>, not a <form>: on the edit page it sits
         inside #form-cp, and a nested <form> is dropped by the HTML parser, so
         its submit button would submit the checkpoint PUT instead. Its fields
         also carry no `name` attributes so they never ride along with that PUT;
         the reply is sent with fetch. --}}
    <div data-thread-form data-thread-composer class="border-t border-slate-200 bg-slate-100 px-2 py-2 sm:px-3">
        <p class="mb-1 px-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Tulis Balasan / Klarifikasi
        </p>

        <p data-thread-error class="mb-1 hidden px-1 text-xs font-semibold text-rose-600"></p>

        {{-- Attachments waiting to go out: photo thumbnails, then the take that
             was just recorded. --}}
        <div data-thread-previews class="mb-1 hidden grid-cols-4 gap-1.5 sm:grid-cols-6"></div>

        {{-- The take that is recorded but not sent yet. --}}
        <div data-thread-voice
            class="mb-1 hidden items-center gap-2 rounded-2xl bg-white px-2 py-1.5 ring-1 ring-slate-200">
            @include('check.partials.thread-audio', ['src' => '', 'duration' => 0, 'tone' => 'pending'])
            <button type="button" data-thread-voice-remove aria-label="Hapus voice note"
                class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-rose-600 transition hover:bg-rose-50">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>

        <div class="flex items-end gap-2">
            {{-- The typed reply. While the mic is live this pill is swapped for the
                 recording strip, the way a chat app does it. --}}
            <div data-thread-pill
                class="flex min-w-0 flex-1 items-end gap-0.5 rounded-3xl bg-white px-1.5 py-1 ring-1 ring-slate-200">
                <button type="button" data-thread-emoji aria-label="Pilih emoji" aria-expanded="false"
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-xl text-slate-400 transition hover:bg-slate-100">
                    <i class="ri-emotion-happy-line"></i>
                </button>

                <textarea data-thread-body rows="1"
                    class="max-h-24 min-h-[2.25rem] flex-1 resize-none border-0 bg-transparent py-2 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:ring-0"
                    placeholder="Ketik pesan"></textarea>

                <button type="button" data-thread-attach aria-label="Lampirkan foto"
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-lg text-slate-400 transition hover:bg-slate-100">
                    <i class="ri-attachment-2"></i>
                </button>
                <button type="button" data-thread-camera aria-label="Ambil foto"
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-lg text-slate-400 transition hover:bg-slate-100">
                    <i class="ri-camera-line"></i>
                </button>
            </div>

            <div data-thread-recorder
                class="hidden min-w-0 flex-1 items-center gap-2 rounded-3xl bg-white px-2 py-2 ring-1 ring-rose-200">
                <button type="button" data-thread-record-cancel aria-label="Batalkan rekaman"
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-lg text-rose-600 transition hover:bg-rose-50">
                    <i class="ri-delete-bin-line"></i>
                </button>
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-rose-600"></span>
                </span>
                <span data-thread-timer class="font-mono text-xs font-bold text-rose-700">0:00</span>
                <span class="text-[11px] text-rose-400">/ {{ $clock($maxAudioSeconds) }}</span>
                <span class="ml-auto truncate pr-1 text-[11px] text-rose-500">Merekam suara…</span>
            </div>

            {{-- One round button that changes job: mic while there is nothing to
                 send, stop while recording, paper plane once there is. --}}
            <button type="button" data-thread-mic aria-label="Rekam voice note"
                class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-sky-600 text-xl text-white shadow-sm transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60">
                <i class="ri-mic-line"></i>
            </button>
            <button type="button" data-thread-record-stop aria-label="Selesai merekam"
                class="hidden h-11 w-11 shrink-0 place-items-center rounded-full bg-rose-600 text-xl text-white shadow-sm transition hover:bg-rose-700">
                <i class="ri-stop-circle-line"></i>
            </button>
            <button type="button" data-thread-submit aria-label="Kirim pesan"
                class="hidden h-11 w-11 shrink-0 place-items-center rounded-full bg-sky-600 text-xl text-white shadow-sm transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60">
                <i class="ri-send-plane-fill"></i>
            </button>
        </div>

        <div data-thread-emoji-panel
            class="mt-2 hidden max-h-40 grid-cols-8 gap-1 overflow-y-auto rounded-2xl bg-white p-2 ring-1 ring-slate-200 sm:grid-cols-10">
            @foreach ($emojis as $emoji)
                <button type="button" data-emoji="{{ $emoji }}"
                    class="grid h-9 place-items-center rounded-lg text-xl transition hover:bg-slate-100">{{ $emoji }}</button>
            @endforeach
        </div>

        <p class="mt-1 px-1 text-[10px] text-slate-400">Maks. {{ $maxImages }} foto · voice
            {{ $clock($maxAudioSeconds) }}</p>

        <input type="file" data-thread-files accept="image/*" multiple class="hidden">
        <input type="file" data-thread-camera-input accept="image/*" capture="environment" class="hidden">
    </div>

    <p data-thread-hint
        class="mt-2 text-xs {{ $status === 'denied' ? 'text-rose-600' : ($status === 'accept' ? 'text-emerald-700' : 'hidden text-slate-400') }}">
        @if ($status === 'denied')
            <i class="ri-refresh-line"></i> Balasan ini mengirim ulang pekerjaan untuk ditinjau.
        @elseif ($status === 'accept')
            <i class="ri-check-double-line"></i> Pekerjaan sudah disetujui — balasan ini hanya klarifikasi.
        @endif
    </p>

    {{-- Markup for a message the current viewer has just posted, cloned by the
         script below so the design lives in one place. It is always the
         viewer's own message, so it always sits on their side of the thread and
         carries the same colours the loop above renders for them. --}}
    @php
        // The viewer's own initial, so a bubble posted without a reload wears the
        // same avatar the server-rendered ones do.
        $myInitial = mb_strtoupper(mb_substr(auth()->user()->nama_lengkap ?? '?', 0, 1));
    @endphp
    <template data-thread-template>
        <div class="mt-3 flex justify-end" data-bubble>
            <div class="max-w-[85%] sm:max-w-[75%]">
                <div data-bubble-body
                    class="relative rounded-2xl rounded-br-sm bg-sky-600 px-3 py-2 text-sm leading-relaxed text-white shadow-sm">
                    <span aria-hidden="true"
                        class="absolute bottom-0 -right-2 h-0 w-0 border-y-[6px] border-y-transparent border-l-[8px] border-l-sky-600"></span>
                    <p class="whitespace-pre-line" data-slot="body"></p>
                    <div class="mt-1.5 hidden" data-slot="audio"></div>
                    <div class="mt-1.5 hidden grid-cols-2 gap-1.5 sm:grid-cols-3" data-slot="files"></div>
                    <p class="mt-0.5 flex items-center justify-end gap-1 text-[10px] leading-none text-white/70">
                        <span data-slot="time" class="overflow-hidden"></span>
                        <i class="ri-check-line" title="Terkirim"></i>
                    </p>
                </div>
                {{-- This template lives outside the @forelse loop, so `$mine`
                     and `$bubble` do not exist here — the cloned bubble is
                     always the viewer's own message, and the script below
                     stamps the real message id onto the retract button. --}}
                <div class="mt-1 flex justify-end">
                    <button
                        type="button"
                        data-thread-retract=""
                        title="Tarik pesan ini"
                        aria-label="Tarik pesan ini"
                        class="inline-flex min-h-9 items-center justify-center gap-1.5
                            rounded-full px-3 py-1.5
                            text-xs font-medium leading-none text-slate-500
                            transition-colors duration-200
                            hover:bg-rose-50 hover:text-rose-600
                            active:bg-rose-100 active:text-rose-700
                            focus-visible:outline-none focus-visible:ring-2
                            focus-visible:ring-rose-400 focus-visible:ring-offset-1
                            disabled:pointer-events-none disabled:opacity-40
                            sm:min-h-7 sm:px-2 sm:py-1 sm:text-[11px] overflow-hidden">

                        <i class="ri-delete-back-2-line inline-flex shrink-0 items-center justify-center
                                text-base leading-none overflow-hidden"
                        aria-hidden="true"></i>

                        <span class="inline-flex items-center leading-none overflow-hidden">
                            Tarik pesan
                        </span>
                    </button>
                </div>
            </div>
            <div
                class="ml-2 grid h-8 w-8 shrink-0 place-items-center self-end rounded-full bg-sky-600 text-xs font-bold text-white">
                {{ $myInitial }}</div>
        </div>
    </template>
</div>

@once
    <script>
        (() => {
            const MAX_IMAGES = {{ $maxImages }};
            const MAX_VOICE_SECONDS = {{ $maxAudioSeconds }};
            // Containers a browser recorder can produce, most preferred first:
            // Chromium records WebM/Opus, Safari records MP4/AAC.
            const VOICE_TYPES = [
                { mime: 'audio/webm;codecs=opus', ext: 'webm' },
                { mime: 'audio/webm', ext: 'webm' },
                { mime: 'audio/ogg;codecs=opus', ext: 'ogg' },
                { mime: 'audio/mp4', ext: 'm4a' },
                { mime: 'audio/ogg', ext: 'ogg' },
            ];
            const CHIPS = {
                accept: {
                    label: 'DITINJAU',
                    className: 'bg-emerald-100 text-emerald-700'
                },
                denied: {
                    label: 'PERLU DIPERBAIKI',
                    className: 'bg-rose-100 text-rose-700'
                },
                proccess: {
                    label: 'MENUNGGU TINJAUAN',
                    className: 'bg-amber-100 text-amber-700'
                },
            };
            const ALL_CHIP_CLASSES = ['bg-emerald-100', 'text-emerald-700', 'bg-rose-100',
                'text-rose-700', 'bg-amber-100', 'text-amber-700'
            ];

            const files = new WeakMap();

            const stateOf = (root) => {
                if (!files.has(root)) files.set(root, []);
                return files.get(root);
            };

            // root -> the finished-but-unsent voice note, and root -> the take
            // being recorded right now. `live` mirrors the second one so the
            // page can shut every microphone when it goes away.
            const voices = new WeakMap();
            const recordings = new WeakMap();
            const live = new Set();

            const voiceOf = (root) => voices.get(root) || null;

            const clock = (seconds) => {
                const total = Math.max(0, Math.round(seconds));
                return Math.floor(total / 60) + ':' + String(total % 60).padStart(2, '0');
            };

            const renderVoice = (root) => {
                const box = root.querySelector('[data-thread-voice]');
                const controller = box ? box.querySelector('[data-audio-controller]') : null;
                const player = controller ? controller.querySelector('[data-audio-player]') : null;
                const voice = voiceOf(root);

                if (!box || !controller || !player) return;

                box.classList.toggle('hidden', !voice);
                box.classList.toggle('flex', Boolean(voice));

                if (voice) {
                    // The take's own length is the one number the browser cannot
                    // recover from a MediaRecorder blob, so it travels with it.
                    controller.dataset.duration = String(voice.duration);
                    player.src = voice.url;
                } else {
                    player.pause();
                    player.removeAttribute('src');
                }

                const seek = controller.querySelector('[data-audio-seek]');
                const label = controller.querySelector('[data-audio-time]');
                const icon = controller.querySelector('[data-audio-icon]');

                if (seek) seek.value = '0';
                if (label) {
                    label.textContent = clock(voice ? voice.duration : Number(controller.dataset.duration || 0));
                }
                if (icon) icon.className = 'ri-play-fill text-lg';

                updateComposer(root);
            };

            const setVoice = (root, voice) => {
                const previous = voiceOf(root);
                if (previous && previous.url) URL.revokeObjectURL(previous.url);

                if (voice) {
                    voices.set(root, voice);
                } else {
                    voices.delete(root);
                }

                renderVoice(root);
            };

            const showRecorder = (root, visible) => {
                const bar = root.querySelector('[data-thread-recorder]');
                bar.classList.toggle('hidden', !visible);
                bar.classList.toggle('flex', visible);

                // One take at a time: the pill stands aside, the round button
                // becomes the stop button, and the mic comes back afterwards.
                updateComposer(root);
            };

            // The round button has three jobs, and the pill gives way while the
            // mic is live: mic → stop → send, the way a chat composer works.
            // Buttons carry their display utility here too, because `hidden`
            // alone loses to `grid`/`flex` whenever both are on the element.
            const updateComposer = (root) => {
                const recording = recordings.has(root);
                const ready = Boolean(
                    (root.querySelector('[data-thread-body]')?.value || '').trim() ||
                    stateOf(root).length ||
                    voiceOf(root)
                );

                const show = (element, visible, display) => {
                    if (!element) return;
                    element.classList.toggle('hidden', !visible);
                    element.classList.toggle(display, visible);
                };

                show(root.querySelector('[data-thread-pill]'), !recording, 'flex');
                show(root.querySelector('[data-thread-recorder]'), recording, 'flex');
                show(root.querySelector('[data-thread-mic]'), !recording && !ready, 'grid');
                show(root.querySelector('[data-thread-record-stop]'), recording, 'grid');
                show(root.querySelector('[data-thread-submit]'), !recording && ready, 'grid');

                const mic = root.querySelector('[data-thread-mic]');
                if (mic) mic.disabled = recording;
            };

            const pickVoiceType = () => VOICE_TYPES.find((type) => {
                try {
                    return MediaRecorder.isTypeSupported(type.mime);
                } catch (ignored) {
                    return false;
                }
            }) || { mime: '', ext: 'webm' };

            const startRecording = (root, stream) => {
                const type = pickVoiceType();
                let recorder;

                try {
                    recorder = type.mime ?
                        new MediaRecorder(stream, { mimeType: type.mime }) :
                        new MediaRecorder(stream);
                } catch (ignored) {
                    stream.getTracks().forEach((track) => track.stop());
                    error(root, 'Perangkat ini tidak bisa merekam suara.');
                    return;
                }

                const take = {
                    recorder,
                    stream,
                    chunks: [],
                    startedAt: Date.now(),
                    duration: 0,
                    ext: type.ext,
                    mime: recorder.mimeType || type.mime || 'audio/webm',
                    timer: null,
                    cancelled: false,
                    done: false,
                    finish: null,
                };

                recorder.addEventListener('dataavailable', (event) => {
                    if (event.data && event.data.size) take.chunks.push(event.data);
                });

                take.finish = () => {
                    // `stop` also fires after an error or a cancel, so this may
                    // only run once per take.
                    if (take.done) return;
                    take.done = true;

                    window.clearInterval(take.timer);
                    take.stream.getTracks().forEach((track) => track.stop());
                    recordings.delete(root);
                    live.delete(take);
                    showRecorder(root, false);

                    if (take.cancelled) return;

                    const elapsed = take.duration || Math.round((Date.now() - take.startedAt) / 1000);
                    const blob = new Blob(take.chunks, { type: take.mime });

                    if (!blob.size) {
                        error(root, 'Rekaman kosong. Coba rekam ulang.');
                        return;
                    }

                    clearError(root);
                    setVoice(root, {
                        blob,
                        url: URL.createObjectURL(blob),
                        duration: Math.min(Math.max(elapsed, 1), MAX_VOICE_SECONDS),
                        ext: take.ext,
                    });
                };

                recorder.addEventListener('stop', () => take.finish());
                recorder.addEventListener('error', () => {
                    take.cancelled = true;
                    error(root, 'Perekaman gagal. Coba lagi.');
                    take.finish();
                });

                take.timer = window.setInterval(() => {
                    take.duration = Math.round((Date.now() - take.startedAt) / 1000);
                    root.querySelector('[data-thread-timer]').textContent = clock(take.duration);

                    // The server caps a voice note at this length too, so stop
                    // on the dot rather than let a take be rejected for a
                    // second of overrun.
                    if (take.duration >= MAX_VOICE_SECONDS) stopRecording(root);
                }, 200);

                recordings.set(root, take);
                live.add(take);

                root.querySelector('[data-thread-timer]').textContent = '0:00';
                clearError(root);
                showRecorder(root, true);
                recorder.start();
            };

            const stopRecording = (root) => {
                const take = recordings.get(root);
                if (!take || take.recorder.state === 'inactive') return;

                take.duration = take.duration || Math.round((Date.now() - take.startedAt) / 1000);
                root.querySelector('[data-thread-timer]').textContent = clock(take.duration);
                take.recorder.stop();
            };

            const cancelRecording = (root) => {
                const take = recordings.get(root);
                if (!take) return;

                take.cancelled = true;

                if (take.recorder.state === 'inactive') {
                    take.finish();
                } else {
                    take.recorder.stop();
                }
            };

            // A custom player, bound one element at a time: media events do not
            // bubble, so each controller needs its own listeners. The <audio>
            // element stays the engine — hidden — because the duration trick
            // above has to run on it.
            const enhancePlayers = (scope) => {
                if (!scope || typeof scope.querySelectorAll !== 'function') return;

                scope.querySelectorAll('[data-audio-controller]').forEach((controller) => {
                    if (controller.dataset.bound === '1') return;

                    const player = controller.querySelector('[data-audio-player]');
                    const icon = controller.querySelector('[data-audio-icon]');
                    const seek = controller.querySelector('[data-audio-seek]');
                    const time = controller.querySelector('[data-audio-time]');

                    if (!player || !icon || !seek || !time) return;

                    controller.dataset.bound = '1';

                    // A WebM from MediaRecorder reports no usable duration until
                    // the thread script has seeked it once, so the markup's own
                    // number is the fallback.
                    const totalSeconds = () => (Number.isFinite(player.duration) && player.duration > 0 ?
                        player.duration :
                        Number(controller.dataset.duration || 0));

                    const paint = () => {
                        const total = totalSeconds();

                        seek.value = total ? String(Math.min(100, (player.currentTime / total) * 100)) : '0';
                        time.textContent = player.currentTime > 0.05 ? clock(player.currentTime) : clock(total);
                    };

                    player.addEventListener('timeupdate', paint);
                    player.addEventListener('durationchange', paint);
                    player.addEventListener('loadedmetadata', paint);
                    player.addEventListener('play', () => {
                        icon.className = 'ri-pause-fill text-lg';
                    });
                    player.addEventListener('pause', () => {
                        icon.className = 'ri-play-fill text-lg';
                        paint();
                    });
                    player.addEventListener('ended', () => {
                        icon.className = 'ri-play-fill text-lg';
                        player.currentTime = 0;
                        paint();
                    });

                    controller.querySelector('[data-audio-toggle]')?.addEventListener('click', () => {
                        // One voice note at a time, like a chat app.
                        document.querySelectorAll('[data-audio-player]').forEach((other) => {
                            if (other !== player) other.pause();
                        });

                        if (!player.paused) {
                            player.pause();
                            return;
                        }

                        if (player.getAttribute('src')) player.play().catch(() => {});
                    });

                    seek.addEventListener('input', () => {
                        const total = totalSeconds();
                        if (!total) return;
                        player.currentTime = (Number(seek.value) / 100) * total;
                        paint();
                    });

                    paint();
                });
            };

            // The same player the server renders, built for a message the viewer
            // has just posted — always their own, so always the outgoing look.
            const buildVoiceBubble = (root, data) => {
                const controller = document.createElement('div');
                controller.className = 'flex w-[230px] max-w-full items-center gap-2';
                controller.setAttribute('data-audio-controller', '');
                controller.dataset.duration = String(data.audio_duration || 0);

                const button = document.createElement('button');
                button.type = 'button';
                button.setAttribute('data-audio-toggle', '');
                button.setAttribute('aria-label', 'Putar voice note');
                button.className =
                    'grid h-8 w-8 shrink-0 place-items-center rounded-full bg-sky-600 text-white transition hover:bg-sky-700';

                const buttonIcon = document.createElement('i');
                buttonIcon.className = 'ri-play-fill text-lg';
                buttonIcon.setAttribute('data-audio-icon', '');
                button.append(buttonIcon);

                const seek = document.createElement('input');
                seek.type = 'range';
                seek.min = '0';
                seek.max = '100';
                seek.step = '1';
                seek.value = '0';
                seek.setAttribute('data-audio-seek', '');
                seek.setAttribute('aria-label', 'Geser posisi pemutaran');
                seek.className = 'h-2 w-full flex-1 cursor-pointer accent-white';

                const time = document.createElement('span');
                time.className = 'w-9 shrink-0 text-right text-[10px] font-semibold tabular-nums text-white/80';
                time.setAttribute('data-audio-time', '');
                time.textContent = clock(data.audio_duration || 0);

                const player = document.createElement('audio');
                player.preload = 'metadata';
                player.className = 'hidden';
                player.setAttribute('data-audio-player', '');
                player.src = root.dataset.voiceBase + '/' + data.audio_path;

                controller.append(button, seek, time, player);

                return controller;
            };

            const error = (root, message) => {
                const el = root.querySelector('[data-thread-error]');
                if (!el) return;
                el.textContent = message;
                el.classList.remove('hidden');
            };

            const clearError = (root) => {
                const el = root.querySelector('[data-thread-error]');
                if (!el) return;
                el.textContent = '';
                el.classList.add('hidden');
            };

            const renderPreviews = (root) => {
                const box = root.querySelector('[data-thread-previews]');
                const list = stateOf(root);
                box.innerHTML = '';

                // `grid` travels with `hidden`: the column classes on the box
                // only bite while it is a grid, and dropping `hidden` alone would
                // leave a block and a full-width attachment.
                box.classList.toggle('hidden', !list.length);
                box.classList.toggle('grid', list.length > 0);

                if (!list.length) {
                    return;
                }

                list.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'relative overflow-hidden rounded-lg border border-slate-200 bg-white';

                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = file.name;
                    img.title = file.name;
                    img.className = 'h-16 w-full object-cover';

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className =
                        'absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-xs text-white shadow';
                    remove.setAttribute('aria-label', 'Hapus lampiran');
                    remove.innerHTML = '<i class="ri-close-line"></i>';
                    remove.addEventListener('click', () => {
                        stateOf(root).splice(index, 1);
                        renderPreviews(root);
                    });

                    item.append(img, remove);
                    box.append(item);
                });
            };

            const setBusy = (root, busy) => {
                const button = root.querySelector('[data-thread-submit]');
                const icon = button ? button.querySelector('i') : null;

                if (button) button.disabled = busy;
                // The only label left on that button is its icon, so the spinner
                // is what says "sending".
                if (icon) icon.className = busy ? 'ri-loader-4-line animate-spin' : 'ri-send-plane-fill';

                const mic = root.querySelector('[data-thread-mic]');
                if (mic) mic.disabled = busy || recordings.has(root);
            };

            const applyStatus = (root, status) => {
                const chip = root.querySelector('[data-thread-chip]');
                const preset = CHIPS[status] || CHIPS.proccess;
                chip.classList.remove(...ALL_CHIP_CLASSES);
                preset.className.split(' ').forEach((c) => chip.classList.add(c));
                chip.textContent = preset.label;

                const hint = root.querySelector('[data-thread-hint]');
                const viewerRole = root.dataset.viewerRole;

                if (viewerRole === 'employee' && status === 'proccess') {
                    hint.classList.remove('hidden', 'text-rose-600', 'text-slate-400');
                    hint.classList.add('text-sky-700');
                    hint.innerHTML =
                        '<i class="ri-refresh-line"></i> Balasan terkirim — pekerjaan dikirim ulang untuk ditinjau.';
                } else if (viewerRole === 'employee' && status !== 'accept') {
                    hint.classList.remove('hidden', 'text-rose-600', 'text-slate-400');
                    hint.classList.add('text-sky-700');
                    hint.innerHTML = '<i class="ri-chat-3-line"></i> Balasan terkirim.';
                } else if (status === 'accept') {
                    hint.classList.remove('hidden', 'text-rose-600', 'text-slate-400');
                    hint.classList.add('text-emerald-700');
                    hint.innerHTML =
                        '<i class="ri-check-double-line"></i> Pekerjaan sudah disetujui — balasan ini hanya klarifikasi.';
                } else {
                    hint.classList.add('hidden');
                }
            };

            const appendMessage = (root, data) => {
                const template = root.querySelector('[data-thread-template]');
                const node = template.content.firstElementChild.cloneNode(true);

                // A fresh bubble belongs to whoever just posted it, so it gets
                // the same retract hook the server-rendered ones carry.
                node.setAttribute('data-bubble', data.id);
                const retract = node.querySelector('[data-thread-retract]');
                if (retract) retract.setAttribute('data-thread-retract', data.id);

                const body = node.querySelector('[data-slot="body"]');
                if (data.body) {
                    body.textContent = data.body;
                } else {
                    body.remove();
                }

                const audioSlot = node.querySelector('[data-slot="audio"]');
                if (data.audio_path) {
                    audioSlot.append(buildVoiceBubble(root, data));
                    audioSlot.classList.remove('hidden');
                } else {
                    audioSlot.remove();
                }

                const target = node.querySelector('[data-slot="files"]');
                (data.images || []).forEach((path) => {
                    const link = document.createElement('a');
                    link.href = root.dataset.imageBase + '/' + path;
                    link.target = '_blank';
                    link.rel = 'noopener';
                    // Same as the server-rendered thumbnails: a lightbox hook,
                    // plus the ring that reads light on a sky bubble and slate
                    // on a management one.
                    link.setAttribute('data-thread-photo', '');
                    link.setAttribute('aria-label', 'Perbesar foto lampiran');
                    link.className = 'group block cursor-zoom-in overflow-hidden rounded-lg ring-1 ' +
                        (root.dataset.viewerRole === 'management' ? 'ring-slate-200' : 'ring-white/40');

                    const img = document.createElement('img');
                    img.src = link.href;
                    img.alt = 'Lampiran balasan';
                    img.className = 'h-20 w-full object-cover transition group-hover:scale-105';

                    link.append(img);
                    target.append(link);
                });

                // Photos only tile while the slot is a grid — `hidden` and `grid`
                // are toggled together, never `hidden` on its own.
                if (target.children.length) {
                    target.classList.remove('hidden');
                    target.classList.add('grid');
                } else {
                    target.remove();
                }

                // Only the clock inside the bubble, the way a chat app stamps it.
                node.querySelector('[data-slot="time"]').textContent = new Intl.DateTimeFormat('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                }).format(new Date());

                const empty = root.querySelector('[data-thread-empty]');
                if (empty) empty.remove();

                root.querySelector('[data-thread-list]').append(node);

                // Bind the new player, follow the conversation down, and put the
                // round button back to being a mic — the reply is gone.
                enhancePlayers(node);
                scrollToBottom(root);
                updateComposer(root);

                const count = root.querySelector('[data-thread-count]');
                count.textContent = (parseInt(count.textContent, 10) + 1) + ' Pesan';

                // Whoever just posted, the ball is now in the other court.
                const pending = root.querySelector('[data-thread-pending]');
                pending.classList.toggle('hidden', root.dataset.viewerRole !== 'employee');
            };

            const serverError = (payload) => {
                if (payload && payload.errors) {
                    const first = Object.values(payload.errors)[0];
                    if (Array.isArray(first) && first.length) return first[0];
                }
                return payload && payload.message ? payload.message : null;
            };

            // --------------------------------------------------------------
            // Lightbox. The thumbnails inside a bubble are deliberately small,
            // so clicking one opens the untouched photo in a shared modal
            // instead of a browser tab. One overlay per page, built on first
            // use and reused by every thread on the page.
            // --------------------------------------------------------------
            let lightbox = null;
            let lightboxPhotos = [];
            let lightboxIndex = 0;
            let lightboxTrigger = null;

            const setPhotoStatus = (message) => {
                if (!lightbox) return;
                lightbox.querySelector('[data-lightbox-status]').textContent = message || '';
            };

            const buildLightbox = () => {
                const overlay = document.createElement('div');
                overlay.className =
                    'fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/85 p-4 backdrop-blur-sm';
                overlay.setAttribute('data-photo-lightbox', '');
                overlay.setAttribute('role', 'dialog');
                overlay.setAttribute('aria-modal', 'true');
                overlay.setAttribute('aria-label', 'Pratinjau foto');
                // Only static markup, so no user data ever reaches innerHTML.
                overlay.innerHTML = `
                    <button type="button" data-lightbox-close
                        class="absolute right-3 top-3 grid h-10 w-10 place-items-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/25 focus:outline-none focus:ring-2 focus:ring-white/60"
                        aria-label="Tutup pratinjau"><i class="ri-close-line"></i></button>
                    <button type="button" data-lightbox-prev
                        class="absolute left-2 top-1/2 hidden h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/25 focus:outline-none focus:ring-2 focus:ring-white/60 sm:left-4"
                        aria-label="Foto sebelumnya"><i class="ri-arrow-left-s-line"></i></button>
                    <button type="button" data-lightbox-next
                        class="absolute right-2 top-1/2 hidden h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/25 focus:outline-none focus:ring-2 focus:ring-white/60 sm:right-4"
                        aria-label="Foto berikutnya"><i class="ri-arrow-right-s-line"></i></button>
                    <figure class="flex max-h-full max-w-full flex-col items-center justify-center gap-3">
                        <img data-lightbox-image
                            class="max-h-[78vh] max-w-[92vw] rounded-xl bg-white/5 object-contain shadow-2xl"
                            alt="">
                        <figcaption
                            class="flex flex-wrap items-center justify-center gap-3 text-xs font-semibold text-white/80">
                            <span data-lightbox-counter
                                class="hidden rounded-md bg-white/10 px-2 py-1"></span>
                            <span data-lightbox-status class="text-amber-200"></span>
                            <a data-lightbox-open target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-2.5 py-1.5 transition hover:bg-white/25">
                                <i class="ri-external-link-line"></i> Buka ukuran penuh
                            </a>
                        </figcaption>
                    </figure>`;

                const image = overlay.querySelector('[data-lightbox-image]');
                image.addEventListener('load', () => setPhotoStatus(''));
                image.addEventListener('error', () => setPhotoStatus('Foto gagal dimuat.'));

                overlay.addEventListener('click', (event) => {
                    // The dark area around the photo closes; the photo itself
                    // (and its caption) does not, so a stray click is harmless.
                    if (event.target === overlay) return closeLightbox();
                    if (event.target.closest('[data-lightbox-close]')) return closeLightbox();
                    if (event.target.closest('[data-lightbox-prev]')) return stepLightbox(-1);
                    if (event.target.closest('[data-lightbox-next]')) return stepLightbox(1);
                });

                document.body.append(overlay);

                return overlay;
            };

            const showPhoto = (index) => {
                const photo = lightboxPhotos[index];
                if (!photo) return;

                lightboxIndex = index;

                const image = lightbox.querySelector('[data-lightbox-image]');
                const open = lightbox.querySelector('[data-lightbox-open]');
                const counter = lightbox.querySelector('[data-lightbox-counter]');
                const multiple = lightboxPhotos.length > 1;

                setPhotoStatus('Memuat foto...');
                image.alt = photo.alt;
                image.src = photo.src;
                open.href = photo.src;

                // Re-showing the photo already on screen fires no `load`, so
                // clear the placeholder ourselves when it is already decoded.
                if (image.complete && image.naturalWidth > 0) {
                    setPhotoStatus('');
                }

                counter.textContent = multiple ? (index + 1) + ' / ' + lightboxPhotos.length : '';
                counter.classList.toggle('hidden', !multiple);
                lightbox.querySelector('[data-lightbox-prev]').classList.toggle('hidden', !multiple);
                lightbox.querySelector('[data-lightbox-next]').classList.toggle('hidden', !multiple);
            };

            const stepLightbox = (delta) => {
                const next = lightboxIndex + delta;
                if (next < 0 || next >= lightboxPhotos.length) return;
                showPhoto(next);
            };

            const openLightbox = (photos, index, trigger) => {
                if (!photos.length) return;

                if (!lightbox) lightbox = buildLightbox();

                lightboxPhotos = photos;
                lightboxTrigger = trigger || null;

                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
                // This layout scrolls the document, so lock it while the photo
                // is up — otherwise the page slides behind the overlay.
                document.body.style.overflow = 'hidden';

                showPhoto(index);

                const close = lightbox.querySelector('[data-lightbox-close]');
                if (close) close.focus();
            };

            function closeLightbox() {
                if (!lightbox || lightbox.classList.contains('hidden')) return;

                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');
                document.body.style.overflow = '';
                // Drop the full-size bitmap; a long checkpoint page can hold
                // dozens of threads and none of them needs to keep it decoded.
                lightbox.querySelector('[data-lightbox-image]').removeAttribute('src');
                lightboxPhotos = [];
                setPhotoStatus('');

                const trigger = lightboxTrigger;
                lightboxTrigger = null;
                // preventScroll: the thumbnail was already on screen when it was
                // clicked, and focusing it would otherwise scroll the page back
                // to it after the reader has moved on.
                if (trigger && document.contains(trigger)) trigger.focus({ preventScroll: true });
            }

            document.addEventListener('click', (event) => {
                const attach = event.target.closest('[data-thread-attach]');
                if (!attach) return;
                attach.closest('[data-thread]').querySelector('[data-thread-files]').click();
            });

            // A WebM written by MediaRecorder carries no duration in its header,
            // so Chrome reports `Infinity` and draws the timeline as 0:00 — the
            // player still plays, but it cannot be scrubbed. Seeking past the
            // end makes the browser scan the file and learn its real length,
            // after which the same seek is undone. This is also why every voice
            // note carries its duration in the markup.
            document.addEventListener('loadedmetadata', (event) => {
                const player = event.target;

                if (!(player instanceof HTMLAudioElement)) return;
                if (Number.isFinite(player.duration) && player.duration > 0) return;

                const reset = () => {
                    player.removeEventListener('timeupdate', reset);
                    player.currentTime = 0;
                };

                player.addEventListener('timeupdate', reset);
                player.currentTime = 1e101;
            }, true);

            document.addEventListener('click', async (event) => {
                const mic = event.target.closest('[data-thread-mic]');
                if (!mic) return;

                const root = mic.closest('[data-thread]');

                if (recordings.has(root)) return;

                if (voiceOf(root)) {
                    error(root, 'Hapus voice note yang sudah direkam dulu sebelum merekam lagi.');
                    return;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia ||
                    typeof MediaRecorder === 'undefined') {
                    error(root,
                        'Browser ini tidak mendukung perekaman suara. Gunakan Chrome atau Safari versi terbaru.');
                    return;
                }

                mic.disabled = true;

                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    startRecording(root, stream);
                } catch (ignored) {
                    // Denied permission, no microphone, or an insecure origin.
                    mic.disabled = false;
                    error(root, 'Mikrofon tidak bisa diakses. Periksa izin browser lalu coba lagi.');
                }
            });

            document.addEventListener('click', (event) => {
                const stop = event.target.closest('[data-thread-record-stop]');
                if (stop) {
                    stopRecording(stop.closest('[data-thread]'));
                    return;
                }

                const cancel = event.target.closest('[data-thread-record-cancel]');
                if (cancel) {
                    cancelRecording(cancel.closest('[data-thread]'));
                    return;
                }

                const remove = event.target.closest('[data-thread-voice-remove]');
                if (remove) {
                    const root = remove.closest('[data-thread]');
                    clearError(root);
                    setVoice(root, null);
                }
            });

            // The composer mirrors a chat bar, so the emoji button opens a small
            // grid and the camera button opens the camera straight away.
            document.addEventListener('click', (event) => {
                const camera = event.target.closest('[data-thread-camera]');
                if (camera) {
                    camera.closest('[data-thread]').querySelector('[data-thread-camera-input]').click();
                    return;
                }

                const toggle = event.target.closest('[data-thread-emoji]');
                if (toggle) {
                    const root = toggle.closest('[data-thread]');
                    const panel = root.querySelector('[data-thread-emoji-panel]');
                    const open = panel.classList.contains('hidden');

                    panel.classList.toggle('hidden', !open);
                    panel.classList.toggle('grid', open);
                    toggle.setAttribute('aria-expanded', String(open));
                    return;
                }

                const emoji = event.target.closest('[data-emoji]');
                if (emoji) {
                    const root = emoji.closest('[data-thread]');
                    const body = root.querySelector('[data-thread-body]');
                    const start = body.selectionStart ?? body.value.length;
                    const end = body.selectionEnd ?? start;
                    const picked = emoji.getAttribute('data-emoji');

                    // Insert where the caret is, then leave the caret after it.
                    body.value = body.value.slice(0, start) + picked + body.value.slice(end);
                    body.focus();
                    body.setSelectionRange(start + picked.length, start + picked.length);
                    updateComposer(root);
                    return;
                }

                // A click anywhere else closes an open emoji grid.
                document.querySelectorAll('[data-thread-emoji-panel]').forEach((panel) => {
                    if (panel.classList.contains('hidden') || panel.contains(event.target)) return;

                    panel.classList.add('hidden');
                    panel.classList.remove('grid');
                    panel.closest('[data-thread]')?.querySelector('[data-thread-emoji]')
                        ?.setAttribute('aria-expanded', 'false');
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    document.querySelectorAll('[data-thread-emoji-panel]').forEach((panel) => {
                        panel.classList.add('hidden');
                        panel.classList.remove('grid');
                    });
                    return;
                }

                const body = event.target.closest('[data-thread-body]');
                if (!body) return;

                // Enter sends, Shift+Enter starts a new line, like a chat app.
                if (event.key !== 'Enter' || event.shiftKey || event.isComposing) return;

                const root = body.closest('[data-thread]');
                const send = root.querySelector('[data-thread-submit]');

                if (send && !send.disabled && !send.classList.contains('hidden')) {
                    event.preventDefault();
                    send.click();
                }
            });

            // The box grows with the text instead of scrolling early, and the
            // round button re-decides whether it is a mic or a paper plane.
            document.addEventListener('input', (event) => {
                const body = event.target.closest('[data-thread-body]');
                if (!body) return;

                body.style.height = 'auto';
                body.style.height = Math.min(body.scrollHeight, 96) + 'px';

                updateComposer(body.closest('[data-thread]'));
            });

            // Is the newest message already on screen? A few pixels of slack
            // keeps the button from flickering on a fractional scroll offset.
            const atLatest = (list) => list.scrollHeight - list.scrollTop - list.clientHeight < 48;

            // The jump button only exists while there is history worth leaving
            // and the reader has actually left the newest message behind.
            const syncJump = (root) => {
                const list = root.querySelector('[data-thread-list]');
                const jump = root.querySelector('[data-thread-jump]');

                if (!list || !jump) return;

                const scrollable = list.scrollHeight - list.clientHeight > 8;
                const visible = scrollable && !atLatest(list);

                jump.classList.toggle('hidden', !visible);
                jump.classList.toggle('grid', visible);
            };

            const scrollToBottom = (root) => {
                const list = root.querySelector('[data-thread-list]');
                if (list) list.scrollTop = list.scrollHeight;
                syncJump(root);
            };

            // Every thread on a page stands alone, so each one is primed here:
            // players bound, composer state decided, history scrolled down.
            const initThreads = () => {
                document.querySelectorAll('[data-thread]').forEach((root) => {
                    enhancePlayers(root);
                    updateComposer(root);
                    scrollToBottom(root);

                    const list = root.querySelector('[data-thread-list]');
                    if (list) list.addEventListener('scroll', () => syncJump(root), {
                        passive: true
                    });

                    syncJump(root);
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initThreads);
            } else {
                initThreads();
            }

            // Rewrite a bubble into its tombstone in place: the slot, the
            // alignment and the time stay, the content does not.
            const markRetracted = (root, id) => {
                const wrapper = root.querySelector('[data-bubble="' + id + '"]');
                if (!wrapper) return;

                const body = wrapper.querySelector('[data-bubble-body]');

                if (body) {
                    const timeText = body.querySelector('[data-slot="time"]')?.textContent || '';

                    body.className =
                        'relative rounded-2xl rounded-br-sm border border-dashed border-slate-300 bg-white px-3 py-2 text-sm italic leading-relaxed text-slate-400 shadow-sm';
                    body.replaceChildren();

                    const note = document.createElement('p');
                    note.className = 'flex items-center gap-1.5';

                    const icon = document.createElement('i');
                    icon.className = 'ri-eraser-line';

                    note.append(icon, document.createTextNode('Pesan ini ditarik'));

                    const meta = document.createElement('p');
                    meta.className = 'mt-0.5 flex items-center justify-end gap-1 text-[10px] leading-none text-slate-400';

                    const time = document.createElement('span');
                    time.setAttribute('data-slot', 'time');
                    time.textContent = timeText;
                    meta.append(time);

                    body.append(note, meta);
                }

                wrapper.querySelector('[data-thread-retract]')?.remove();
            };

            // --------------------------------------------------------------
            // Retract confirmation.
            // window.confirm is a blocking browser dialog with no styling and
            // no control over layout; this shared modal matches the app's
            // look and resolves a promise so the caller stays a simple
            // async function. One overlay per page, built on first use.
            // --------------------------------------------------------------
            let confirmModal = null;

            const confirmRetract = () => new Promise((resolve) => {
                if (confirmModal) return resolve(false);

                const onKey = (event) => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        settle(false);
                    }
                };

                const settle = (answer) => {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                    document.body.style.overflow = '';
                    document.removeEventListener('keydown', onKey, true);
                    overlay.remove();
                    confirmModal = null;
                    resolve(answer);
                };

                const overlay = document.createElement('div');
                overlay.className =
                    'fixed inset-0 z-[80] hidden items-center justify-center bg-slate-900/70 p-4 backdrop-blur-sm';
                overlay.setAttribute('data-thread-confirm', '');
                overlay.setAttribute('role', 'dialog');
                overlay.setAttribute('aria-modal', 'true');
                overlay.setAttribute('aria-label', 'Konfirmasi tarik pesan');
                // Static markup only; the message is a fixed string, so no
                // user data ever reaches innerHTML.
                overlay.innerHTML = `
                    <div class="w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                        <div class="flex items-start gap-3 px-5 pt-5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-rose-100 text-xl text-rose-600">
                                <i class="ri-delete-bin-line"></i>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800">Tarik pesan ini?</h2>
                                <p class="mt-1 text-sm leading-relaxed text-slate-500">
                                    Isi pesan akan hilang untuk semua orang yang melihat percakapan ini.
                                </p>
                            </div>
                        </div>
                        <div class="mt-5 flex justify-end gap-2 border-t border-slate-100 bg-slate-50 px-5 py-3">
                            <button type="button" data-confirm-cancel
                                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Batal</button>
                            <button type="button" data-confirm-ok
                                class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700">Ya, tarik</button>
                        </div>
                    </div>`;

                overlay.addEventListener('click', (event) => {
                    if (event.target === overlay) return settle(false);
                    if (event.target.closest('[data-confirm-cancel]')) return settle(false);
                    if (event.target.closest('[data-confirm-ok]')) return settle(true);
                });

                document.addEventListener('keydown', onKey, true);
                document.body.append(overlay);
                confirmModal = overlay;
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                document.body.style.overflow = 'hidden';
                overlay.querySelector('[data-confirm-cancel]').focus();
            });

            document.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-thread-retract]');
                if (!button || button.disabled) return;

                event.preventDefault();

                const root = button.closest('[data-thread]');
                const id = button.getAttribute('data-thread-retract');

                if (!id) return;

                const confirmed = await confirmRetract();

                if (!confirmed) return;

                button.disabled = true;

                try {
                    const response = await fetch(root.dataset.retractEndpoint.replace('__ID__', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': root.dataset.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (response.ok) {
                        markRetracted(root, id);
                        clearError(root);
                        // A tombstone is shorter than the message it replaced, so
                        // the list may not overflow any more.
                        syncJump(root);
                        return;
                    }

                    button.disabled = false;

                    if (response.status === 403) {
                        error(root, 'Hanya pengirim pesan yang bisa menariknya.');
                    } else if (response.status === 419) {
                        error(root, 'Sesi Anda berakhir. Muat ulang halaman lalu coba lagi.');
                    } else {
                        error(root, 'Pesan gagal ditarik. Coba lagi.');
                    }
                } catch (ignored) {
                    button.disabled = false;
                    error(root, 'Koneksi bermasalah. Coba lagi.');
                }
            });

            // The down arrow parks over the corner of the history: tapping it
            // glides back to the newest message instead of jumping.
            document.addEventListener('click', (event) => {
                const jump = event.target.closest('[data-thread-jump]');
                if (!jump) return;

                event.preventDefault();

                const root = jump.closest('[data-thread]');
                const list = root ? root.querySelector('[data-thread-list]') : null;
                if (!list) return;

                list.scrollTo({
                    top: list.scrollHeight,
                    behavior: 'smooth'
                });
                syncJump(root);
            });

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-thread-photo]');
                if (!trigger) return;

                // A ctrl/cmd/shift/alt click still means "open in a new tab",
                // and the anchor is a working link for that.
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

                event.preventDefault();

                // Navigate within one conversation, so an item's photos do not
                // mix with the next item's on a page that lists several.
                const root = trigger.closest('[data-thread]') || document;
                const triggers = Array.from(root.querySelectorAll('[data-thread-photo]'));
                const photos = triggers.map((node) => ({
                    src: node.getAttribute('href'),
                    alt: node.querySelector('img')?.alt || 'Lampiran percakapan',
                }));

                openLightbox(photos, Math.max(triggers.indexOf(trigger), 0), trigger);
            });

            document.addEventListener('keydown', (event) => {
                if (!lightbox || lightbox.classList.contains('hidden')) return;

                // Never steal keys from a field the user is typing in.
                const active = document.activeElement;
                if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) return;

                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeLightbox();
                } else if (event.key === 'ArrowLeft') {
                    event.preventDefault();
                    stepLightbox(-1);
                } else if (event.key === 'ArrowRight') {
                    event.preventDefault();
                    stepLightbox(1);
                } else if (event.key === 'Tab') {
                    // Keep focus inside the overlay while it is open.
                    const focusable = Array.from(lightbox.querySelectorAll('button, a[href]'))
                        .filter((el) => !el.classList.contains('hidden'));
                    if (!focusable.length) return;

                    if (!lightbox.contains(active)) {
                        event.preventDefault();
                        focusable[0].focus();
                        return;
                    }

                    const first = focusable[0];
                    const last = focusable[focusable.length - 1];

                    if (event.shiftKey && active === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && active === last) {
                        event.preventDefault();
                        first.focus();
                    }
                }
            });

            document.addEventListener('change', (event) => {
                const input = event.target.closest('[data-thread-files], [data-thread-camera-input]');
                if (!input) return;

                const root = input.closest('[data-thread]');
                const list = stateOf(root);
                const incoming = Array.from(input.files || []);
                const overflow = list.length + incoming.length > MAX_IMAGES;

                incoming.forEach((file) => {
                    if (list.length >= MAX_IMAGES) return;
                    list.push(file);
                });

                if (overflow) {
                    error(root, 'Maksimal ' + MAX_IMAGES + ' foto per balasan.');
                } else {
                    clearError(root);
                }

                input.value = '';
                renderPreviews(root);
                updateComposer(root);
            });

            document.addEventListener('click', async (event) => {
                const submit = event.target.closest('[data-thread-submit]');
                if (!submit) return;
                event.preventDefault();

                const form = submit.closest('[data-thread-form]');
                const root = form.closest('[data-thread]');
                const bodyEl = form.querySelector('[data-thread-body]');
                const list = stateOf(root);
                const voice = voiceOf(root);
                const body = bodyEl.value.trim();

                clearError(root);

                if (recordings.has(root)) {
                    error(root, 'Selesaikan atau batalkan rekaman dulu sebelum mengirim.');
                    return;
                }

                if (!body && !list.length && !voice) {
                    error(root, 'Tulis balasan, lampirkan minimal satu foto, atau rekam voice note.');
                    return;
                }

                if (list.length > MAX_IMAGES) {
                    error(root, 'Maksimal ' + MAX_IMAGES + ' foto per balasan.');
                    return;
                }

                const payload = new FormData();
                if (body) payload.append('body', body);
                list.forEach((file) => payload.append('images[]', file));

                if (voice) {
                    payload.append('audio', voice.blob, 'voice-note.' + voice.ext);
                    payload.append('audio_duration', String(voice.duration));
                }

                setBusy(root, true);

                try {
                    const response = await fetch(root.dataset.endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': root.dataset.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                        body: payload,
                    });

                    let data = null;
                    try {
                        data = await response.json();
                    } catch (ignored) {
                        data = null;
                    }

                    if (response.status === 201 && data) {
                        appendMessage(root, data);
                        applyStatus(root, data.approve_status);
                        bodyEl.value = '';
                        stateOf(root).length = 0;
                        renderPreviews(root);
                        // The take now lives in the thread; drop the local blob
                        // and the object URL that played it back.
                        setVoice(root, null);
                    } else if (response.status === 422) {
                        error(root, serverError(data) || 'Balasan tidak valid.');
                    } else if (response.status === 403) {
                        error(root, 'Anda tidak berhak membalas percakapan ini.');
                    } else if (response.status === 419) {
                        error(root, 'Sesi Anda berakhir. Muat ulang halaman lalu coba lagi.');
                    } else {
                        error(root, 'Balasan gagal dikirim. Coba lagi.');
                    }
                } catch (ignored) {
                    error(root, 'Koneksi bermasalah. Coba lagi.');
                } finally {
                    setBusy(root, false);
                }
            });
            // A live take holds the microphone open, and the browser's
            // recording indicator with it. Nothing may outlive the page.
            window.addEventListener('pagehide', () => {
                live.forEach((take) => {
                    take.cancelled = true;

                    try {
                        take.recorder.stop();
                    } catch (ignored) {
                        // Already stopped; the tracks below still need closing.
                    }

                    take.stream.getTracks().forEach((track) => track.stop());
                });
            });
        })();
    </script>
@endonce
