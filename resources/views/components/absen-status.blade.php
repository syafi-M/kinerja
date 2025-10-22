<div class="flex items-center justify-start">
    @if ($absenP && $luweh1Dino && $absenP?->absensi_type_pulang == null)
        <div class="inset-0 px-4 py-2 ml-5 font-semibold text-center rounded-tr-lg rounded-bl-lg shadow-md w-fit"
            style="color: #DEDEDE; background-color: #8F0000; font-size: 10pt; {{ $rillSholat ? '' : 'margin-bottom: 10px;' }}">
            <p>Kamu Belum Absen Pulang !!</p>
        </div>
    @endif

    @if ($izin)
        <span class="hidden">
            <span id="waktuIzin" data-waktu="{{ $izin->updated_at->format('H:i') }}"></span>
        </span>
        <div id="inpoIzin"
            class="text-center hidden rounded-tr-lg rounded-bl-lg my-5 sm:w-fit text-md sm:text-xl font-semibold text-slate-50 {{ $statusClass }} py-2 px-4 shadow-md ml-5 sm:ml-10 inset-0">
            <p>{{ $statusMessage }}</p>
        </div>
    @endif
</div>
