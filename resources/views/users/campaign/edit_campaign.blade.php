<form id="formAddCompaign" method="POST" action="{{route('users.update-campaign')}}" enctype="multipart/form-data">
			@csrf
			
			<input type="hidden" name="offer_id" value="{{$sdt->pk_int_scratch_offers_id}}">
			<input type="hidden" name="offer_img" value="{{$sdt->vchr_scratch_offers_image}}">
			<input type="hidden" name="mobile_img" value="{{$sdt->mobile_image}}">
			
			<div class="mb-2 row">
			    <div class="col-lg-11 col-xl-11 col-xxl-11">
				<label for="example-text-input" class="col-form-label">Campaign Name</label>
				<input class="form-control" type="text" name="offer_name_edit" id="offer_name_edit" value="{{$sdt->vchr_scratch_offers_name}}" required>
				</div>
			</div>
			
			<div class="mb-2 row">
				<div class="col-lg-8 col-xl-8 col-xxl-8">
					<label for="example-text-input" class="col-form-label">Web Banner (<span class="text-light-blue">Max:500kb</span>)</label>
					<input class="form-control" type="file" name="offer_image_edit" id="offer_image_edit" >
					</div>
				<div class="col-lg-3 col-xl-3 col-xxl-3">
				<img id="img_offer_output_edit" src="{{ \App\Facades\FileUpload::viewFile($sdt->vchr_scratch_offers_image,'local')}}" style="width:75px;height:70px;">
				</div>
			</div>
				
			<div class="mb-2 row">		
				<div class="col-lg-8 col-xl-8 col-xxl-8">
					<label for="example-text-input" class="col-form-label">Mobile Banner (<span class="text-light-blue">Max:500kb</span>)</label>
					<input class="form-control" type="file" name="mobile_image_edit" id="mobile_image_edit" >
				</div>
				<div class="col-lg-3 col-xl-3 col-xxl-3">
				<img id="img_mobile_output_edit" src="{{ \App\Facades\FileUpload::viewFile($sdt->mobile_image,'local')}}" value="{{$sdt->offer_name}}" style="width:75px;height:70px;">
				</div>
			</div>
			<div class=" mb-2 row">				
				<div class="col-lg-6 col-xl-6 col-xxl-6">
						<label for="example-text-input" class="col-form-label">Campaign Type</label>
						<select class="form-select" name="offer_type_edit" id="offer_type_edit" required>
							<option selected="">select</option>
							@foreach($type as $row)
							<option value="{{$row->id}}" @if($row->id==$sdt->type_id){{__('selected')}}@endif>{{$row->type}}</option>
							@endforeach
						  </select>
				</div>
			</div>
			<hr>
			<div class="mb-2 row">	
				<div class="col-lg-12 col-xl-12 col-xxl-12 " style="text-align:right;">
				<button type="button" class="btn btn-danger btn-reset"  id="btn-reset-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
				<button class="btn btn-primary" type="submit">Update Campaign</button>
				</div>
			</div>
</form>

<script>

$(document).on('click','#btn-reset-offcanvas',function()
{
	$("#edit-campaign .offcanvas-body").html('');
});


offer_image_edit.onchange = evt => {
  const [file] = offer_image_edit.files

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
			$("#offer_image_edit").val('');
			$("#img_offer_output_edit").prop('src','');
		}
		else
		{
			if (file) {
				img_offer_output_edit.src = URL.createObjectURL(file)
			  }
		}  
	}
}

mobile_image_edit.onchange = evt => {
  const [file] = mobile_image_edit.files

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
			$("#mobile_image_edit").val('');
			$("#img_mobile_output_edit").prop('src','');
		}
		else
		{
			if (file) {
				img_mobile_output_edit.src = URL.createObjectURL(file)
			  }
		} 
	}		
}

</script>

