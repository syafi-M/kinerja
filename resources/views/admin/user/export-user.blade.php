<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<head>
	<style>
		*,
		body {
			font-family: Arial, Helvetica, sans-serif;
		}

		table {
			border-collapse: collapse;
			width: 100%;
		}

		 table,
		 th,
		td {
			border: 1px solid black;
		}

		td {
			text-align: center;
		}

		th {
			background-color: rgb(19, 110, 170);
			color: white;
		}

		tr:nth-child(even) {
			background-color: #cbd5e1;
		}
	</style>
</head>

<main>
	<div class="title">
		<span class="sub-title" style="vertical-align: 20px; font-weight: bolder; font-size: 25px;">PT. Surya
			Amanah Cendekia</span>
	</div>
	<div class="table-wrapper">
		<table class="border">
			<thead>
				<tr>
				    <th>#</th>
				    <th>User ID</th>
				    <th>Nama Lengkap</th>
					<th>Nama</th>
					<th>Password</th>
				</tr>
			</thead>
			<tbody>
				@php
                    $n = 1;
				@endphp
				@forelse ($data as $items)
        				@forelse ($items as $user)
    					    <tr>
    					        <td>{{ $n++}}</td>
    					        <td>{{ $user->id }}</td>
    					        <td>{{ $user->nama_lengkap}}</td>
    					        <td>{{ $user->name}}</td>
    					        <td><span style="background-color: red; padding-x: 2px; border-radius: 5px;">{{$user->kerjasama_id == 1 ? '12345678' : '123456'}}</span></td>
    					    </tr>
        				@empty
        					<td colspan="31" class="text-center">Kosong</td>
        				@endforelse
				@empty
					<td colspan="31" class="text-center">Kosong</td>
				@endforelse
			</tbody>
		</table>

	</div>
	
</main>
</body>

</html>
