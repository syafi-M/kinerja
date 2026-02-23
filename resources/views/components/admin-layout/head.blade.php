<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Admin Kinerja SAC-PONOROGO</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        /* Global polish for legacy admin pages migrated from x-app-layout */
        .legacy-admin .btn {
            border-radius: 0.75rem;
            min-height: 2.4rem;
            height: 2.4rem;
            font-size: 0.8rem;
            font-weight: 600;
            padding-inline: 0.85rem;
            box-shadow: none;
        }

        .legacy-admin .input,
        .legacy-admin .select,
        .legacy-admin .file-input,
        .legacy-admin textarea {
            border-radius: 0.75rem;
            border-color: rgb(229 231 235);
            background-color: rgb(249 250 251);
            min-height: 2.5rem;
            height: 2.5rem;
            font-size: 0.85rem;
        }

        .legacy-admin textarea {
            min-height: 6rem;
            height: auto;
        }

        .legacy-admin .table {
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgb(243 244 246);
            background: white;
        }

        .legacy-admin .table :where(th) {
            background: rgb(249 250 251) !important;
            color: rgb(75 85 99);
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .legacy-admin .table :where(td) {
            font-size: 0.85rem;
            color: rgb(55 65 81);
            vertical-align: top;
        }

        .legacy-admin .table tr:hover td {
            background: rgb(239 246 255 / 0.55);
        }

        .legacy-admin .bg-slate-100,
        .legacy-admin .bg-slate-50,
        .legacy-admin .bg-slate-500 {
            background: white !important;
            border: 1px solid rgb(243 244 246);
            border-radius: 1rem;
        }

        .legacy-admin .shadow,
        .legacy-admin .shadow-md {
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
        }

        .legacy-admin .rounded-md {
            border-radius: 0.85rem !important;
        }

        .legacy-admin .rounded {
            border-radius: 0.65rem !important;
        }
    </style>

    @stack('styles')

</head>

