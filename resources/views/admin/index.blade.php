<x-app-layout>
	<x-main-div>
		<span class="p-2 mt-2 py-1 px-2 rounded-br-xl shadow-sm text-sm font-semibold text-white" style="background-color: #03a157;">Wellcomeback,
			Admin !!</span>
		<div class="flex flex-col gap-2 justify-center items-center pb-3">
		    <p class="text-center text-2xl uppercase font-bold bg-white p-5 rounded-md w-3/6 shadow-md">Dashboard Admin</p>
		    <span class="flex flex-col justify-center items-center">
    		    @if($izin)
    		        <p class="text-center text-sm uppercase font-bold bg-sky-400 p-3 rounded-md w-fit shadow-md"># Ada {{ $izin }} izin yang belum Di Acc</p>
    		    @endif
		    </span>
		</div>
		<div style="margin-bottom: 7rem" class=" flex items-start justify-center mx-5">
			<div class="grid grid-cols-4 grid-flow-row justify-items-center w-fit">
				{{-- menu user --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuUser">
						<button id="btnUser" class="btn btn-warning w-full mt-5"><i class="ri-folder-user-line text-lg"></i>Menu User</button>
					</div>
					<div id="user" class="hidden absolute">
						<div style="width: 17rem;">
							<span class=" flex flex-col justify-center gap-x-2 mx-2">
								<a href="{{ route('users.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-user-line text-lg"></i>Data
									User</a>
								<a href="{{ route('users.create') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-user-add-line text-lg"></i>Tambah
									User</a>
							</span>
						</div>
					</div>
				</div>
				{{-- end menu --}}
	
				{{-- menu devisi --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuDevisi">
						<button id="btnDevisi" class="btn btn-warning w-full mt-5"><i class="ri-group-2-line text-lg"></i> Menu Devisi</button>
					</div>
					<div id="devisi" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2">
								<a href="{{ route('devisi.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-team-line text-lg"></i> Data
									Devisi</a>
								<a href="{{ route('devisi.create') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-add-line text-lg"></i> Tambah
									Devisi</a>
							</span>
						</div>
					</div>
				</div>
				{{-- end menu --}}
	
				{{-- menu client --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuClient">
						<button id="btnClient" class="btn btn-warning w-full mt-5"><i class="ri-p2p-line text-lg"></i> Menu Client</button>
					</div>
					<div id="client" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2">
								<a href="{{ route('data-client.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-user-5-line text-lg"></i> Data
									Client</a>
								<a href="{{ route('data-client.create') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-add-line text-lg"></i> Tambah
									Client</a>
							</span>
						</div>
					</div>
				</div>
				{{-- end menu --}}
	
				{{-- menu shift --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuShift">
						<button id="btnShift" class="btn btn-warning w-full mt-5"><i class="ri-timer-line text-lg"></i> Menu Shift</button>
					</div>
					<div id="shift" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2">
								<a href="{{ route('shift.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-timer-flash-line text-lg"></i> Data
									Shift</a>
								<a href="{{ route('shift.create') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-add-line text-lg"></i> Tambah
									Shift</a>
							</span>
						</div>
					</div>
				</div>
				{{-- end menu --}}
	
				{{-- Menu Ruangan --}}
				<div style="width: 17rem;">
					<div>
						<div class="flex justify-center px-2 btnRuangan">
							<a href="{{ route('ruangan.index') }}" class="btn btn-warning w-full mt-5"><i class="ri-door-open-line text-lg"></i> Data Ruangan</a>
						</div>
					</div>
				</div>
				{{-- End Menu Ruangan --}}
	
				{{-- Menu Point --}}
				<div style="width: 17rem;">
					<div>
						<div class="flex justify-center px-2 btnPoint">
							<a href="{{ route('point.index') }}" class="btn btn-warning w-full mt-5"><i class="ri-coins-line text-lg font-normal"></i> Data Point</a>
						</div>
					</div>
				</div>
				{{-- End Menu Ruangan --}}
	
				{{-- Menu area --}}
				<div style="width: 17rem;">
					<div>
						<div class="flex justify-center px-2 btnArea">
							<a href="{{ route('area.index') }}" class="btn btn-warning w-full mt-5"><i class="ri-map-pin-2-line text-lg"></i> Data Area</a>
						</div>
					</div>
				</div>
				{{-- End Menu area --}}
	
				{{-- menu kerjasama --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuKerjasama">
						<button id="btnKerjasama" class="btn btn-warning w-full mt-5"><i class="ri-shake-hands-line text-lg"></i> Menu Kerjasama</button>
					</div>
					<div id="kerjasama" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px]">
								<a href="{{ route('kerjasamas.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-hand-coin-line text-lg"></i> Data
									Kerjasama</a>
								<a href="{{ route('kerjasamas.create') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-add-line text-lg"></i> Tambah
									Kerjasama</a>
							</span>
						</div>
					</div>
				</div>
				{{-- end menu --}}
	
				{{-- Menu Absen --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuAbsen">
						<button id="btnAbsen" class="btn btn-warning w-full mt-5"><i class="ri-calendar-todo-line text-lg"></i> Menu Absensi</button>
					</div>
					<div id="absen" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('admin.absen') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-list-check-3 text-lg"></i> Data
									Absensi</a>
								<a href="{{ route('data-izin.admin') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-shield-user-line text-lg"></i> Data
									Izin</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu Absen --}}
	
				{{-- Menu Perlengkapan --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuPerlengkapan">
						<button id="btnPerlengkapan" class="btn btn-warning w-full mt-5"><i class="ri-tools-line text-lg"></i> Menu Perlengkapan</button>
					</div>
					<div id="perlengkapan" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px]">
								<a href="{{ route('perlengkapan.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-hammer-line text-lg"></i> Data
									Perlengkapan</a>
								<a href="{{ route('perlengkapan.create') }}"
									class="btn-info hover:bg-sky-500  hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-add-line text-lg"></i> Tambah
									Perlengkapan</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
	
				{{-- Menu Lembur --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuLembur">
						<button id="btnLembur" class="btn btn-warning w-full mt-5"><i class="ri-time-line text-lg"></i> Menu Lembur</button>
					</div>
					<div id="lembur" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('lemburList') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-hourglass-2-line text-lg"></i> Data
									Lembur</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
	
				{{-- Menu Jabatan --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuJabatan">
						<button id="btnJabatan" class="btn btn-warning w-full mt-5"><i class="ri-medal-line text-lg"></i> Menu Jabatan</button>
					</div>
					<div id="jabatan" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('jabatan.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-award-line text-lg"></i> Data
									Jabatan</a>
								<a href="{{ route('jabatan.create') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-add-line text-lg"></i> Tambah
									Jabatan</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
	
				{{-- Menu lokasi --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuLokasi">
						<button id="btnLokasi" class="btn btn-warning w-full mt-5"><i class="ri-road-map-line text-lg"></i> Menu Lokasi</button>
					</div>
					<div id="lokasi" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('lokasi.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-pin-distance-line text-lg"></i> Data
									Lokasi</a>
								<a href="{{ route('lokasi.create') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-map-pin-add-line text-lg"></i> Tambah
									lokasi</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
				{{-- Menu Jadwal --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuJadwal">
						<button id="btnJadwal" class="btn btn-warning w-full mt-5"><i class="ri-calendar-event-line text-lg"></i> Menu Jadwal</button>
					</div>
					<div id="jadwal" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('admin-jadwal.index')}}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-calendar-2-line text-lg"></i> Data
									Jadwal User</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
				{{-- Menu Laporan --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuLaporan">
						<button id="btnLaporan" class="btn btn-warning w-full mt-5"><i class="ri-task-line text-lg"></i> Menu Laporan</button>
					</div>
					<div id="laporan" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('laporan.index')}}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-calendar-2-line text-lg"></i> Data
									Laporan</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
				{{-- Menu Cp --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuCP">
						<button id="btnCP" class="btn btn-warning w-full mt-5"><i class="ri-task-line text-lg"></i> Menu Check Point</button>
					</div>
					<div id="CP" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('admin.cp.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-calendar-2-line text-lg"></i> Data
									Check Point</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
				{{-- Menu Pekerjaan CP --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuPCP">
						<button id="btnPCP" class="btn btn-warning w-full mt-5 flex"><i class="ri-task-line text-lg"></i> Menu Pekerjaan CP</button>
					</div>
					<div id="PCP" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('pekerjaanCp.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-calendar-2-line text-lg"></i> Data Pekerjaan
									Check Point</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
				
			
				{{-- Menu news --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuNews">
						<button id="btnNews" class="btn btn-warning w-full mt-5 flex"><i class="ri-task-line text-lg"></i> Menu Berita</button>
					</div>
					<div id="News" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('news.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-calendar-2-line text-lg"></i> Data
									Berita</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
				{{-- Menu subarea --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuSubArea">
						<button id="btnSubArea" class="btn btn-warning w-full mt-5 flex"><i class="ri-task-line text-lg"></i> Menu Sub Area</button>
					</div>
					<div id="SubArea" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('subarea.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-calendar-2-line text-lg"></i> Data
									Sub Area</a>
							</span>
						</div>
					</div>
				</div>
				{{-- End Menu --}}
				
				{{-- Menu area final --}}
				 <div style="width: 17rem;">
					<div class="flex justify-center px-2 menuChecklist">
						<button id="btnChecklist" class="btn btn-warning w-full mt-5 flex"><i class="ri-task-line text-lg"></i> Menu Checklist</button>
					</div>
					<div id="Checklist" class="hidden absolute">
						<div style="width: 17rem;">
							<span class="flex flex-col justify-center gap-x-2 mx-2 pb-[148px] ">
								<a href="{{ route('admin-checklist.index') }}"
									class="btn-info hover:bg-sky-500 hover:text-white w-full shadow-md hover:shadow-none text-center uppercase font-semibold text-sm rounded-md px-4 py-2 mt-5 transition-all ease-linear .2s"><i class="ri-calendar-2-line text-lg"></i> Data
									Checklist</a>
							</span>
						</div>
					</div>
				</div> 
				{{-- End Menu --}}
				
				{{-- Menu QR CODE --}}
				<div style="width: 17rem;">
					<div class="flex justify-center px-2 menuQR">
						<a id="btnQR" class="btn btn-warning w-full mt-5 flex" href="{{ route('qrcode.index')}}"><i class="ri-qr-code-line text-lg"></i> DATA QR CODE</a>
					</div>
				</div>
				{{-- End Menu --}}
			</div>
		</div>

	</x-main-div>
		<x-footer-component/>
</x-app-layout>
