{{-- Isi satu sel kalender: tanggal + status/ringkasan. Dipakai oleh sel yang
     bisa diklik maupun sel yang dinonaktifkan saat pekan laporan ditutup. --}}
<span class="overflow-hidden text-[11px] leading-none sm:text-xs md:text-sm flex justify-between items-center">
    {{ $date->day }}
    <i class="ri-pushpin-line {{ $date->isToday() ? '' : 'hidden' }}"></i>
</span>

@if ($item['hasData'])

    <x-checkpoint-status-row :counts="$item['counts']" :has-data="$item['hasData']" size="md" />

@elseif ($date->isWeekend())

    <span
        class="hidden truncate text-[8px] font-semibold uppercase tracking-wide opacity-85 sm:block sm:text-[9px] md:text-[10px]">
        Libur
    </span>

@endif
