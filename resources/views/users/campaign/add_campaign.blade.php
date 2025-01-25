	<form id="formAddCompaign" method="POST" action="{{route('users.save-campaign')}}" enctype="multipart/form-data">
			@csrf

			<div class="mb-3 mt-2 row">
			    <div class="col-lg-11 col-xl-11 col-xxl-11">
				<label for="example-text-input" class="col-form-label">Campaign Name</label>
				<input class="form-control" type="text" name="offer_name" id="offer_name"  required>
				</div>
			</div>
			
			<div class="mb-3 mt-2 row">
				<div class="col-lg-8 col-xl-8 col-xxl-8">
					<label for="example-text-input" class="col-form-label">Banner (<span class="text-light-blue"><small>Max:500mb, Size 450x450</small></span>)</label>
					<input class="form-control" type="file" name="offer_image" id="offer_image" required>
					</div>
				<div class="col-lg-3 col-xl-3 col-xxl-3">
				<img id="img_offer_output" src="{{url('assets/images/no-image.png')}}" style="width:75px;height:70px;">
				</div>
			</div>


			<div class=" mb-3 mt-2 row">				
				<div class="col-lg-6 col-xl-6 col-xxl-6">
						<label for="example-text-input" class="col-form-label">Campaign Type</label>
						<select class="form-select" name="offer_type" id="offer_type" required>
							<option selected="">select</option>
							@foreach($type as $row)
							<option value="{{$row->id}}" >{{$row->type}}</option>
							@endforeach
						  </select>
				</div>
			</div>
			
			<div class=" mb-3 mt-2 row">	
			<div class="col-lg-6 col-xl-6 col-xxl-6">
					<label for="example-text-input" class="col-form-label">Campaign End Date</label>
					<input class="form-control" type="date" name="campaign_end_date" id="campaign_end_date" required>
			</div>
			</div>
							
			<hr>
			<div class="mb-3 row">	
				<div class="col-lg-12 col-xl-12 col-xxl-12 " style="text-align:right;">
				<button type="button" class="btn btn-danger btn-reset"  id="btn-reset-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
				<button class="btn btn-primary" type="submit">Save Campaign</button>
				</div>
			</div>
</form>


<script>

$(document).on('click','#btn-reset-offcanvas',function()
{
	$("#edit-campaign .offcanvas-body").html('');
});


offer_image.onchange = evt => {
  const [file] = offer_image.files

	var size=file.size;
	if(size>524288)
	{
		alert("Image size too large. Maximum 500Kb only");
		$(this).val('');
	}
	else
	{

        var allowedExtensions="";
	    allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
	    var filePath = file.name;
		console.log(file);
	
		if (!allowedExtensions.exec(filePath)) { 
			alert('Invalid file type, Try again.'); 
			$("#offer_image").val('');
			$("#img_offer_output").prop('src','');
		}
		else
		{
			if (file) {
				img_offer_output.src = URL.createObjectURL(file)
			  }
		}  
	}
}


</script>

