	
<div class="block md:hidden mx-5 sm:mx-auto">
	<div class="menu menu-horizontal px-3 flex items-center bg-slate-200 mb-1 rounded-box">
		<div class="flex text-[10px]">
		    <ul class="flex">
			<li class="overflow-hidden">
				<a href="{{ route('profile.index') }}" class="flex flex-col gap-0 -my-2 " style="margin-top: -0.5rem; margin-bottom: -0.5rem">
					<i class="ri-account-circle-line text-xl text-blue-500 ">
					</i>
					<span class="font-semibold text-slate-700 ">Profile</span>

				</a>
			</li>
			<li class="overflow-hidden">
				<a href="{{ route('dashboard.index') }}" class="flex flex-col gap-0" style="margin-top: -0.5rem; margin-bottom: -0.5rem">
					<i class="ri-home-2-line  text-xl"></i>
					<span class="font-semibold text-slate-700">Home</span>
				</a>
			</li>
				<li class="overflow-hidden">
			<form action="{{ route('logout') }}" method="post">
					@csrf
					@method('POST')
					<button type="submit" class="flex justify-center items-center flex-col gap-0" style="margin-top: -0.5rem; margin-bottom: -0.5rem">
						<i class="ri-shut-down-line text-xl text-red-500"></i>
						<span class="font-semibold text-slate-700">Log out</span>
					</button>
			</form>
				</li>
			@if(Route::has('login') && request()->routeIs('dashboard.index'))
			@auth
    			@if (Auth::user()->divisi->jabatan->code_jabatan != 'MITRA' && Auth::user()->divisi->jabatan->code_jabatan != 'LEADER' && Auth::user()->name != 'DIREKSI')
    				<li class="overflow-hidden">
    				    @if(Auth::user()->kerjasama_id == 1)
    				        @if(Carbon\Carbon::now()->format('N') == 6 || Carbon\Carbon::now()->format('N') == 7)
    				            @if(Auth::user()->devisi_id == 26)
    					        <a id="aAbsen2" href="{{ route('absensi.index') }}" class="flex flex-col gap-0" style="margin-top: -0.5rem; margin-bottom: -0.5rem">
            						<i class="ri-file-edit-fill text-xl text-green-500"></i>
            						<span class="font-semibold text-slate-700">Kehadiran</span>
            					</a>
    				            @else
    					        <a id="aAbsen2" href="#" class="flex flex-col gap-0" style="margin-top: -0.5rem; margin-bottom: -0.5rem">
            						<i class="ri-file-edit-fill text-xl text-green-500"></i>
            						<span class="font-semibold text-slate-700">Tidak Ada Jadwal</span>
            					</a>
    				            @endif
    				        @else
    					        <a id="aAbsen2" href="{{ route('absensi.index') }}" class="flex flex-col gap-0" style="margin-top: -0.5rem; margin-bottom: -0.5rem">
            						<i class="ri-file-edit-fill text-xl text-green-500"></i>
            						<span class="font-semibold text-slate-700">Kehadiran</span>
            					</a>
    				        @endif
    						@else
    						    <a id="aAbsen2" href="{{ route('absensi.index') }}" class="flex flex-col gap-0" style="margin-top: -0.5rem; margin-bottom: -0.5rem">
            						<i class="ri-file-edit-fill text-xl text-green-500"></i>
            						<span class="font-semibold text-slate-700">Kehadiran</span>
            					</a>
					    @endif
    					<!--<a id="aAbsen2" href="{{ route('absensi.index') }}" class="flex flex-col gap-0" style="margin-top: -0.5rem; margin-bottom: -0.5rem">-->
    					<!--	<i class="ri-file-edit-fill text-xl text-green-500"></i>-->
    					<!--	<span class="font-semibold text-slate-700">Kehadiran</span>-->
    					<!--</a>-->
    				</li>
    			@endif
    		@endauth
    		@endif
    		</ul>
		</div>
	</div>
</div>
