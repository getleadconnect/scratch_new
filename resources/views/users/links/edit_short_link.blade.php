<form id="formEditLink" method="POST" action="{{route('users.update-link')}}" enctype="multipart/form-data">
			@csrf
		
		<input type="hidden"  name="link_id" value="{{$sl->id}}">

			<div class="mb-2 row">
			    <div class="col-lg-12 col-xl-12 col-xxl-12">
				<label for="example-text-input" class="col-form-label">Short Link</label>
				<input class="form-control" type="text" name="short_link_edit" id="short_link_edit" value="{{$sl->link}}" disabled readonly>
				</div>
			</div>

			<div class=" mb-2 row">				
				<div class="col-lg-12 col-xl-12 col-xxl-12">
						<label for="example-text-input" class="col-form-label">Scratch Offer</label>
						<select class="form-select" name="offer_id_edit" id="offer_id_edit" required>
							<option selected="">select</option>
							@foreach($offers as $row)
							<option value="{{$row->pk_int_scratch_offers_id}}" @if($row->pk_int_scratch_offers_id==$sl->offer_id){{__('selected')}}@endif>{{$row->vchr_scratch_offers_name}}</option>
							@endforeach
						  </select>
				</div>
			</div>

			
			<div class="mb-2 mt-2 row">
				<div class="col-lg-8 col-xl-8 col-xxl-8">
					<label for="example-text-input" class="col-form-label">Bill Number Required?</label>
					<div style="display:flex;" class="align-item-center;">					
					<input type="radio" value="0" name="custom_field_edit" style="width:20px;height:20px;" {{($sl->custom_field == 0)?'checked':''}}><span style="margin-right:15px;">&nbsp;NO</span>
                    <input type="radio" value="1" name="custom_field_edit" style="width:20px;height:20px;" {{($sl->custom_field == 1)?'checked':''}}><span>&nbsp;YES</span>
					</div>
			</div>
			</div>
					
						
			<div class="mb-2 mt-2 row">
				<div class="col-lg-8 col-xl-8 col-xxl-8">
					<label for="example-text-input" class="col-form-label">Branch Required?</label>
					<div style="display:flex;" class="align-item-center;">					
					<input type="radio" value="0" name="branch_required_edit" style="width:20px;height:20px;" {{($sl->branch_required == 0)?'checked':''}}><span style="margin-right:15px;">&nbsp;NO</span>
                    <input type="radio" value="1" name="branch_required_edit" style="width:20px;height:20px;" {{($sl->brnach_required == 1)?'checked':''}}><span>&nbsp;YES</span>
					</div>
			</div>
			</div>
			
			<div class="mb-2 mt-2 row">
				<div class="col-lg-8 col-xl-8 col-xxl-8">
					<label for="example-text-input" class="col-form-label">Email Required?</label>
					<div style="display:flex;" class="align-item-center;">					
					<input type="radio" value="0" name="email_required_edit" style="width:20px;height:20px;" {{($sl->email_required == 0)?'checked':''}}><span style="margin-right:15px;">&nbsp;NO</span>
                    <input type="radio" value="1" name="email_required_edit" style="width:20px;height:20px;"  name="custom_field" {{($sl->email_required == 1)?'checked':''}}><span>&nbsp;YES</span>
					</div>
			</div>
			</div>
			
			<hr class="mt-3">
			<div class="mb-2 row">	
				<div class="col-lg-12 col-xl-12 col-xxl-12 " style="text-align:right;">
				<button type="button" class="btn btn-danger btn-reset"  id="btn-reset-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
				<button class="btn btn-primary" type="submit">Update Link</button>
				</div>
			</div>
</form>

<script>


$('#formEditLink').validate({ // initialize the plugin
           rules: {
               offer_id: {
                   required: true,
               },
   
               code: {
                   required: true,
                   maxlength: 8,
                   minlength: 2

               },
   
           },
   
       });
	   

$(document).on('click','#btn-reset-offcanvas',function()
{
	$("#edit-link .offcanvas-body").html('');
});



</script>

