<x-mitra-layout title="Dashboard Kehadiran">
    @php
        $totalKehadiran = $jumlahKehadiran ?? collect($absensiList ?? [])->pluck('user_id')->filter()->unique()->count();
        $totalHariIni = $jumlahAbsensiHariIni ?? 0;
        $labelPeriode = $periodeLabel ?? 'Hari Ini';
        $currentPeriode = $periode ?? 'today';
    @endphp

    <div class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
        <div class="flex flex-col justify-between gap-5 md:flex-row md:items-center">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-blue-400">Data Kehadiran Karyawan</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">{{ $totalKehadiran }} Karyawan Hadir</h1>
                <p class="mt-1 text-sm text-slate-300">
                    Periode: <span class="font-semibold text-white">{{ $labelPeriode }}</span>
                    <span class="mx-2 text-slate-500">|</span>
                    Hadir Hari Ini: <span class="font-semibold text-white">{{ $totalHariIni }}</span>
                </p>
            </div>
            <div class="w-full md:w-auto">
                <div class="inline-flex w-full p-1 bg-slate-800 rounded-xl md:w-auto">
                    <a href="{{ route('mitra_absensi', ['periode' => 'today']) }}"
                       class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-semibold transition rounded-lg md:flex-none {{ $currentPeriode === 'today' ? 'bg-blue-500 text-white shadow-sm' : 'text-slate-300 hover:text-white' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('mitra_absensi', ['periode' => '30days']) }}"
                       class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-semibold transition rounded-lg md:flex-none {{ $currentPeriode === '30days' ? 'bg-blue-500 text-white shadow-sm' : 'text-slate-300 hover:text-white' }}">
                        30 Hari Terakhir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-hidden border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
        <div class="p-4 border-b border-slate-600/50">
            <h2 class="text-xs font-black tracking-[0.3em] uppercase text-slate-400">Daftar Kehadiran {{ $labelPeriode }}</h2>
        </div>
        <div class="p-4">
            @if($absensiList->isEmpty())
                <p class="text-sm italic text-slate-400">Belum ada data kehadiran pada periode ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-300">
                        <thead class="text-xs uppercase bg-slate-800/50 text-slate-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Foto</th>
                                <th scope="col" class="px-4 py-3">Nama</th>
                                <th scope="col" class="px-4 py-3">Shift</th>
                                <th scope="col" class="px-4 py-3">Tanggal</th>
                                <th scope="col" class="px-4 py-3">Waktu Hadir</th>
                                <th scope="col" class="px-4 py-3">Keterangan</th>
                                <th scope="col" class="px-4 py-3">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absensiList as $absen)
                                <tr class="border-b border-slate-700/50 hover:bg-slate-800/30">
                                    <td class="px-4 py-3">
                                        @if($absen->image)
                                            <img
                                                src="{{ asset('storage/images/' . $absen->image) }}"
                                                alt="Foto {{ $absen->user?->nama_lengkap ?? 'Karyawan' }}"
                                                class="object-cover w-10 h-10 rounded-full"
                                            >
                                        @else
                                            <div class="flex items-center justify-center w-10 h-10 text-xs font-bold rounded-full bg-slate-600 text-slate-200">
                                                {{ strtoupper(substr($absen->user?->nama_lengkap ?? 'K', 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-100">{{ capitalizeWords($absen->user?->nama_lengkap ?? '-') }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-100">{{ $absen->shift?->shift_name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $absen->shift?->jam_start ?? '-' }} - {{ \Carbon\Carbon::parse($absen->shift?->jam_end ?? '00:00:00')->subHour()->format('H:i') }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $absen->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $absen->created_at->format('H:i') }} - {{ \Carbon\Carbon::parse($absen->absensi_type_pulang)->format('H:i') }}</td>
                                    <td class="px-4 py-3 capitalize">
                                        @php
                                            $keterangan = strtolower(trim((string) ($absen->keterangan ?? 'hadir')));
                                            $isTerlambat = $keterangan === 'telat';
                                            $durasiTerlambat = '';

                                            if ($isTerlambat) {
                                                $jamAbs = $absen->absensi_type_masuk ?? $absen->created_at?->format('H:i:s') ?? '00:00:00';
                                                $jamStr = $absen->shift?->jam_start ?? '00:00:00';

                                                $formatAbs = strlen($jamAbs) === 5 ? 'H:i' : 'H:i:s';
                                                $formatStr = strlen($jamStr) === 5 ? 'H:i' : 'H:i:s';

                                                try {
                                                    $jAbs = \Carbon\Carbon::createFromFormat($formatAbs, $jamAbs);
                                                    $jJad = \Carbon\Carbon::createFromFormat($formatStr, $jamStr);
                                                    $jDiff = $jAbs->diff($jJad);

                                                    if ($jDiff->h > 0) {
                                                        $durasiTerlambat .= $jDiff->format('%h Jam ');
                                                    }
                                                    if ($jDiff->i > 0) {
                                                        $durasiTerlambat .= $jDiff->format('%i Menit ');
                                                    }
                                                    if ($jDiff->s > 0) {
                                                        $durasiTerlambat .= $jDiff->format('%s Detik');
                                                    }

                                                    $durasiTerlambat = trim($durasiTerlambat);
                                                    if ($durasiTerlambat === '') {
                                                        $durasiTerlambat = '0 Detik';
                                                    }
                                                } catch (\Throwable $th) {
                                                    $durasiTerlambat = '';
                                                }
                                            }
                                        @endphp

                                        @if($isTerlambat)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-red-200 bg-red-600 rounded-md">
                                                Terlambat
                                            </span>
                                            @if($durasiTerlambat !== '')
                                                <p class="mt-1 text-xs text-red-300">{{ $durasiTerlambat }}</p>
                                            @endif
                                        @else
                                            {{ $absen->keterangan ?? 'Hadir' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('mitra-lihatMap', $absen->id) }}" class="font-semibold text-blue-400 hover:underline">Lihat Lokasi</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentSignature = @json($dataSignature ?? 'empty');
            const intervalMs = 10 * 60 * 1000;

            setInterval(async function () {
                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('check_updates', '1');

                    const response = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        return;
                    }

                    const result = await response.json();
                    if (result.signature && result.signature !== currentSignature) {
                        window.location.reload();
                    }
                } catch (error) {
                    // Silent fail: auto-check akan mencoba lagi di interval berikutnya.
                }
            }, intervalMs);
        });
    </script>
</x-mitra-layout>
