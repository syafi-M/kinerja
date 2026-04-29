@props(['title' => 'Dashboard Mitra', 'maxWidth' => 'max-w-6xl'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'SAC-PONOROGO') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function () {
            const storageKey = 'mitra_theme_preference';
            const root = document.documentElement;
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

            let preference = 'system';

            try {
                preference = localStorage.getItem(storageKey) || 'system';
            } catch (error) {}

            const resolvedTheme = preference === 'system'
                ? (mediaQuery.matches ? 'dark' : 'light')
                : preference;

            root.dataset.mitraThemePreference = preference;
            root.dataset.mitraTheme = resolvedTheme;
            root.style.colorScheme = resolvedTheme;
        })();
    </script>

    <style>
        :root {
            --mitra-bg: #f8fafc;
            --mitra-bg-accent: radial-gradient(circle at top, rgba(37, 99, 235, 0.12), transparent 38%);
            --mitra-surface: rgba(255, 255, 255, 0.92);
            --mitra-surface-strong: #ffffff;
            --mitra-surface-soft: rgba(248, 250, 252, 0.9);
            --mitra-border: rgba(148, 163, 184, 0.24);
            --mitra-border-strong: rgba(148, 163, 184, 0.32);
            --mitra-text: #0f172a;
            --mitra-text-soft: #334155;
            --mitra-text-muted: #64748b;
            --mitra-accent: #2563eb;
            --mitra-accent-strong: #1d4ed8;
            --mitra-accent-soft: rgba(37, 99, 235, 0.12);
            --mitra-success: #059669;
            --mitra-warning: #d97706;
            --mitra-danger: #e11d48;
            --mitra-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        :root[data-mitra-theme="dark"] {
            --mitra-bg: #020617;
            --mitra-bg-accent: radial-gradient(circle at top, rgba(56, 189, 248, 0.14), transparent 34%);
            --mitra-surface: rgba(15, 23, 42, 0.9);
            --mitra-surface-strong: #0f172a;
            --mitra-surface-soft: rgba(15, 23, 42, 0.72);
            --mitra-border: rgba(148, 163, 184, 0.16);
            --mitra-border-strong: rgba(148, 163, 184, 0.24);
            --mitra-text: #e2e8f0;
            --mitra-text-soft: #cbd5e1;
            --mitra-text-muted: #94a3b8;
            --mitra-accent: #38bdf8;
            --mitra-accent-strong: #0ea5e9;
            --mitra-accent-soft: rgba(56, 189, 248, 0.14);
            --mitra-success: #34d399;
            --mitra-warning: #fbbf24;
            --mitra-danger: #fb7185;
            --mitra-shadow: 0 28px 60px rgba(2, 6, 23, 0.38);
        }

        body {
            overflow-x: hidden;
            color: var(--mitra-text);
            background:
                var(--mitra-bg-accent),
                linear-gradient(180deg, var(--mitra-bg) 0%, color-mix(in srgb, var(--mitra-bg) 92%, #000 8%) 100%);
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        html,
        body {
            scrollbar-width: thin;
            scrollbar-color: color-mix(in srgb, var(--mitra-border-strong) 84%, transparent) color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            width: 11px;
            height: 11px;
        }

        html::-webkit-scrollbar-track,
        body::-webkit-scrollbar-track {
            background: color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
        }

        html::-webkit-scrollbar-thumb,
        body::-webkit-scrollbar-thumb {
            background: color-mix(in srgb, var(--mitra-border-strong) 92%, transparent);
            border: 2px solid color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
            border-radius: 999px;
        }

        html::-webkit-scrollbar-thumb:hover,
        body::-webkit-scrollbar-thumb:hover {
            background: color-mix(in srgb, var(--mitra-accent) 26%, var(--mitra-border-strong) 74%);
        }

        html.mitra-theme-switching body,
        html.mitra-theme-switching,
        html.mitra-theme-switching body {
            overflow: hidden !important;
        }

        html.mitra-theme-switching body,
        html.mitra-theme-switching #sidebar,
        html.mitra-theme-switching #navbar,
        html.mitra-theme-switching .theme-card,
        html.mitra-theme-switching .mitra-panel,
        html.mitra-theme-switching .mitra-panel-soft,
        html.mitra-theme-switching .mitra-mobile-list-card,
        html.mitra-theme-switching .mitra-theme-button,
        html.mitra-theme-switching .mitra-theme-badge,
        html.mitra-theme-switching .mitra-theme-menu,
        html.mitra-theme-switching .mitra-theme-option,
        html.mitra-theme-switching .mitra-input,
        html.mitra-theme-switching .mitra-info-panel,
        html.mitra-theme-switching .mitra-modal-surface,
        html.mitra-theme-switching .mitra-link-accent,
        html.mitra-theme-switching .mitra-status-badge,
        html.mitra-theme-switching [class*="bg-slate-"],
        html.mitra-theme-switching [class*="bg-zinc-"],
        html.mitra-theme-switching [class*="text-slate-"],
        html.mitra-theme-switching [class*="border-slate-"],
        html.mitra-theme-switching [class*="border-zinc-"] {
            transition: none !important;
        }

        .theme-card { transition: all 0.25s ease; }
        #sidebar {
            transition:
                width 0.25s ease,
                transform 0.34s cubic-bezier(0.22, 1, 0.36, 1),
                background-color 0.25s ease,
                border-color 0.25s ease,
                box-shadow 0.25s ease;
            will-change: transform;
        }
        #navbar { transition: background-color 0.25s ease, border-color 0.25s ease; }
        #sidebarOverlay {
            transition: opacity 0.24s ease;
        }

        #sidebar,
        #navbar {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        #sidebar {
            background: color-mix(in srgb, var(--mitra-surface-strong) 92%, transparent);
            border-color: var(--mitra-border);
            box-shadow: var(--mitra-shadow);
        }

        #navbar {
            background: color-mix(in srgb, var(--mitra-surface) 92%, transparent);
            border-color: var(--mitra-border);
        }

        .mitra-panel {
            background: var(--mitra-surface);
            border: 1px solid var(--mitra-border);
            box-shadow: var(--mitra-shadow);
        }

        .mitra-panel-soft {
            background: color-mix(in srgb, var(--mitra-surface) 82%, transparent);
            border: 1px solid var(--mitra-border);
        }

        .mitra-nav-card:hover {
            border-color: color-mix(in srgb, var(--mitra-accent) 24%, var(--mitra-border)) !important;
            box-shadow: 0 18px 35px color-mix(in srgb, var(--mitra-accent) 8%, transparent);
        }

        .mitra-theme-button {
            color: var(--mitra-text-soft);
            background: color-mix(in srgb, var(--mitra-surface-strong) 88%, transparent);
            border-color: var(--mitra-border-strong);
        }

        .mitra-theme-button:hover {
            color: var(--mitra-text);
            background: color-mix(in srgb, var(--mitra-accent-soft) 55%, var(--mitra-surface-strong) 45%);
        }

        .mitra-theme-badge {
            color: var(--mitra-accent-strong);
            background: var(--mitra-accent-soft);
            border: 1px solid color-mix(in srgb, var(--mitra-accent) 35%, transparent);
        }

        .mitra-theme-menu {
            position: absolute;
            top: calc(100% + 0.55rem);
            right: 0;
            width: 16.5rem;
            padding: 0.7rem;
            border-radius: 1rem;
            border: 1px solid color-mix(in srgb, var(--mitra-border) 88%, transparent);
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--mitra-surface-strong) 98%, transparent) 0%, color-mix(in srgb, var(--mitra-surface) 94%, transparent) 100%);
            box-shadow:
                0 18px 40px rgba(15, 23, 42, 0.12),
                0 2px 8px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            opacity: 0;
            transform: translateY(-8px) scale(0.985);
            transform-origin: top right;
            pointer-events: none;
            transition:
                opacity 0.16s ease,
                transform 0.24s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.22s ease;
            z-index: 70;
        }

        .mitra-theme-menu.is-open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .mitra-theme-option {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 0.7rem;
            padding: 0.72rem 0.78rem;
            border: 1px solid transparent;
            border-radius: 0.85rem;
            color: var(--mitra-text-soft);
            transition:
                background-color 0.18s ease,
                border-color 0.18s ease,
                color 0.18s ease,
                transform 0.18s ease,
                box-shadow 0.18s ease;
        }

        .mitra-theme-option:hover {
            color: var(--mitra-text);
            background: color-mix(in srgb, var(--mitra-accent-soft) 36%, var(--mitra-surface-strong) 64%);
            border-color: color-mix(in srgb, var(--mitra-accent) 18%, transparent);
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.06);
        }

        .mitra-theme-option.is-active {
            color: var(--mitra-accent-strong);
            background: color-mix(in srgb, var(--mitra-accent-soft) 72%, var(--mitra-surface-strong) 28%);
            border-color: color-mix(in srgb, var(--mitra-accent) 24%, transparent);
            box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--mitra-accent) 10%, transparent);
        }

        .mitra-theme-option-label {
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .mitra-theme-option-note {
            font-size: 0.69rem;
            color: var(--mitra-text-muted);
        }

        .mitra-theme-transition {
            position: fixed;
            inset: 0;
            z-index: 120;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
            --mitra-transition-from-accent: rgba(37, 99, 235, 0.18);
            --mitra-transition-to-accent: rgba(56, 189, 248, 0.2);
            --mitra-transition-from-veil: rgba(248, 250, 252, 1);
            --mitra-transition-to-veil: rgba(2, 6, 23, 1);
            --mitra-transition-wipe: rgba(2, 6, 23, 1);
            --mitra-transition-core-bg: rgba(255, 255, 255, 0.06);
            --mitra-transition-core-border: rgba(255, 255, 255, 0.12);
            --mitra-transition-core-text: rgba(255, 255, 255, 0.72);
            --mitra-transition-core-accent: #2563eb;
            contain: layout style paint;
        }

        .mitra-theme-transition.is-active {
            opacity: 1;
            visibility: visible;
        }

        .mitra-theme-transition__veil {
            position: absolute;
            inset: 0;
            opacity: 0;
            overflow: hidden;
            will-change: opacity;
        }

        .mitra-theme-transition__veil::before,
        .mitra-theme-transition__veil::after {
            content: "";
            position: absolute;
            inset: 0;
            background: var(--mitra-transition-from-veil);
            opacity: 0;
        }

        .mitra-theme-transition__veil::after {
            background: var(--mitra-transition-to-veil);
        }

        .mitra-theme-transition__wipe {
            position: absolute;
            left: 0;
            width: 100%;
            height: 52%;
            opacity: 1;
            background: var(--mitra-transition-wipe);
            will-change: transform;
        }

        .mitra-theme-transition__wipe--top {
            top: 0;
            transform: translateY(-102%);
        }

        .mitra-theme-transition__wipe--bottom {
            bottom: 0;
            transform: translateY(102%);
        }

        .mitra-theme-transition__wipe--left,
        .mitra-theme-transition__wipe--right {
            top: 0;
            width: 50%;
            height: 100%;
            transform: translateX(0);
            opacity: 0;
        }

        .mitra-theme-transition__wipe--left {
            left: 0;
        }

        .mitra-theme-transition__wipe--right {
            left: auto;
            right: 0;
        }

        .mitra-theme-transition__core {
            position: relative;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 0.8rem;
            padding: 1rem 1.4rem;
            border-radius: 999px;
            border: 1px solid var(--mitra-transition-core-border);
            background: var(--mitra-transition-core-bg);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.03) inset,
                0 12px 32px rgba(2, 6, 23, 0.16);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transform: translateY(10px) scale(0.98);
            opacity: 0;
            min-width: 11rem;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__veil {
            animation: mitra-theme-veil 1520ms ease forwards;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__veil::before {
            animation: mitra-theme-color-from 1520ms ease forwards;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__veil::after {
            animation: mitra-theme-color-to 1520ms ease forwards;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__wipe--top {
            animation: mitra-theme-wipe-top 1520ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__wipe--bottom {
            animation: mitra-theme-wipe-bottom 1520ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__wipe--left {
            animation: mitra-theme-wipe-left 1520ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__wipe--right {
            animation: mitra-theme-wipe-right 1520ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .mitra-theme-transition.is-active .mitra-theme-transition__core {
            animation: mitra-theme-core 1520ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .mitra-theme-transition__spinner {
            position: relative;
            width: 4.5rem;
            height: 0.28rem;
            border-radius: 999px;
            background: color-mix(in srgb, var(--mitra-transition-core-border) 82%, transparent);
            overflow: hidden;
        }

        .mitra-theme-transition__spinner::before,
        .mitra-theme-transition__spinner::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            border-radius: inherit;
        }

        .mitra-theme-transition__spinner::before {
            left: 0;
            right: 0;
            background: color-mix(in srgb, var(--mitra-transition-core-border) 64%, transparent);
            opacity: 0.75;
        }

        .mitra-theme-transition__spinner::after {
            left: -36%;
            width: 36%;
            border-radius: 999px;
            background: var(--mitra-transition-core-accent);
            box-shadow: 0 0 20px color-mix(in srgb, var(--mitra-transition-core-accent) 36%, transparent);
            animation: mitra-theme-sweep 1040ms cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
        }

        .mitra-theme-transition__label {
            font-size: 0.64rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--mitra-transition-core-text);
            white-space: nowrap;
        }

        @keyframes mitra-theme-veil {
            0% { opacity: 0; }
            16% { opacity: 1; }
            66% { opacity: 1; }
            67%, 100% { opacity: 0; }
        }

        @keyframes mitra-theme-wipe-top {
            0% { transform: translateY(-102%); opacity: 1; }
            24% { transform: translateY(0); opacity: 1; }
            66% { transform: translateY(0); opacity: 1; }
            67%, 100% { transform: translateY(0); opacity: 0; }
        }

        @keyframes mitra-theme-wipe-bottom {
            0% { transform: translateY(102%); opacity: 1; }
            24% { transform: translateY(0); opacity: 1; }
            66% { transform: translateY(0); opacity: 1; }
            67%, 100% { transform: translateY(0); opacity: 0; }
        }

        @keyframes mitra-theme-wipe-left {
            0%, 66% { transform: translateX(0); opacity: 0; }
            67% { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(-102%); opacity: 1; }
        }

        @keyframes mitra-theme-wipe-right {
            0%, 66% { transform: translateX(0); opacity: 0; }
            67% { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(102%); opacity: 1; }
        }

        @keyframes mitra-theme-core {
            0% { opacity: 0; transform: translateY(14px) scale(0.98); }
            22% { opacity: 1; transform: translateY(0) scale(1); }
            64% { opacity: 1; transform: translateY(0) scale(1); }
            67%, 100% { opacity: 0; transform: translateY(-8px) scale(0.99); }
        }

        @keyframes mitra-theme-color-from {
            0% { opacity: 1; }
            42% { opacity: 1; }
            100% { opacity: 0; }
        }

        @keyframes mitra-theme-color-to {
            0% { opacity: 0; }
            30% { opacity: 0.2; }
            72% { opacity: 1; }
            100% { opacity: 1; }
        }

        @keyframes mitra-theme-sweep {
            0% { transform: translateX(0); opacity: 0.35; }
            18% { opacity: 1; }
            100% { transform: translateX(380%); opacity: 0.65; }
        }

        .mitra-section-title {
            color: var(--mitra-text-muted);
        }

        .mitra-empty-state {
            color: var(--mitra-text-muted);
        }

        .mitra-table {
            color: var(--mitra-text-soft);
        }

        .mitra-table thead {
            color: var(--mitra-text-muted);
        }

        .mitra-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: color-mix(in srgb, var(--mitra-border-strong) 84%, transparent) color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
        }

        .mitra-scrollbar::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .mitra-scrollbar::-webkit-scrollbar-track {
            background: color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
            border-radius: 999px;
        }

        .mitra-scrollbar::-webkit-scrollbar-thumb {
            background: color-mix(in srgb, var(--mitra-border-strong) 92%, transparent);
            border: 2px solid color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
            border-radius: 999px;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .mitra-scrollbar::-webkit-scrollbar-thumb:hover {
            background: color-mix(in srgb, var(--mitra-accent) 26%, var(--mitra-border-strong) 74%);
        }

        .mitra-scrollbar::-webkit-scrollbar-corner {
            background: transparent;
        }

        .mitra-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-gutter: stable both-edges;
        }

        .mitra-table-wrap,
        nav[role="navigation"],
        #sidebar nav {
            scrollbar-width: thin;
            scrollbar-color: color-mix(in srgb, var(--mitra-border-strong) 84%, transparent) color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
        }

        .mitra-table-wrap::-webkit-scrollbar,
        nav[role="navigation"]::-webkit-scrollbar,
        #sidebar nav::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .mitra-table-wrap::-webkit-scrollbar-track,
        nav[role="navigation"]::-webkit-scrollbar-track,
        #sidebar nav::-webkit-scrollbar-track {
            background: color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
            border-radius: 999px;
        }

        .mitra-table-wrap::-webkit-scrollbar-thumb,
        nav[role="navigation"]::-webkit-scrollbar-thumb,
        #sidebar nav::-webkit-scrollbar-thumb {
            background: color-mix(in srgb, var(--mitra-border-strong) 92%, transparent);
            border: 2px solid color-mix(in srgb, var(--mitra-surface-soft) 82%, transparent);
            border-radius: 999px;
        }

        .mitra-table-wrap::-webkit-scrollbar-thumb:hover,
        nav[role="navigation"]::-webkit-scrollbar-thumb:hover,
        #sidebar nav::-webkit-scrollbar-thumb:hover {
            background: color-mix(in srgb, var(--mitra-accent) 26%, var(--mitra-border-strong) 74%);
        }

        .mitra-table-row {
            border-color: var(--mitra-border);
        }

        .mitra-avatar-fallback {
            color: var(--mitra-text-soft);
            background: color-mix(in srgb, var(--mitra-border-strong) 55%, var(--mitra-surface-strong) 45%);
        }

        .mitra-input {
            background-color: color-mix(in srgb, var(--mitra-surface-strong) 94%, var(--mitra-bg) 6%) !important;
            color: var(--mitra-text) !important;
            border-color: var(--mitra-border) !important;
        }

        .mitra-input::placeholder {
            color: var(--mitra-text-muted);
        }

        .mitra-modal-surface {
            background: color-mix(in srgb, var(--mitra-surface-strong) 96%, transparent);
            border: 1px solid var(--mitra-border);
            box-shadow: var(--mitra-shadow);
        }

        .mitra-info-panel {
            background: color-mix(in srgb, var(--mitra-surface-strong) 94%, var(--mitra-bg) 6%);
            color: var(--mitra-text);
            border: 1px solid var(--mitra-border);
        }

        .mitra-mobile-list-card {
            background: color-mix(in srgb, var(--mitra-surface-strong) 92%, transparent);
            border: 1px solid var(--mitra-border);
            border-radius: 1rem;
        }

        .mitra-deferred-card {
            content-visibility: auto;
            contain-intrinsic-size: 300px;
        }

        .mitra-meta-label {
            color: var(--mitra-text-muted);
        }

        .mitra-status-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .mitra-status-pending {
            color: #78350f;
            background: rgba(245, 158, 11, 0.22);
        }

        .mitra-status-success {
            color: #ecfdf5;
            background: rgba(5, 150, 105, 0.9);
        }

        .mitra-status-danger {
            color: #fff1f2;
            background: rgba(225, 29, 72, 0.88);
        }

        .mitra-link-accent {
            color: var(--mitra-accent);
            font-weight: 600;
            transition: color 0.2s ease, opacity 0.2s ease;
        }

        .mitra-link-accent:hover {
            color: var(--mitra-accent-strong);
        }

        nav[role="navigation"] {
            width: 100%;
        }

        nav[role="navigation"] > div:first-child {
            margin-bottom: 0.85rem;
        }

        nav[role="navigation"] span[aria-current="page"] span,
        nav[role="navigation"] a,
        nav[role="navigation"] span[aria-disabled="true"] span {
            border-radius: 0.85rem !important;
        }

        .mitra-text-muted { color: var(--mitra-text-muted); }
        .mitra-text-soft { color: var(--mitra-text-soft); }
        .mitra-text-strong { color: var(--mitra-text); }
        .mitra-accent { color: var(--mitra-accent); }

        html[data-mitra-theme] body[class*="bg-slate-"],
        html[data-mitra-theme] body[class*="bg-zinc-"] {
            background-color: transparent !important;
        }

        html[data-mitra-theme] [class~="bg-slate-700"],
        html[data-mitra-theme] [class~="bg-slate-800"],
        html[data-mitra-theme] [class~="bg-slate-900"],
        html[data-mitra-theme] [class~="bg-zinc-700"],
        html[data-mitra-theme] [class~="bg-zinc-800"],
        html[data-mitra-theme] [class~="bg-zinc-900"] {
            background-color: var(--mitra-surface-strong) !important;
        }

        html[data-mitra-theme] [class~="bg-slate-700/50"],
        html[data-mitra-theme] [class~="bg-slate-800/50"],
        html[data-mitra-theme] [class~="bg-slate-800/80"],
        html[data-mitra-theme] [class~="bg-slate-900/95"],
        html[data-mitra-theme] [class~="bg-slate-800/95"],
        html[data-mitra-theme] [class~="bg-zinc-900/95"] {
            background-color: var(--mitra-surface) !important;
        }

        html[data-mitra-theme] [class~="border-slate-600"],
        html[data-mitra-theme] [class~="border-slate-600/50"],
        html[data-mitra-theme] [class~="border-slate-700"],
        html[data-mitra-theme] [class~="border-slate-700/50"],
        html[data-mitra-theme] [class~="border-slate-500"],
        html[data-mitra-theme] [class~="border-slate-300"],
        html[data-mitra-theme] [class~="border-zinc-600"] {
            border-color: var(--mitra-border) !important;
        }

        html[data-mitra-theme] [class~="text-slate-100"],
        html[data-mitra-theme] [class~="text-slate-200"] {
            color: var(--mitra-text) !important;
        }

        html[data-mitra-theme] [class~="text-slate-300"] {
            color: var(--mitra-text-soft) !important;
        }

        html[data-mitra-theme] [class~="text-slate-400"],
        html[data-mitra-theme] [class~="text-slate-500"],
        html[data-mitra-theme] [class~="text-slate-600"] {
            color: var(--mitra-text-muted) !important;
        }

        html[data-mitra-theme] [class~="bg-slate-200"] {
            background-color: color-mix(in srgb, var(--mitra-surface-strong) 94%, var(--mitra-bg) 6%) !important;
        }

        html[data-mitra-theme] [class~="text-slate-800"] {
            color: var(--mitra-text) !important;
        }

        html[data-mitra-theme] [class*="hover:bg-slate-600"]:hover,
        html[data-mitra-theme] [class*="hover:bg-slate-800"]:hover,
        html[data-mitra-theme] [class*="hover:bg-slate-800/30"]:hover {
            background-color: color-mix(in srgb, var(--mitra-accent-soft) 36%, var(--mitra-surface-strong) 64%) !important;
        }

        html[data-mitra-theme] [class*="hover:text-white"]:hover {
            color: var(--mitra-text) !important;
        }

        html[data-mitra-theme] table thead {
            background-color: color-mix(in srgb, var(--mitra-bg) 65%, var(--mitra-surface-strong) 35%) !important;
        }

        html[data-mitra-theme="dark"] .btn-ghost:hover {
            background-color: rgba(148, 163, 184, 0.12) !important;
        }

        @media (min-width: 1024px) {
            #appContent {
                padding-left: 18rem;
                transition: padding-left 0.25s ease;
            }
            body.sidebar-collapsed #sidebar {
                width: 5.5rem !important;
            }
            body.sidebar-collapsed #appContent {
                padding-left: 5.5rem;
            }
            body.sidebar-collapsed #sidebar .sidebar-text,
            body.sidebar-collapsed #sidebar .sidebar-section-title,
            body.sidebar-collapsed #sidebar .sidebar-user-card,
            body.sidebar-collapsed #sidebar .sidebar-logout-text {
                display: none;
            }
            body.sidebar-collapsed #sidebar .sidebar-brand,
            body.sidebar-collapsed #sidebar .sidebar-item,
            body.sidebar-collapsed #sidebar .sidebar-logout-btn {
                justify-content: center;
            }
            body.sidebar-collapsed #sidebar .sidebar-indicator {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .mitra-mobile-card {
                padding: 1.1rem;
                border-radius: 1.25rem;
            }

            .mitra-mobile-stack {
                flex-direction: column;
                align-items: flex-start;
            }

            .mitra-mobile-table {
                min-width: 680px;
            }

            .mitra-mobile-table-wide {
                min-width: 840px;
            }

            #sidebar {
                width: min(20rem, calc(100vw - 1rem));
                margin: 0.5rem 0 0.5rem 0.5rem;
                border-radius: 1.5rem;
            }

            #appContent main > div {
                padding-top: 1.15rem;
                padding-bottom: 5rem;
            }

            #navbar .text-sm.font-bold {
                font-size: 0.78rem;
                line-height: 1.15rem;
                letter-spacing: 0.04em;
            }

            .mitra-panel,
            .mitra-panel-soft,
            .mitra-mobile-list-card {
                box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
            }

            .mitra-panel-soft .p-4,
            .mitra-panel .p-4 {
                padding: 0.9rem;
            }

            .btn,
            button,
            a.btn,
            input,
            select,
            textarea {
                min-height: 2.75rem;
            }

            nav[role="navigation"] {
                margin-top: 0.25rem;
            }

            nav[role="navigation"] > div:first-child {
                display: none;
            }

            nav[role="navigation"] > div:last-child {
                display: flex;
                flex-direction: column;
                gap: 0.85rem;
                align-items: stretch;
            }

            nav[role="navigation"] > div:last-child > div:first-child {
                display: flex;
                justify-content: center;
                font-size: 0.8rem;
                color: var(--mitra-text-muted);
            }

            nav[role="navigation"] > div:last-child > div:last-child {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.45rem;
            }

            nav[role="navigation"] a,
            nav[role="navigation"] span[aria-disabled="true"] span,
            nav[role="navigation"] span[aria-current="page"] span {
                min-width: 2.75rem;
                min-height: 2.75rem;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                padding: 0.65rem 0.85rem !important;
                font-size: 0.82rem !important;
            }

            .mitra-theme-menu {
                display: none !important;
            }
        }
    </style>
</head>
<body id="mainBody" class="font-sans antialiased">
    @php
        $primaryMenus = [
            ['label' => 'Dashboard', 'route' => 'dashboard.index', 'icon' => 'ri-dashboard-3-line'],
            ['label' => 'Kehadiran', 'route' => 'mitra_absensi', 'icon' => 'ri-fingerprint-line'],
            ['label' => 'Karyawan', 'route' => 'mitra_user', 'icon' => 'ri-group-line'],
        ];
        $operationMenus = [
            ['label' => 'Laporan', 'route' => 'mitra_laporan', 'icon' => 'ri-file-list-3-line'],
            ['label' => 'Lembur', 'route' => 'mitra_lembur', 'icon' => 'ri-time-line'],
            ['label' => 'Izin', 'route' => 'mitra_izin', 'icon' => 'ri-article-line'],
            ['label' => 'Rekap Bulanan', 'route' => 'mitra_rekap', 'icon' => 'ri-calendar-schedule-line'],
        ];
    @endphp

    <div id="themeTransition" class="mitra-theme-transition" aria-hidden="true">
        <div class="mitra-theme-transition__veil"></div>
        <div class="mitra-theme-transition__wipe mitra-theme-transition__wipe--top"></div>
        <div class="mitra-theme-transition__wipe mitra-theme-transition__wipe--bottom"></div>
        <div class="mitra-theme-transition__wipe mitra-theme-transition__wipe--left"></div>
        <div class="mitra-theme-transition__wipe mitra-theme-transition__wipe--right"></div>
        <div class="mitra-theme-transition__core">
            <div class="mitra-theme-transition__spinner"></div>
            <div class="mitra-theme-transition__label">Mengubah Tema</div>
        </div>
    </div>

    <div class="relative min-h-screen lg:flex">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col max-h-screen p-4 transition-transform duration-300 -translate-x-full border-r shadow-xl w-72 lg:translate-x-0">
            <div class="flex items-center gap-3 px-2 py-3 sidebar-brand">
                <div class="flex items-center justify-center rounded-lg shadow-lg w-9 h-9" style="background: var(--mitra-accent); box-shadow: 0 12px 24px color-mix(in srgb, var(--mitra-accent) 24%, transparent);">
                    <i class="text-lg text-white ri-dashboard-3-fill"></i>
                </div>
                <div class="sidebar-text">
                    <p class="text-xs tracking-widest uppercase mitra-text-muted">Portal Mitra</p>
                    <p class="text-sm font-bold mitra-text-strong">Dashboard</p>
                </div>
            </div>

            <div class="px-3 py-3 mt-3 border sidebar-user-card rounded-2xl mitra-panel-soft">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mitra-text-muted">Akun Aktif</p>
                <p class="mt-1 text-sm font-semibold truncate mitra-text-strong">{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</p>
                <p class="text-xs truncate mitra-text-muted">{{ auth()->user()->email }}</p>
            </div>

            <nav class="flex-1 mt-6 overflow-y-auto">
                <p class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-[0.22em] mitra-text-muted">Menu Utama</p>
                <div class="space-y-1">
                    @foreach($primaryMenus as $menu)
                        @if(Route::has($menu['route']))
                            @php $isActive = request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*'); @endphp
                            <a href="{{ route($menu['route']) }}"
                               title="{{ $menu['label'] }}"
                               class="sidebar-item relative flex items-center gap-3 px-3 py-2 rounded-xl border transition {{ $isActive ? 'border-blue-500/30' : 'border-transparent hover:bg-slate-800 hover:text-white' }}"
                               style="{{ $isActive ? 'background: var(--mitra-accent-soft); color: var(--mitra-accent-strong);' : 'color: var(--mitra-text-soft);' }}">
                                <span class="sidebar-indicator absolute left-0 w-1 rounded-r {{ $isActive ? 'h-6' : 'h-0' }}" style="{{ $isActive ? 'background: var(--mitra-accent);' : '' }}"></span>
                                <i class="{{ $menu['icon'] }} text-lg"></i>
                                <span class="text-sm font-semibold sidebar-text">{{ $menu['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>

                <p class="sidebar-section-title px-2 mt-2.5 mb-2 text-[10px] font-black uppercase tracking-[0.22em] mitra-text-muted">Operasional</p>
                <div class="space-y-1">
                    @foreach($operationMenus as $menu)
                        @if(Route::has($menu['route']))
                            @php $isActive = request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*'); @endphp
                            <a href="{{ route($menu['route']) }}"
                               title="{{ $menu['label'] }}"
                               class="sidebar-item relative flex items-center gap-3 px-3 py-2 rounded-xl border transition {{ $isActive ? 'border-blue-500/30' : 'border-transparent hover:bg-slate-800 hover:text-white' }}"
                               style="{{ $isActive ? 'background: var(--mitra-accent-soft); color: var(--mitra-accent-strong);' : 'color: var(--mitra-text-soft);' }}">
                                <span class="sidebar-indicator absolute left-0 w-1 rounded-r {{ $isActive ? 'h-6' : 'h-0' }}" style="{{ $isActive ? 'background: var(--mitra-accent);' : '' }}"></span>
                                <i class="{{ $menu['icon'] }} text-lg"></i>
                                <span class="text-sm font-semibold sidebar-text">{{ $menu['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </nav>

            <div class="pt-4 mt-4 border-t" style="border-color: var(--mitra-border);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn flex items-center justify-center w-full gap-2 px-4 py-2.5 text-xs font-bold transition rounded-xl hover:text-white" style="background: color-mix(in srgb, var(--mitra-danger) 12%, transparent); color: var(--mitra-danger);">
                        <i class="ri-logout-circle-r-line"></i>
                        <span class="sidebar-logout-text">LOGOUT</span>
                    </button>
                </form>
            </div>
        </aside>

        <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden opacity-0 bg-slate-950/45 lg:hidden"></div>

        <div id="appContent" class="flex flex-col flex-1 min-h-screen">
            <nav id="navbar" class="sticky top-0 z-30 border-b shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button id="sidebarToggle" type="button" class="inline-flex items-center justify-center w-10 h-10 border rounded-lg lg:hidden mitra-theme-button">
                            <i class="text-lg ri-menu-line"></i>
                        </button>
                        <button id="sidebarCollapseToggle" type="button" class="items-center justify-center hidden w-10 h-10 border rounded-lg lg:inline-flex mitra-theme-button" title="Collapse sidebar">
                            <i id="sidebarCollapseIcon" class="text-lg ri-layout-left-2-line"></i>
                        </button>
                        <span class="text-sm font-bold tracking-tight uppercase mitra-text-strong">{{ $title }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="themeModeLabel" class="hidden px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.22em] rounded-full sm:inline-flex mitra-theme-badge"></span>
                        <div id="themePicker" class="relative">
                            <button id="themeToggle" type="button" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold border rounded-xl mitra-theme-button" title="Ganti tema">
                                <i id="themeToggleIcon" class="text-base ri-computer-line"></i>
                                <span class="hidden sm:inline">Tema</span>
                            </button>
                            <div id="themeMenu" class="mitra-theme-menu">
                                <p class="px-3 pb-2 text-[10px] font-black uppercase tracking-[0.24em] mitra-text-muted">Pilih Tema</p>
                                <div class="space-y-2">
                                    <button type="button" class="mitra-theme-option" data-theme-choice="system">
                                        <span class="mitra-theme-option-label">
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl" style="background: color-mix(in srgb, var(--mitra-accent-soft) 70%, transparent); color: var(--mitra-accent);">
                                                <i class="text-base ri-computer-line"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-semibold">System</span>
                                                <span class="mitra-theme-option-note">Ikuti tema browser</span>
                                            </span>
                                        </span>
                                        <i class="ri-check-line"></i>
                                    </button>
                                    <button type="button" class="mitra-theme-option" data-theme-choice="light">
                                        <span class="mitra-theme-option-label">
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl" style="background: rgba(245, 158, 11, 0.12); color: #b45309;">
                                                <i class="text-base ri-sun-line"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-semibold">Light</span>
                                                <span class="mitra-theme-option-note">Tampilan terang</span>
                                            </span>
                                        </span>
                                        <i class="ri-check-line"></i>
                                    </button>
                                    <button type="button" class="mitra-theme-option" data-theme-choice="dark">
                                        <span class="mitra-theme-option-label">
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl" style="background: rgba(15, 23, 42, 0.10); color: var(--mitra-text-strong);">
                                                <i class="text-base ri-moon-clear-line"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-semibold">Dark</span>
                                                <span class="mitra-theme-option-note">Tampilan gelap</span>
                                            </span>
                                        </span>
                                        <i class="ri-check-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="flex-1 pb-24">
                <div class="{{ $maxWidth }} px-4 py-8 mx-auto lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <script>
        const root = document.documentElement;
        const themeStorageKey = 'mitra_theme_preference';
        const themeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const themeToggle = document.getElementById('themeToggle');
        const themeToggleIcon = document.getElementById('themeToggleIcon');
        const themeModeLabel = document.getElementById('themeModeLabel');
        const themePicker = document.getElementById('themePicker');
        const themeMenu = document.getElementById('themeMenu');
        const themeOptions = Array.from(document.querySelectorAll('[data-theme-choice]'));
        const desktopThemeMediaQuery = window.matchMedia('(hover: hover) and (pointer: fine) and (min-width: 1024px)');
        const themeTransition = document.getElementById('themeTransition');
        const themeTransitionDuration = 1580;
        const themeTransitionSwapDelay = 340;
        let themeTransitionTimer = null;
        let themeTransitionSwapTimer = null;

        function getResolvedTheme(preference) {
            return preference === 'system'
                ? (themeMediaQuery.matches ? 'dark' : 'light')
                : preference;
        }

        function updateThemeToggle(preference, resolvedTheme) {
            if (!themeToggle || !themeToggleIcon || !themeModeLabel) return;

            const iconMap = {
                system: 'ri-computer-line',
                light: 'ri-sun-line',
                dark: 'ri-moon-clear-line',
            };

            const labelMap = {
                system: 'System',
                light: 'Light',
                dark: 'Dark',
            };

            themeToggleIcon.className = `text-base ${iconMap[preference] || iconMap.system}`;
            themeModeLabel.textContent = labelMap[preference] || labelMap.system;
            themeToggle.setAttribute('aria-label', `Tema saat ini ${labelMap[preference] || labelMap.system}, klik untuk mengganti`);
            themeToggle.setAttribute('title', `Tema ${labelMap[preference] || labelMap.system} (${resolvedTheme})`);

            themeOptions.forEach(function (option) {
                option.classList.toggle('is-active', option.dataset.themeChoice === preference);
            });
        }

        function getThemeTransitionPalette(theme) {
            const palettes = {
                light: {
                    accent: 'rgba(37, 99, 235, 0.18)',
                    veil: 'rgba(248, 250, 252, 1)',
                    wipe: 'rgba(248, 250, 252, 1)',
                    coreBg: 'rgba(255, 255, 255, 0.08)',
                    coreBorder: 'rgba(148, 163, 184, 0.22)',
                    coreText: 'rgba(15, 23, 42, 0.54)',
                    coreAccent: '#2563eb',
                },
                dark: {
                    accent: 'rgba(56, 189, 248, 0.22)',
                    veil: 'rgba(2, 6, 23, 1)',
                    wipe: 'rgba(2, 6, 23, 1)',
                    coreBg: 'rgba(255, 255, 255, 0.05)',
                    coreBorder: 'rgba(148, 163, 184, 0.16)',
                    coreText: 'rgba(226, 232, 240, 0.62)',
                    coreAccent: '#38bdf8',
                },
            };

            return palettes[theme] || palettes.light;
        }

        function setThemeTransitionPalette(fromTheme, toTheme) {
            if (!themeTransition) return;

            const fromPalette = getThemeTransitionPalette(fromTheme);
            const toPalette = getThemeTransitionPalette(toTheme);

            themeTransition.style.setProperty('--mitra-transition-from-accent', fromPalette.accent);
            themeTransition.style.setProperty('--mitra-transition-to-accent', toPalette.accent);
            themeTransition.style.setProperty('--mitra-transition-from-veil', fromPalette.veil);
            themeTransition.style.setProperty('--mitra-transition-to-veil', toPalette.veil);
            themeTransition.style.setProperty('--mitra-transition-wipe', toPalette.wipe);
            themeTransition.style.setProperty('--mitra-transition-core-bg', toPalette.coreBg);
            themeTransition.style.setProperty('--mitra-transition-core-border', toPalette.coreBorder);
            themeTransition.style.setProperty('--mitra-transition-core-text', toPalette.coreText);
            themeTransition.style.setProperty('--mitra-transition-core-accent', toPalette.coreAccent);
        }

        function setThemeState(preference, persist) {
            const resolvedTheme = getResolvedTheme(preference);

            root.dataset.mitraThemePreference = preference;
            root.dataset.mitraTheme = resolvedTheme;
            root.style.colorScheme = resolvedTheme;

            if (persist) {
                try {
                    localStorage.setItem(themeStorageKey, preference);
                } catch (error) {}
            }

            updateThemeToggle(preference, resolvedTheme);
        }

        function showThemeTransition(preference, persist) {
            if (!themeTransition) {
                setThemeState(preference, persist);
                return;
            }

            const currentResolvedTheme = root.dataset.mitraTheme || getResolvedTheme(root.dataset.mitraThemePreference || 'system');
            const nextResolvedTheme = getResolvedTheme(preference);

            if (themeTransitionTimer) {
                window.clearTimeout(themeTransitionTimer);
            }
            if (themeTransitionSwapTimer) {
                window.clearTimeout(themeTransitionSwapTimer);
            }

            setThemeTransitionPalette(currentResolvedTheme, nextResolvedTheme);
            root.classList.add('mitra-theme-switching');
            themeTransition.classList.remove('is-active');
            void themeTransition.offsetWidth;
            themeTransition.classList.add('is-active');

            themeTransitionSwapTimer = window.setTimeout(function () {
                setThemeState(preference, persist);
            }, themeTransitionSwapDelay);

            themeTransitionTimer = window.setTimeout(function () {
                themeTransition.classList.remove('is-active');
                root.classList.remove('mitra-theme-switching');
            }, themeTransitionDuration);
        }

        function applyTheme(preference, options = {}) {
            const persist = typeof options === 'boolean' ? options : Boolean(options.persist);
            const withTransition = typeof options === 'object' && Boolean(options.withTransition);

            if (withTransition) {
                showThemeTransition(preference, persist);
                return;
            }

            setThemeState(preference, persist);
        }

        function cycleThemePreference() {
            const currentPreference = root.dataset.mitraThemePreference || 'system';
            const nextPreference = currentPreference === 'system'
                ? 'light'
                : (currentPreference === 'light' ? 'dark' : 'system');

            applyTheme(nextPreference, { persist: true, withTransition: true });
        }

        function isDesktopThemeMenuMode() {
            return desktopThemeMediaQuery.matches;
        }

        function openThemeMenu() {
            if (!themeMenu || !isDesktopThemeMenuMode()) return;
            themeMenu.classList.add('is-open');
            themeToggle.setAttribute('aria-expanded', 'true');
        }

        function closeThemeMenu() {
            if (!themeMenu) return;
            themeMenu.classList.remove('is-open');
            themeToggle.setAttribute('aria-expanded', 'false');
        }

        function syncResponsiveImages() {
            const activeViewport = window.innerWidth >= 768 ? 'desktop' : 'mobile';
            const groups = document.querySelectorAll('[data-viewport-content]');

            groups.forEach(function (group) {
                const isActiveGroup = group.dataset.viewportContent === activeViewport;

                group.querySelectorAll('img[data-responsive-src]').forEach(function (image) {
                    const targetSrc = image.dataset.responsiveSrc;

                    if (isActiveGroup) {
                        if (targetSrc && image.getAttribute('src') !== targetSrc) {
                            image.setAttribute('src', targetSrc);
                        }
                        return;
                    }

                    if (image.hasAttribute('src')) {
                        image.removeAttribute('src');
                    }
                });
            });
        }

        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarCollapseToggle = document.getElementById('sidebarCollapseToggle');
        const sidebarCollapseIcon = document.getElementById('sidebarCollapseIcon');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            if (!sidebar || !sidebarOverlay) return;
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
            requestAnimationFrame(function () {
                sidebarOverlay.classList.remove('opacity-0');
                sidebarOverlay.classList.add('opacity-100');
            });
        }

        function closeSidebar() {
            if (!sidebar || !sidebarOverlay) return;
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.remove('opacity-100');
            sidebarOverlay.classList.add('opacity-0');
            window.setTimeout(function () {
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebarOverlay.classList.add('hidden');
                }
            }, 240);
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        function setSidebarCollapsed(collapsed) {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            if (sidebarCollapseIcon) {
                sidebarCollapseIcon.className = collapsed
                    ? 'text-lg ri-layout-right-2-line'
                    : 'text-lg ri-layout-left-2-line';
            }
            try {
                localStorage.setItem('mitra_sidebar_collapsed', collapsed ? '1' : '0');
            } catch (error) {}
        }

        if (window.innerWidth >= 1024) {
            try {
                const stored = localStorage.getItem('mitra_sidebar_collapsed') === '1';
                setSidebarCollapsed(stored);
            } catch (error) {}
        }

        if (sidebarCollapseToggle) {
            sidebarCollapseToggle.addEventListener('click', function () {
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                setSidebarCollapsed(!isCollapsed);
            });
        }

        if (themeToggle) {
            themeToggle.addEventListener('click', function (event) {
                if (isDesktopThemeMenuMode()) {
                    event.preventDefault();
                    if (themeMenu && themeMenu.classList.contains('is-open')) {
                        closeThemeMenu();
                    } else {
                        openThemeMenu();
                    }
                    return;
                }

                cycleThemePreference();
            });
        }

        themeOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                const preference = option.dataset.themeChoice || 'system';
                applyTheme(preference, { persist: true, withTransition: true });
                closeThemeMenu();
            });
        });

        document.addEventListener('click', function (event) {
            if (!themePicker || !isDesktopThemeMenuMode()) return;
            if (!themePicker.contains(event.target)) {
                closeThemeMenu();
            }
        });

        if (typeof desktopThemeMediaQuery.addEventListener === 'function') {
            desktopThemeMediaQuery.addEventListener('change', function () {
                closeThemeMenu();
            });
        } else if (typeof desktopThemeMediaQuery.addListener === 'function') {
            desktopThemeMediaQuery.addListener(function () {
                closeThemeMenu();
            });
        }

        let responsiveImageTimer = null;
        syncResponsiveImages();
        window.addEventListener('resize', function () {
            window.clearTimeout(responsiveImageTimer);
            responsiveImageTimer = window.setTimeout(syncResponsiveImages, 120);
        });

        if (typeof themeMediaQuery.addEventListener === 'function') {
            themeMediaQuery.addEventListener('change', function () {
                if ((root.dataset.mitraThemePreference || 'system') === 'system') {
                    applyTheme('system');
                }
            });
        } else if (typeof themeMediaQuery.addListener === 'function') {
            themeMediaQuery.addListener(function () {
                if ((root.dataset.mitraThemePreference || 'system') === 'system') {
                    applyTheme('system');
                }
            });
        }

        applyTheme(root.dataset.mitraThemePreference || 'system');
    </script>
</body>
</html>
