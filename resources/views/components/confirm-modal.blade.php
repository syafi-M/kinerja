@props(['id' => 'confirmModal', 'title' => 'Konfirmasi', 'message' => 'Apakah Anda yakin?', 'confirmText' => 'Ya, Lanjutkan', 'cancelText' => 'Batal', 'type' => 'danger', 'onConfirm' => null])

<div x-data="{ show: false, onConfirm: null }" x-show="show" x-cloak
    @open-modal-{{ $id }}.window="show = true"
    @close-modal-{{ $id }}.window="show = false"
    @set-confirm-{{ $id }}.window="onConfirm = $event.detail"
    @keydown.escape.window="show = false"
    class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto px-4 py-6"
    style="display: none;">
    
    <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="show = false"></div>

    <div x-show="show" x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-slate-900/5 transition-all">
        
        <div class="p-5">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full"
                    :class="{
                        'bg-rose-100': '{{ $type }}' === 'danger',
                        'bg-amber-100': '{{ $type }}' === 'warning',
                        'bg-sky-100': '{{ $type }}' === 'info',
                        'bg-emerald-100': '{{ $type }}' === 'success'
                    }">
                    <i class="text-xl"
                        :class="{
                            'ri-error-warning-line text-rose-600': '{{ $type }}' === 'danger',
                            'ri-alert-line text-amber-600': '{{ $type }}' === 'warning',
                            'ri-information-line text-sky-600': '{{ $type }}' === 'info',
                            'ri-checkbox-circle-line text-emerald-600': '{{ $type }}' === 'success'
                        }"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $message }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4">
            <button type="button" @click="show = false"
                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                {{ $cancelText }}
            </button>
            <button type="button" @click="if (onConfirm) onConfirm(); show = false"
                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                :class="{
                    'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500': '{{ $type }}' === 'danger',
                    'bg-amber-500 hover:bg-amber-600 focus:ring-amber-400': '{{ $type }}' === 'warning',
                    'bg-sky-600 hover:bg-sky-700 focus:ring-sky-500': '{{ $type }}' === 'info',
                    'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500': '{{ $type }}' === 'success'
                }">
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>
