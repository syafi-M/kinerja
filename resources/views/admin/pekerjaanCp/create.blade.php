<x-app-layout>
    <x-main-div>
    <div class="bg-slate-500 p-4 mx-36 shadow-md rounded-md">
		<p class="text-center text-2xl uppercase font-bold">Tambah Pekerjaan</p>
		<form method="POST" action="{{ route('pekerjaanCp.store') }}" class="mx-[25%] my-10" id="form">
			@csrf
			<div class="bg-slate-100 px-10 py-5 rounded shadow">
				<!-- user -->
				<div class="flex flex-col">
					<label for="user_id" class="label">Pilih User</label>
                    <select name="user_id" id="user_id" class="select-bordered select">
                        <option disabled selected>~ Pilih User ~</option>
                        @forelse ($user as $us)
                            @if($us->kerjasama_id == 1)
                                <option value="{{ $us->id }}" data-divisi="{{ $us->devisi_id }}">{{ $us->nama_lengkap }}</option>
                            @endif
                        @empty
                            <option disabled>~ Data Kosong ~</option>
                        @endforelse
                    </select>
				</div>
				<!-- divisi -->
				<div class="flex flex-col">
					<label for="devisi_id" class="label">Pilih divisi</label>
                    <select name="devisi_id" id="devisi_id" class="select-bordered select">
                        <option disabled selected>~ Pilih Divisi ~</option>
                        @forelse ($divisi as $d)
                            <option value="{{ $d->id }}">{{ $d->jabatan->name_jabatan }}</option>
                        @empty
                            <option disabled>~ Data Kosong ~</option>
                        @endforelse
                    </select>
				</div>
				<!-- kerja bagus -->
				<div class="flex flex-col">
					<label for="kerjasama_id" class="label">Pilih Kerjasama</label>
                    <select name="kerjasama_id" id="kerjasama_id" class="select-bordered select">
                        <option disabled selected>~ Pilih Kerjasama ~</option>
                        @forelse ($kerjasama as $ker)
                            <option value="{{ $ker->id }}" {{ Auth::user()->kerjasama_id == $ker->id ? "selected" : "" }}>{{ $ker->client->name }}</option>
                        @empty
                            <option disabled>~ Data Kosong ~</option>
                        @endforelse
                    </select>
				</div>
				<!-- type check -->
				<div class="flex flex-col">
					<label for="type_check" class="label">Pilih Jenis Pekerjaan</label>
                    <select name="type_check" id="type_check" class="select-bordered select">
                        <option disabled selected>~ Pilih Jenis Pekerjaan ~</option>
                        <option value="harian" >harian</option>
                        <option value="mingguan">migguan</option>
                        <option value="bulanan">bulanan</option>
                        <option value="isidental">isidental</option>
                    </select>
				</div>
			    <!-- nama -->
				<div class="flex flex-col">
					<label for="name" class="label">Nama Pekerjaan</label>
                    <input type="text" name="name" id="name" class="input input-bordered" placeholder="Nama pekerjaan...">
				</div>

				<div class="flex gap-2 my-5 justify-end">
					<button><a href="{{ route('pekerjaanCp.index') }}" class="btn btn-error">Back</a></button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</div>
		</form>
	</div>
	
	<script>
        $(document).ready(function() {
            $('#user_id').on('change', function() {
                var selectedDivisi = $(this).find(':selected').data('divisi'); // Get the data-divisi attribute value
    
                // Set the selected value in the 'devisi_id' dropdown
                $('#devisi_id').val(selectedDivisi);
            });
        });
    </script>
</x-main-div>
</x-app-layout>