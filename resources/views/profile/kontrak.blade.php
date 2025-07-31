<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <x-main-div>
        <p class="text-center text-2xl font-bold pt-5 uppercase grid justify-center items-center">Form Kontrak</p>
		<div style="overflow: auto; margin: 0.5rem; padding: 0.5rem; gap: 0.5rem;" class="bg-slate-100 rounded-md shadow flex flex-col justify-center items-center">
		    <a href="{{ route('form-kontrak-preview', ['id' => $kontrak?->id]) }}" target="_blank" onclick="window.open(this.href, '_blank'); window.location.reload(); return false; {{ session()->put('seen_kontrak', [true, Carbon\Carbon::now()->format('Y-m-d')]) }};" class="btn btn-info">LIHAT KONTRAK</a>
		    <div style="margin-top: 4px; overflow: hidden;" class="container relative">
		        @if(session()->has('seen_kontrak') &&
                    is_array(session('seen_kontrak')) &&
                    session('seen_kontrak')[0] != true &&
                    session('seen_kontrak')[1] != \Carbon\Carbon::now()->format('Y-m-d'))
		        <div style="position: absolute; width: 100%; height: 100%; background: rgba(211, 211, 211, 0.5); overflow: hidden;" class="flex justify-center items-center">
		            <p style="rotate: 45deg; text-align: center; overflow: hidden; color: #2e2e2e; font-weight: 600; font-size: 12pt;">!!BACA KONTRAK TERLEBIH DAHULU!!</p>
		        </div>
		        @endif
                <h2 class="text-center font-medium">TTD Dibawah</h2>
                <div class="flex justify-center items-center">
                    <canvas id="signature-pad" width="400" height="300" style="border:3px solid #ccc; border-radius: 8px; width: 100%; height: auto; max-width: 400px; max-height: 300px;"></canvas>
                </div>
                <br>
                <div class="flex justify-between items-center overflow-hidden">
                    <button id="clear" class="btn btn-sm btn-warning uppercase">Reset</button>
                    <form id="signature-form" method="POST" action="{{ route('form-kontrak-update', $kontrak?->id) }}" class="overflow-hidden">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="signature_svg" id="signature-svg">
                        <button type="submit" class="btn btn-sm btn-success overflow-hidden uppercase">Simpan & Kirim</button>
                    </form>
                </div>
            </div>
		</div>
		<script>
            const canvas = document.getElementById('signature-pad');
        
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }
        
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas(); // Initial resize
        
            const signaturePad = new SignaturePad(canvas, {
                penColor: "black",
                backgroundColor: "white"
            });
        
            document.getElementById('clear').addEventListener('click', () => {
                signaturePad.clear();
            });
        
            document.getElementById('signature-form').addEventListener('submit', function (e) {
                if (signaturePad.isEmpty()) {
                    alert("Harap berikan tanda tangan terlebih dahulu.");
                    e.preventDefault();
                } else {
                    const svgData = signaturePad.toDataURL('image/svg+xml');
                    document.getElementById('signature-svg').value = svgData;
                }
            });
        </script>
    </x-main-div>
</x-app-layout>