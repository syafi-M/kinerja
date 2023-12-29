<x-app-layout>
    <x-main-div>
        <div>
            <p class="text-center text-2xl font-bold py-5 uppercase">Data Checklist</p>
            <div class="mx-10">
                <x-search/>
            </div>
            @if($startDate && $endDate)
                <div class="flex justify-between my-5 mx-10">
                    <a href="{{ Auth::user()->role_id == 2 ? route('admin.index') : route('leaderView') }}" class="btn btn-error">Back</a>
                    <a href="{{  Auth::user()->role_id == 2 ? route('admin-checklist.create') : route('leader-checklist.create') }}" class="btn btn-warning hover:bg-yellow-600 border-none transition-all ease-in-out .2s">+ Checklist</a>
                </div>
                <div class="flex items-center justify-center flex-col mx-5 pb-10 gap-4">
                    @php
                        $str = Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                        $end = Carbon\Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
                        $str1 = $str->isoFormat('dddd, MMM YYYY');
                        $end1 = $end->isoFormat('dddd, MMM YYYY');
                    @endphp
                    @for($date = $str->copy(); $date->lte($end); $date->addDay())
                        @php
                            $d = $date->isoFormat('dddd, D MMMM Y');
                            $renderedC = [];
                            $filteredChecklist = $checklist->where('created_at', '>=', $date)->where('created_at', '<', $date->copy()->addDay());
                        @endphp
                        @if($filteredChecklist->count() > 0)
                            <table class="table table-xs w-full bg-slate-50" id="searchTable">
                                <thead>
                                    <tr>
                                        <th colspan="4" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem; font-size: 1rem;" class="bg-slate-300 py-3 text-center font-semibold">{{ $d }}</th>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-300">#</th>
                                        <th class="bg-slate-300">Area - Subarea</th>
                                        <th class="bg-slate-300">Tingkat Bersih</th>
                                        <th class="bg-slate-300 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($filteredChecklist as $c)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $c->area}} - {{ $c->sub_area }}</td>
                                            <td>{{ $c->tingkat_bersih }}</td>
                                            <td>
                                                <form action="{{ route('admin-checklist.destroy', $c->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="overflow-hidden">
                                                        <button class="text-red-400 hover:text-red-500 text-xl transition-all ease-linear .2s"><i class="ri-delete-bin-5-line"></i></button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                        
                    @endfor
                </div>
                <div class="flex justify-start mx-10">
                    @foreach($checklistApproved as $c)
                        @php $hasMatch = false; @endphp
                        @forelse($final as $fin)
                            @if($c->id == $fin->checklist_id)
                                @php $hasMatch = true; @endphp
                                <img srcset="{{ asset('storage/'. Auth::user()->signature->getSignatureImagePath()) }}"/>
                            @endif
                            @break
                        @empty
                            @if (!$hasMatch && $filteredChecklist->count() > 0)
                                @if (!Auth::user()->hasBeenSigned())
                                <span class="w-full">
                                    <form id="signForm" action="{{ Auth::user()->getSignatureRoute() }}" method="POST" id="uploadForm" class="flex justify-center items-center">
                                        @csrf
                                        <div style="text-align: center">
                                            <x-creagia-signature-pad
                                                border-color="#AFAFAF"
                                                pad-classes="rounded-xl border-2"
                                                button-classes="btn btn-primary mt-4"
                                                clear-name="Hapus"
                                                submit-name="Approve"
                                                :disabled-without-signature="true"
                                            />
                                        </div>
                                    </form>
                                </span>
                                    @break
                                @else
                                    <form id="hiddenForm" action="{{ route('admin-checklist.index')}}" method="GET" class="approvalForm w-full my-10">
                                        @csrf
                                        @foreach ($checklistApproved as $item)
                                            <input name="allow" value="true" class="hidden"/>
                                            <input type="text" name="checklist_id[]" value="{{ $item->id }}" class="hidden">
                                            <input name="signature" id="approve" value="{{ Auth::user()->signature->getSignatureImagePath()}}" class="hidden"/>
                                        @endforeach
                                        <div class="flex justify-center">
                                            <button type="submit" class="btn btn-primary">Approve</button>
                                        </div>
                                    </form>
                                    @break
                                @endif
                            @endif
                        @endforelse
                    @endforeach
                </div>
            </div>
        @else
            <form action="{{ Auth::user()->role_id == 2 ? route('admin-checklist.index') : route('leader-checklist.index') }}" method="GET" class="my-10">
                @csrf
                <span class="flex justify-center items-center">
                    <span class="w-fit bg-slate-50 rounded-lg p-10">
                        <p class="text-center font-bold text-lg mb-5">Tanggal Mulai - Akhir</p>
                        <div class="flex mr-2">
                            <div class="mr-2">
                                <input type="date" name="start_date" id="str1" placeholder="Tanggal Mulai" class="text-sm block px-3 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
                            </div>
                            <div class="ml-2">
                                <input type="date" name="end_date" id="end1" class="text-sm block px-3 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
                            </div>
                        </div>
                        <span class="flex justify-center">
                            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                        </span>
                    </span
                    </span>
                </span>
            </form>
        @endif
    </div>
    <script src="{{ asset('vendor/sign-pad/sign-pad.min.js') }}"></script>
</x-main-div>
</x-app-layout>