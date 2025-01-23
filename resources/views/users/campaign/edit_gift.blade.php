@php
	$cust_cnt=$gft->int_scratch_offers_count-$gft->int_scratch_offers_balance;
@endphp

<form  method="post" action="{{url('users/update-gift')}}" enctype="multipart/form-data">
	@csrf
		
		<input type="hidden" name="gift_id" id="gift_id" value="{{$gft->pk_int_scratch_offers_listing_id}}">
		<input type="hidden" name="gimage" id="gimage" value="{{$gft->image}}">
		<input type="hidden" name="customer_count" id="customer_count" value="{{$cust_cnt}}">
		<input type="hidden" name="prev_url" id="prev_url" value="{{url()->previous()}}">
	
			@if($cust_cnt>0)
			<div class="mb-2 row">
			    <div class="col-lg-11 col-xl-11 col-xxl-11">
					<ul>
					<li class="text-red">{{$cust_cnt}} customers scratched this offer.</li>
					</ul>
				</div>
			</div>
			@endif
		
			<div class="mb-2 row">
			    <div class="col-lg-5 col-xl-5 col-xxl-5">
				<label for="example-text-input" class="col-form-label">Gift Count</label>
				<input class="form-control " type="text" name="offer_count_edit" id="offer_count_edit" value="{{$gft->int_scratch_offers_count}}" required>
				</div>

			</div>
			
				
			<div class="mb-2 row">
			    <div class="col-lg-11 col-xl-11 col-xxl-11">
				<label for="example-text-input" class="col-form-label">Description</label>
				<textarea class="form-control" name="description_edit" required>{{$gft->txt_description}}</textarea>
				
				</div>
			</div>
						
			<div class="mb-2 row">
			    <div class="col-lg-8 col-xl-8 col-xxl-8">
				<label for="example-text-input" class="col-form-label">Image(Max:500mb, Size 450x450) </label>
				<input class="form-control" type="file" name="gift_image_edit" id="gift_image_edit" >
				</div>
				<div class="col-lg-3 col-xl-3 col-xxl-3">
				<img src="{{url('uploads').'/'.$gft->image}}" class="gift_image_output_edit" style="width:100px;">
				</div>
			</div>
										
			<div class="mb-2 row">
			    <div class="col-lg-11 col-xl-11 col-xxl-11">
				<label for="example-text-input" class="col-form-label">Status</label>
				<select name="winning_status_edit" id="winning_status_edit" class="form-control wstatus" 
				@if($gft->int_scratch_offers_count!=$gft->int_scratch_offers_balance)
					disabled
				@endif
				required>
						<option value="">--select--</option>
						<option value="1" @if($gft->int_winning_status==1) selected @endif>Win</option>
						<option value="0" @if($gft->int_winning_status==0) selected @endif>Loss</option>
				</select>
				</div>
			</div>
						
		
			<div class="mb-2 mt-3 row">
			    <div class="col-lg-11 col-xl-11 col-xxl-11 text-right">
				<button type="button" class="btn btn-danger btn-reset"  data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
				<button class="btn btn-primary btn-xs " id="btn_save_gift"  type="submit" > Update </button>
				</div>
			</div>	
		
	</form>


<script>
$(document).on('click','.btn-reset',function()
{
	$("#edit-gift .offcanvas-body").html('');
});

$(document).on('change','#gift_image_edit',function()
{
	var size=$("#gift_image_edit")[0].files[0].size;
	if(size>524288)
	{
		alert("Image size too large. Maximum 500Kb only");
		$(this).val('');
	}
	else
	{
		var file=this.files[0];
			var allowedExtensions="";
			allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
			var filePath = file.name;
			console.log(file);
		
			if (!allowedExtensions.exec(filePath)) { 
				alert('Invalid file type, Try again.'); 
				  $(this).val('');
				  $(".gift_image_output_edit").attr('src','');
			}
			else
			{
			    if (file) {
				  var reader = new FileReader();
				  reader.onload = function (e) {
				     $(".gift_image_output_edit").attr('src',e.target.result);
				  }
				  reader.readAsDataURL(file);
				}
			}  
	}
});
</script>

