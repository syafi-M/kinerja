@props(['active'])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 font-semibold text-sm rounded-md transition-all duration-200';

    $activeClasses = 'text-slate-800 bg-yellow-400 hover:bg-yellow-500 shadow-md hover:shadow-lg';

    $inactiveClasses = 'text-white bg-slate-400/30 hover:bg-yellow-400 hover:text-white hover:shadow-md';

    $classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
