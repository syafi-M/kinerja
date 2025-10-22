@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'inline-flex items-center gap-2 px-4 py-2 font-semibold text-sm rounded-md text-slate-800 bg-yellow-400 hover:bg-yellow-500 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 ease-out'
            : 'inline-flex text-white items-center w-auto mt-4 h-3/6 px-2 hover:bg-yellow-400 hover:text-white hover:shadow-md rounded-md transition all ease-in-out .2s font-semibold bg-slate-400/30 overflow-hidden';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
