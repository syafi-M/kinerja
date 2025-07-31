<x-app-layout>
    <x-main-div>
        <style>
            /* Remove number input arrows */
            input[type=number]::-webkit-outer-spin-button,
            input[type=number]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            
            input[type=number] {
                -moz-appearance: textfield;
            }
        </style>
        <p class="text-center text-2xl font-bold pt-5 uppercase grid justify-center items-center">Form Pengajuan Kontrak</p>
		<div style="overflow: auto; margin: 1rem; padding: 0.5rem; gap: 0.5rem;" class="bg-slate-100 rounded-md shadow">
            <form id="signature-form" method="POST" action="{{ route('form-kontrak-kirimPengajuan') }}" class="overflow-hidden">
                @csrf
                @method('POST')
                <div class="overflow-hidden">
                    <div class="flex flex-col gap-1">
                        <label class="label label-text">Tempat Lahir</label>
                        <input type="text" name="tempat_lhr" placeholder="Tempat lahir" class="input input-sm input-bordered" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="label label-text">Tanggal Lahir</label>
                        <input type="date" name="tgl_lhr" max="{{ Carbon\Carbon::now()->subYears(17)->format('Y-m-d') }}" class="input input-sm input-bordered"/>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="label label-text">Alamat Sekarang</label>
                        <input type="text" name="alamat_pk_kda" placeholder="Alamat tempat tinggal sekarang" class="input input-sm input-bordered"/>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="label label-text">No. NIK / KTP</label>
                        <input type="number" name="nik" placeholder="NIK" class="input input-sm input-bordered"/>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="label label-text">No. KK</label>
                        <input type="number" name="no_kk" placeholder="Nomor KK" class="input input-sm input-bordered"/>
                    </div>
                    <div>
                        <input type="hidden" name="name" value="{{ Auth::user()->nama_lengkap }}" />
                        <input type="hidden" name="client_id" value="{{ Auth::user()->kerjasama->client_id }}" />
                    </div>
                    <div class="flex items-center justify-between mx-5 mt-5">
                        <a href="{{ route('profile.index') }}" class="overflow-hidden btn btn-sm btn-warning">Kembali</a>
                        <button type="submit" class="overflow-hidden btn btn-sm btn-success">Kirim</button>
                    </div>    
                </div>
            </form>
		</div>
		<script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('input[type=number]').forEach(function (el) {
                    el.addEventListener('wheel', function (e) {
                        e.preventDefault();
                    });
                });
            });
        </script>
    </x-main-div>
</x-app-layout>