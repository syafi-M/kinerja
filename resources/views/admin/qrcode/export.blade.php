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
            margin-top: 10px;
        }

        .box {
            border: 1px solid #000;
            display: inline-block; /* Display boxes inline */
            position: relative;
            margin-right: 8px;
            margin-bottom: 10px;
            width: 20%;
            height: 30%;
        }
        
        .break-normal {
            word-break: normal;
        }
        
        .break-all {
            word-break: break-all;
            white-space: pre-line;
        }
        
        .keep-all {
            word-break: keep-all;
        }
        .page-break {
			page-break-before: always;
		}
        
    </style>
</head>

<main>
    
    <div class="container">
        <div class="flex-row">
    	    @forelse($qr as $index => $i)
                <div class="box text-center">
                    @php
                    
                        $path = 'storage/images/'. $i->qr_code;
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        
                    @endphp
                        <div style="display: absolute; background-color: #2e1065; top: 0; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
        	               <span style="text-align: center; color: white; font-size: 10px; top: 0; font-weight: bold;">SAC{{ Carbon\Carbon::now()->year }}</span>
    	                </div>
    	                
                        <img src="{{ $base64 }}" width="120px" style="padding-top: 10px;"/>
    	                <div style="word-wrap: break-word; " class="break-all">
    	                    <span style="font-size: 9px; word-wrap: break-all; font-weight: bold;" class="break-normal">
    	                        {{ $i->ruangan->nama_ruangan }}
    	                    </span>
    	                </div>
    	                <!--<div style=" background-color: blue; bottom: 0; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">-->
        	            <!--   <span style="text-align: center; color: white; font-size: 10px; bottom: 0;">PT. SAC PONOROGO</span>-->
    	                <!--</div>-->
                </div>
        	@empty
    			<td colspan="31" class="text-center">Kosong</td>
    		@endforelse
		</div>
    </div>
		                  
	
</main>
</body>

</html>
