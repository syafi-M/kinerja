<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<head>
<style>
        body {
            font-family: Arial, sans-serif;
        }
        
        .text-center {
            text-align: center;
        }

        .container {
            margin: 20px;
        }

        .flex-row {
            display: block; /* Force block layout */
            margin-bottom: 10px;
        }

        .box {
            border: 1px solid #000;
            display: inline-block; /* Display boxes inline */
            margin-right: 10px;
            padding: 10px;
        }
    </style>
</head>

<main>
    
    <div class="container">
        <div class="flex-row">
    	    @forelse($qr as $i)
                <div class="box text-center">
                    @php
                    
                        $path = 'storage/images/'. $i->qr_code;
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        
                    @endphp
                    <img src="{{ $base64 }}" />
    	                <div>
    	                    {{ $i->kerjasama->client->name }}
    	                </div>
    	                    {{ $i->ruangan->nama_ruangan }}
                </div>
        	@empty
    			<td colspan="31" class="text-center">Kosong</td>
    		@endforelse
		</div>
    </div>
		                  
	
</main>
</body>

</html>
