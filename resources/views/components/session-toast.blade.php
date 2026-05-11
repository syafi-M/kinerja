@if (session('toast'))
    <div id="session-toast"
        class="fixed right-4 top-4 z-[99999] w-[calc(100%-2rem)] max-w-sm rounded-xl border-l-4 bg-white px-4 py-3 text-sm shadow-xl ring-1 ring-slate-900/5"
        data-type="{{ session('toast.type') }}">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-white" data-toast-icon>
                <i class="ri-check-line"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-slate-900" data-toast-title>Berhasil</p>
                <p class="mt-0.5 text-xs font-medium leading-5 text-slate-600">{{ session('toast.message') }}</p>
            </div>
            <button type="button" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                data-toast-close aria-label="Tutup notifikasi">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>

    <script>
        (function() {
            const sessionToast = document.getElementById('session-toast');
            if (!sessionToast) return;

            const type = sessionToast.dataset.type || 'info';
            const iconWrap = sessionToast.querySelector('[data-toast-icon]');
            const title = sessionToast.querySelector('[data-toast-title]');
            const closeBtn = sessionToast.querySelector('[data-toast-close]');

            const themes = {
                success: { border: 'border-emerald-500', icon: 'bg-emerald-500', title: 'Berhasil', iconClass: 'ri-check-line' },
                error: { border: 'border-rose-500', icon: 'bg-rose-500', title: 'Gagal', iconClass: 'ri-close-line' },
                warning: { border: 'border-amber-500', icon: 'bg-amber-500', title: 'Peringatan', iconClass: 'ri-alert-line' },
                info: { border: 'border-sky-500', icon: 'bg-sky-500', title: 'Info', iconClass: 'ri-information-line' },
            };

            const theme = themes[type] ?? themes.info;
            sessionToast.classList.add(theme.border, 'transition-all', 'duration-200');

            if (iconWrap) {
                iconWrap.classList.add(theme.icon);
                iconWrap.innerHTML = `<i class="${theme.iconClass}"></i>`;
            }

            if (title) {
                title.textContent = theme.title;
            }

            const closeToast = () => {
                sessionToast.classList.add('opacity-0', 'translate-y-1', 'pointer-events-none');
                setTimeout(() => sessionToast.remove(), 220);
            };

            closeBtn?.addEventListener('click', closeToast);
            setTimeout(closeToast, 3500);
        })();
    </script>
@endif

