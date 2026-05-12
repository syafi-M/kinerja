<script>
    (function() {
        const toastThemes = {
            success: {
                background: 'linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%)',
                border: '#059669',
                text: '#0f172a',
                muted: '#475569',
            },
            error: {
                background: 'linear-gradient(135deg, #ffe4e6 0%, #fff1f2 100%)',
                border: '#e11d48',
                text: '#0f172a',
                muted: '#475569',
            },
            warning: {
                background: 'linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%)',
                border: '#d97706',
                text: '#0f172a',
                muted: '#475569',
            },
            info: {
                background: 'linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%)',
                border: '#0284c7',
                text: '#0f172a',
                muted: '#475569',
            },
        };

        const getToastType = (toast) => {
            if (toast.classList.contains('fl-success') || toast.classList.contains('toast-success')) return 'success';
            if (toast.classList.contains('fl-error') || toast.classList.contains('toast-error')) return 'error';
            if (toast.classList.contains('fl-warning') || toast.classList.contains('toast-warning')) return 'warning';
            if (toast.classList.contains('fl-info') || toast.classList.contains('toast-info')) return 'info';
            return null;
        };

        const paintToast = (toast) => {
            const type = getToastType(toast);
            const theme = toastThemes[type];

            if (!theme || toast.dataset.themePainted === type) {
                return;
            }

            toast.dataset.themePainted = type;
            toast.style.setProperty('background', theme.background, 'important');
            toast.style.setProperty('background-color', 'transparent', 'important');
            toast.style.setProperty('border-left-color', theme.border, 'important');
            toast.style.setProperty('border-left-width', '4px', 'important');
            toast.style.setProperty('border-radius', '12px', 'important');
            toast.style.setProperty('box-shadow', '0 18px 42px rgb(15 23 42 / 0.12), 0 2px 8px rgb(15 23 42 / 0.06)', 'important');
            toast.style.setProperty('color', theme.text, 'important');

            toast.querySelectorAll('.fl-title, .toast-title').forEach((title) => {
                title.style.setProperty('color', theme.text, 'important');
                title.style.setProperty('font-size', '0.8125rem', 'important');
                title.style.setProperty('font-weight', '700', 'important');
            });

            toast.querySelectorAll('.fl-message, .toast-message').forEach((message) => {
                message.style.setProperty('color', theme.muted, 'important');
                message.style.setProperty('font-size', '0.765rem', 'important');
                message.style.setProperty('font-weight', '500', 'important');
            });
        };

        const refreshToasts = (root = document) => {
            if (!root.querySelectorAll) {
                return;
            }

            root.querySelectorAll('.fl-main-container .fl-container, #toast-container > div').forEach(paintToast);
        };

        document.addEventListener('DOMContentLoaded', () => refreshToasts());

        new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (!(node instanceof HTMLElement)) {
                        return;
                    }

                    if (node.matches('.fl-container, #toast-container > div')) {
                        paintToast(node);
                    }

                    refreshToasts(node);
                });
            });
        }).observe(document.documentElement, {
            childList: true,
            subtree: true,
        });
    })();
</script>
