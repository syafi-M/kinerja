<x-app-layout>
    <x-main-div>
        <div class="flex justify-center flex-col items-center mx-10 my-10">
            <div class=" py-10 font-semibold w-fit bg-slate-100 w-full  rounded-md p-3 px-10 shadow-md">
                <p class="pb-5 text-center">Rating Saya, </p>
                @php
                    $ratM = $rating ? $rating->rate_mitra : 0;
                    $ratL = $rating ? $rating->rate_leader : 0;
                    $totalR = ($ratM + $ratL) / 2;
                @endphp
                <p class="font-semibold text-center mt-5">~ Rating ~</p>
                <span class="p-2 mb-5 flex justify-center items-center rounded-lg bg-slate-300">
                    @if($totalR <= 0)
                        <p class="text-center font-semibold">Belum Ada Rating</p>
                    @else
                    @endif
                    @for ($i = 1; $i <= $totalR; $i++)
						<input type="radio" class="mask mask-star-2 bg-orange-400" readonly disabled />
					@endfor
                </span>
                <div class="pt-5 flex justify-center">
                    <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
                </div>
            </div>
        </div>
    </x-main-div>
</x-app-layout>