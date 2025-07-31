<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        *,
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        
        /*width: 5.3cm;*/
        /*height: 8.6cm;*/

        .table-wrapper {
            text-align: center; /* Center align inline-block elements */
            font-size: 0; /* Remove whitespace gap between inline-block elements */
            white-space: nowrap; /* Prevents wrapping */
            position: relative;
        }

        .card-container {
            display: inline-block; /* Allows items to be in a row */
            width: 5.3cm;
            height: 8.6cm;
            position: relative;
            margin: 5px; /* Space between cards */
            vertical-align: top; /* Ensures all items align at the top */
        }

        #card {
            width: 100%;
            height: 100%;
            position: absolute;
            z-index: 2;
        }

        #prof {
            width: 2.95cm;
            height: 2.95cm;
            border-radius: 50%;
            object-fit: cover;
            position: absolute;
            top: 2.1cm; /* Adjusted for better placement */
            left: 1.16cm;
            z-index: 1;
        }
        
        .isiText {
            position: absolute;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
            margin-top: 10px;
            z-index: 3;
            bottom: 2.77cm;
            width: 100%;
        }
        
        .isiText2 {
            position: absolute;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
            margin-top: 10px;
            z-index: 3;
            bottom: 2.21cm;
            width: 100%;
        }
    </style>
</head>

<body>
    <main>
        <div class="title">
            @forelse ($data as $items)
                @forelse ($items as $user)
                    <p style="font-weight: 700; text-align: center; margin: 0 auto;">ID Card User Mitra {{ $user->kerjasama->client->name }}</p>
                    @break
                @empty
                @endforelse
                @break
            @empty
            @endforelse
        </div>

        <div class="table-wrapper">
            @forelse ($data as $items)
                @forelse ($items as $user)
                    <div class="card-container">
                        <img loading="lazy" src="{{ asset('logo/id_card.png') }}" id="card" alt="Id card">
                        @if(Storage::disk('public')->exists('images/' . $user->image))
                            <img loading="lazy" src="{{ asset('storage/images/' . $user->image) }}" id="prof" alt="Profile">
                        @else
                            <img loading="lazy" src="{{ URL::asset('logo/no-image.jpg') }}" id="prof" alt="Profile">
                        @endif
                        
                        <p class="isiText">{{ ucwords(strtolower($user->nama_lengkap)) }}</p>
                        <p class="isiText2">{{ ucwords(strtolower($user->divisi->jabatan->name_jabatan)) }}</p>
                    </div>
                @empty
                    <p class="text-center">Kosong</p>
                @endforelse
            @empty
                <p class="text-center">Kosong</p>
            @endforelse
        </div>
    </main>
</body>

</html>
