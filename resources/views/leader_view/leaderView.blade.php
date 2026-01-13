<x-app-layout>
    <x-main-div>
        <div class="flex justify-start mx-4 mt-4 mb-2 w-full">
            <button onclick="history.back()" class="btn btn-error">Kembali</button>
        </div>
        @if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS")
        <div class="mx-10 my-5 flex flex-col gap-2">
            {{-- menu menu leader --}}
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden flex items-center"id="Luser">
				<a href="{{ route('lead_user') }}" class="btn btn-info w-full"><i
						class="ri-pass-pending-line text-xl"></i>Data Karyawan</a>
			</div>
			@if (Auth::user()->role_id == 2)
				<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Ljadwal">
					<a href="{{ route('admin-jadwal.index') }}" class="btn btn-info w-full"><i
							class="ri-calendar-check-line text-xl"></i>Jadwal User</a>
				</div>
			@else
				<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Ljadwal">
					<a href="{{ route('leader-jadwal.index') }}" class="btn btn-info w-full"><i
							class="ri-calendar-check-line text-xl"></i>Jadwal</a>
				</div>
			@endif
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Labsensi">
				<a href="{{ route('lead_absensi') }}" class="btn btn-info w-full"><i class="ri-todo-line text-xl"></i>Data
					Absensi</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Lizin">
				<a href="{{ route('lead_izin') }}" class="btn btn-info w-full"><i
						class="ri-shield-user-line text-xl"></i>Data Izin</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Llembur">
				<a href="{{ route('lead_lembur') }}" class="btn btn-info w-full"><i class="ri-time-line text-xl"></i>Data
					Lembur</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Llaporan">
				<a href="https://laporan-sac.sac-po.com" class="btn btn-info w-full"><i
						class="ri-image-add-line text-xl"></i>Data Laporan</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lrating">
				<a href="{{ route('leader-rating.index') }}" class="btn btn-info w-full"><i
						class="ri-sparkling-line text-xl"></i>Rating</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lchecklist">
				<a href="{{ route('leader-checklist.index') }}" class="btn btn-info w-full"><i
						class="ri-sparkling-line text-xl"></i>Checklist</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lchecklist">
				<a href="{{ route('leader-slip') }}" class="btn btn-info w-full"><i
						class="ri-wallet-3-line text-xl"></i>Slip Gaji Karyawan</a>
			</div>
        </div>
        @elseif(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR")
        <div class="mx-10 my-5 flex flex-col gap-2">
            {{-- menu menu danru --}}
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden flex items-center"id="Luser">
				<a href="{{ route('danru_user') }}" class="btn btn-info w-full"><i
						class="ri-pass-pending-line text-xl"></i>Data Karyawan</a>
			</div>
			@if (Auth::user()->role_id == 2)
				<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Ljadwal">
					<a href="{{ route('admin-jadwal.index') }}" class="btn btn-info w-full"><i
							class="ri-calendar-check-line text-xl"></i>Jadwal User</a>
				</div>
			@else
				<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Ljadwal">
					<a href="{{ route('danru-jadwal.index') }}" class="btn btn-info w-full"><i
							class="ri-calendar-check-line text-xl"></i>Jadwal</a>
				</div>
			@endif
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Labsensi">
				<a href="{{ route('danru_absensi') }}" class="btn btn-info w-full"><i class="ri-todo-line text-xl"></i>Data
					Absensi</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Lizin">
				<a href="{{ route('danru_izin') }}" class="btn btn-info w-full"><i
						class="ri-shield-user-line text-xl"></i>Data Izin</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Llembur">
				<a href="{{ route('danru_lembur') }}" class="btn btn-info w-full"><i class="ri-time-line text-xl"></i>Data
					Lembur</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Llaporan">
				<a href="https://laporan-sac.sac-po.com" class="btn btn-info w-full"><i
						class="ri-image-add-line text-xl"></i>Data Laporan</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lrating">
				<a href="{{ route('danru-rating.index') }}" class="btn btn-info w-full"><i
						class="ri-sparkling-line text-xl"></i>Rating</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lchecklist">
				<a href="{{ route('leader-checklist.index') }}" class="btn btn-info w-full"><i
						class="ri-sparkling-line text-xl"></i>Checklist</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lchecklist">
				<a href="{{ route('danru-slip') }}" class="btn btn-info w-full"><i
						class="ri-wallet-3-line text-xl"></i>Slip Gaji Karyawan</a>
			</div>
        </div>
        @elseif(Auth::user()?->jabatan->code_jabatan == "SPV-W")
        <div class="mx-10 my-5 flex flex-col gap-2">
            {{-- menu menu spv w --}}
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden flex items-center"id="Luser">
				<a href="{{ route('spvw_user') }}" class="btn btn-info w-full"><i
						class="ri-pass-pending-line text-xl"></i>Data Karyawan</a>
			</div>
			@if (Auth::user()->role_id == 2)
				<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Ljadwal">
					<a href="{{ route('admin-jadwal.index') }}" class="btn btn-info w-full"><i
							class="ri-calendar-check-line text-xl"></i>Jadwal User</a>
				</div>
			@else
				<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Ljadwal">
					<a href="{{ route('spvw-jadwal.index') }}" class="btn btn-info w-full"><i
							class="ri-calendar-check-line text-xl"></i>Jadwal</a>
				</div>
			@endif
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Labsensi">
				<a href="{{ route('spvw_absensi') }}" class="btn btn-info w-full"><i class="ri-todo-line text-xl"></i>Data
					Absensi</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Lizin">
				<a href="{{ route('spvw_izin') }}" class="btn btn-info w-full"><i
						class="ri-shield-user-line text-xl"></i>Data Izin</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Llembur">
				<a href="{{ route('spvw_lembur') }}" class="btn btn-info w-full"><i class="ri-time-line text-xl"></i>Data
					Lembur</a>
			</div>
			<div class=" w-full space-y-4  sm:px-16 overflow-hidden" id="Llaporan">
				<a href="https://laporan-sac.sac-po.com" class="btn btn-info w-full"><i
						class="ri-image-add-line text-xl"></i>Data Laporan</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lrating">
				<a href="{{ route('spvw-rating.index') }}" class="btn btn-info w-full"><i
						class="ri-sparkling-line text-xl"></i>Rating</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lchecklist">
				<a href="{{ route('spvw-checklist.index') }}" class="btn btn-info w-full"><i
						class="ri-sparkling-line text-xl"></i>Checklist</a>
			</div>
			<div class="w-full space-y-4  sm:px-16 overflow-hidden" id="Lchecklist">
				<a href="{{ route('spvw-slip') }}" class="btn btn-info w-full"><i
						class="ri-wallet-3-line text-xl"></i>Slip Gaji Karyawan</a>
			</div>
        </div>
        @endif
    </x-main-div>
</x-app-layout>
