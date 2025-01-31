<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GL-Scratch : Qr-Codes</title>
    </head>
<style>
@page { margin: 20px; }
body { margin: 10px; }
</style>

<body>

	<div class="mb-2 row ps-5">
	<table style="width:100%">
	<tr>
	@if($qrimages)
		@php 
		 $x=1;
		@endphp

		@foreach($qrimages as $row)
		 <td align="center">
			<img src="uploads/{{$row->qrcode_file}}" style="width:200px;height:200px;">
		</td>
		@if($x==3)
			</tr>
			<tr><td colspan="3" style="height:30px;"></td></tr><tr>
			@php
			$x=1;		
			@endphp
		@else
			@php
			$x++;		
			@endphp
		@endif
	  @endforeach
	  </tr></table>
	@endif
		</div>
	</div>
		
</body>
</html>
