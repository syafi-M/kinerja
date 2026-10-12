@php
    /**
     * Two-way conversation for one checkpoint item: the reviewer's note, the
     * exchange so far, and a composer that posts without reloading the page.
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

    // Awaiting management = the employee had the last word (or the item came
    // back rejected), so the reviewer still has to act on this item.
    $awaitingManagement = $messages->isNotEmpty()
        ? ! $messages->last()->isFromManagement()
        : $status === 'denied';

    // Old rows carry their verdict only in `note`; show it as the opening
    // management message so the thread never looks like it starts nowhere.
    $bubbles = $messages->map(fn ($message) => [
        'role' => $message->sender_role,
        'name' => $message->user?->nama_lengkap ?? 'Pengguna dihapus',
        'at' => $message->created_at,
        'body' => $message->body,
        'images' => $message->images->pluck('path')->all(),
    ])->all();

    if ($messages->isEmpty() && filled($note)) {
        $bubbles[] = [
            'role' => \App\Models\CheckPointMessage::ROLE_MANAGEMENT,
            'name' => 'Manajemen',
            'at' => $item->updated_at,
            'body' => $note,
            'images' => [],
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
@endphp

<div class="item-thread mt-4 border-t border-slate-100 pt-4" data-thread
    data-endpoint="{{ route('checkpoint-item.messages.store', $item->id) }}"
    data-viewer-role="{{ $viewerRole }}" data-image-base="{{ $imageBase }}"
    data-csrf="{{ csrf_token() }}">

    @if (filled($note))
        <div class="mb-3 flex items-start gap-2 rounded-xl border px-3 py-2 text-xs {{ $bannerClass }}">
            <i class="ri-error-warning-line mt-0.5 text-base"></i>
            <p class="leading-relaxed"><strong>Catatan Evaluasi:</strong> {{ $note }}</p>
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-2">
        <p class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.14em] text-slate-500">
            <i class="ri-chat-3-line text-sm"></i> Riwayat Percakapan &amp; Klarifikasi
        </p>
        <div class="flex items-center gap-1.5">
            <span data-thread-pending
                class="{{ $awaitingManagement ? '' : 'hidden' }} rounded-md bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-700">Belum
                ditangani</span>
            <span data-thread-chip
                class="rounded-md px-2 py-0.5 text-[10px] font-bold uppercase {{ $chip['class'] }}">{{ $chip['label'] }}</span>
            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500"
                data-thread-count>{{ $messages->count() }} Pesan</span>
        </div>
    </div>

    <div class="mt-3 space-y-3" data-thread-list>
        @forelse ($bubbles as $bubble)
            @php
                $fromManagement = $bubble['role'] === \App\Models\CheckPointMessage::ROLE_MANAGEMENT;
                $mine = $fromManagement
                    ? $viewerRole === 'management'
                    : $viewerRole === 'employee';
                $initial = mb_strtoupper(mb_substr($bubble['name'] ?: '?', 0, 1));
            @endphp
            <div class="flex items-end gap-2 {{ $mine ? 'justify-end' : 'justify-start' }}">
                @unless ($mine)
                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white {{ $fromManagement ? 'bg-slate-700' : 'bg-sky-600' }}">
                        {{ $initial }}</div>
                @endunless

                <div class="max-w-[82%] sm:max-w-[75%]">
                    <p class="mb-1 text-[11px] text-slate-400 {{ $mine ? 'text-right' : '' }}">
                        <span class="font-semibold text-slate-500">{{ $fromManagement ? 'Atasan' : 'Karyawan' }}</span>
                        &middot; {{ $bubble['name'] }}
                        &middot; {{ optional($bubble['at'])->locale('id')->translatedFormat('d M Y, H.i') }}
                    </p>
                    <div
                        class="rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm {{ $fromManagement ? 'bg-slate-100 text-slate-700' : 'bg-sky-600 text-white' }}">
                        @if (filled($bubble['body']))
                            <p class="whitespace-pre-line">{{ $bubble['body'] }}</p>
                        @endif

                        @if (count($bubble['images']))
                            <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
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
                    </div>
                </div>

                @if ($mine)
                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white {{ $fromManagement ? 'bg-slate-700' : 'bg-sky-600' }}">
                        {{ $initial }}</div>
                @endif
            </div>
        @empty
            <p class="rounded-xl border border-dashed border-slate-200 px-3 py-4 text-center text-xs text-slate-400"
                data-thread-empty>Belum ada percakapan. Balas catatan atasan untuk memulai diskusi.</p>
        @endforelse
    </div>

    {{-- Composer. Deliberately a <div>, not a <form>: on the edit page it sits
         inside #form-cp, and a nested <form> is dropped by the HTML parser, so
         its submit button would submit the checkpoint PUT instead. Its fields
         also carry no `name` attributes so they never ride along with that PUT;
         the reply is sent with fetch. --}}
    <div data-thread-form class="mt-4 rounded-xl border border-slate-200 bg-slate-50/70 p-3">
        <label class="text-[11px] font-bold uppercase tracking-[.14em] text-slate-500">Tulis Balasan / Klarifikasi</label>

        <textarea data-thread-body rows="3"
            class="mt-2 w-full resize-y rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100"
            placeholder="Ketik balasan atau penjelasan respon untuk catatan atasan..."></textarea>

        <p data-thread-error class="mt-1 hidden text-xs font-semibold text-rose-600"></p>

        <div data-thread-previews class="mt-2 hidden grid-cols-2 gap-2 sm:grid-cols-3"></div>

        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <button type="button" data-thread-attach
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-sky-300 hover:text-sky-700">
                    <i class="ri-attachment-2"></i> Lampirkan bukti baru
                </button>
                <span class="text-[11px] text-slate-400">Maks. {{ $maxImages }} foto</span>
            </div>

            <button type="button" data-thread-submit
                class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60">
                <span data-thread-submit-label>Kirim respon</span>
                <i class="ri-send-plane-fill"></i>
            </button>
        </div>

        <input type="file" data-thread-files accept="image/*" multiple class="hidden">
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
         viewer's own message, so it always sits on their side of the thread
         and carries their role's colours — the same side and colours the loop
         above renders for them. --}}
    @php
        $myBubble = $viewerRole === 'management' ? 'bg-slate-100 text-slate-700' : 'bg-sky-600 text-white';
        $myAvatar = $viewerRole === 'management' ? 'bg-slate-700' : 'bg-sky-600';
        $myRole = $viewerRole === 'management' ? 'Atasan' : 'Karyawan';
        $myInitial = $viewerRole === 'management' ? 'A' : 'K';
    @endphp
    <template data-thread-template>
        <div class="flex items-end gap-2 justify-end">
            <div class="max-w-[82%] sm:max-w-[75%]">
                <p class="mb-1 text-right text-[11px] text-slate-400">
                    <span class="font-semibold text-slate-500">{{ $myRole }}</span>
                    &middot; <span data-slot="time"></span>
                </p>
                <div class="rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm {{ $myBubble }}">
                    <p class="whitespace-pre-line" data-slot="body"></p>
                    <div class="mt-2 hidden grid-cols-2 gap-2 sm:grid-cols-3" data-slot="files"></div>
                </div>
            </div>
            <div
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white {{ $myAvatar }}">
                {{ $myInitial }}</div>
        </div>
    </template>
</div>

@once
    <script>
        (() => {
            const MAX_IMAGES = {{ $maxImages }};
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

                if (!list.length) {
                    box.classList.add('hidden');
                    return;
                }

                box.classList.remove('hidden');

                list.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'relative overflow-hidden rounded-lg border border-slate-200 bg-white';

                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = file.name;
                    img.className = 'h-20 w-full object-cover';

                    const label = document.createElement('p');
                    label.className = 'truncate px-2 py-1 text-[11px] text-slate-500';
                    label.textContent = file.name;

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

                    item.append(img, label, remove);
                    box.append(item);
                });
            };

            const setBusy = (root, busy) => {
                const button = root.querySelector('[data-thread-submit]');
                const label = root.querySelector('[data-thread-submit-label]');
                button.disabled = busy;
                label.textContent = busy ? 'Mengirim...' : 'Kirim respon';
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

                const body = node.querySelector('[data-slot="body"]');
                if (data.body) {
                    body.textContent = data.body;
                } else {
                    body.remove();
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

                if (target.children.length) {
                    target.classList.remove('hidden');
                } else {
                    target.remove();
                }

                node.querySelector('[data-slot="time"]').textContent = new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                }).format(new Date());

                const empty = root.querySelector('[data-thread-empty]');
                if (empty) empty.remove();

                root.querySelector('[data-thread-list]').append(node);

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
                const input = event.target.closest('[data-thread-files]');
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
            });

            document.addEventListener('click', async (event) => {
                const submit = event.target.closest('[data-thread-submit]');
                if (!submit) return;
                event.preventDefault();

                const form = submit.closest('[data-thread-form]');
                const root = form.closest('[data-thread]');
                const bodyEl = form.querySelector('[data-thread-body]');
                const list = stateOf(root);
                const body = bodyEl.value.trim();

                clearError(root);

                if (!body && !list.length) {
                    error(root, 'Tulis balasan atau lampirkan minimal satu foto.');
                    return;
                }

                if (list.length > MAX_IMAGES) {
                    error(root, 'Maksimal ' + MAX_IMAGES + ' foto per balasan.');
                    return;
                }

                const payload = new FormData();
                if (body) payload.append('body', body);
                list.forEach((file) => payload.append('images[]', file));

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
        })();
    </script>
@endonce
