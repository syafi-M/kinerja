<style>
    .profile-image-circle {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
      border-radius: 50%;
    }
</style>
<nav class="mx-5 sm:mx-5 mb-5 sm:mb-5 pt-5 capitalize">
	<div class="flex pl-2 w-full h-auto bg-slate-500 shadow-md rounded-md justify-between capitalize">
		<a href="{{ route('profile.index')}}" class="" style="width: {{ Auth::user()->role_id == 2 ? '70%' : '85%' }};">
		<div class="flex items-center gap-1">
			<div
                class="mx-2 my-2 overflow-hidden flex items-center justify-center bg-slate-300 rounded-full shadow-md shadow-slate-600 hover:shadow-none transition-all duration-200 ease-in-out"
                style="width: 2.5rem; height: 2.5rem; padding: 0.25rem;">
                
                @if(Route::has('login'))
                    @auth
                        @php
                            $userImage = Auth::user()->image;
                            
                            if (Auth::user()->image == 'no-image.jpg') {
                                $imgSrc = URL::asset('/logo/person.png');
                            }elseif(Storage::disk('public')->exists('images/' . Auth::user()->image)) {
                                $imgSrc = asset('storage/images/' . Auth::user()->image);
                            }elseif(Storage::disk('public')->exists('user/' . Auth::user()->image)) {
                                $imgSrc = asset('storage/user/' . Auth::user()->image);
                            }else {
                                $imgSrc = URL::asset('/logo/person.png');
                            }
                        @endphp
                        <img 
                            class="profile-image-circle" 
                            src="{{ $imgSrc }}" 
                            alt="profile-logo"
                        />
                    @endauth
                @endif
            </div>


			@if (Route::has('login'))
				@auth
					<div class="flex justify-evenly flex-row gap-1" style="max-width: 65%;" >
					    <div>
    						<p class="font-semibold text-white text-sm line-clamp-1 break-words">{{ Auth::user()->nama_lengkap }}</p>
					    </div>
					</div>
			@else
					<div>
					</div>
				@endauth
			@endif
		</div>
        </a>
		@if (Route::has('login'))
			@auth
			    <div class="flex md:hidden overflow-hidden mx-5 items-center" style="width: 28%;">
			        <form action="{{ route('slip-gaji.index') }}" method="get">
			            <input type="hidden" name="bulan" value="{{ Carbon\Carbon::now()->subMonth()->format('Y-m') }}"/>
			            <button class="btn btn-sm btn-warning">
			                <span class="flex items-center gap-1">
			                    <i class="ri-bank-card-line"></i>
			                    <p class="overflow-hidden">Slip</p>
			                </span>
			            </button>
			        </form>
			    </div>
				<div class="md:flex gap-3 mr-7 hidden overflow-hidden">
					<x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.index')">
						{{ __('Dashboard') }}
					</x-nav-link>

                @if(Auth::user()->divisi->jabatan->code_jabatan == "SPV-A")
					@if (Auth::user()->role_id == 2)
						<x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" class="hidden">
							{{ __('Admin Tool') }}
						</x-nav-link>
					@else
					@endif
				@else
					@if (Auth::user()->role_id == 2)
						<x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')">
							{{ __('Admin Tool') }}
						</x-nav-link>
					@else
					@endif
				@endif
					<form action="{{ route('logout') }}" method="post">
						@csrf
						@method('POST')
						<button type="submit"
							class="inline-flex overflow-hidden items-center w-auto mt-4 h-3/6 px-2 font-semibold rounded-md text-slate-700 bg-yellow-300 hover:bg-yellow-400 hover:text-white hover:shadow-none shadow-md transition all ease-in-out .2s" active>Logout</button>
					</form>
				@else
					<x-nav-link class="mr-5 px-5 py-1" :href="route('login')" :active="true">
						{{ __('Login') }}
					</x-nav-link>

				</div>
			@endauth
		@endif
	</div>
	</div>
</nav>
