{{--<form id="formAddLink" method="POST" action="{{route('users.save-link')}}" enctype="multipart/form-data">--}}
  
		<form id="formAddLink" enctype="multipart/form-data">
			@csrf
			
			@php
				$vendor_id=\App\Models\User::getVendorId();
				$link=env('SHORT_LINK_DOMAIN').'/'.$vendor_id.'/';
			@endphp
			
			
				<div class="mb-2 row">
					<div class="col-lg-12 col-xl-12 col-xxl-12">
					<label for="example-text-input" class="col-form-label">Short Link<span class="text-danger">*</span></label>
					<input class="form-control" type="text" name="short_linke" id="short_link" value="{{$link}}" disabled readonly >
					</div>
				</div>
							
				<div class="mb-2 row">
					<div class="col-lg-12 col-xl-12 col-xxl-12">
					<label for="example-text-input" class="col-form-label">Enter Short Code (<small class="text-blue">Min 6 characters</small>)<span class="text-danger">*</span></label>
					<input class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" type="text" name="code" id="code" value="{{old('code')}}" placeholder="Short Link" required >
						  @if ($errors->has('code'))
						  <span class="invalid-feedback" role="alert">
						  <strong>{{ $errors->first('code') }}</strong>
						  </span>
						  @endif
					</div>
				</div>
								
				<div class=" mb-2 row">				
					<div class="col-lg-12 col-xl-12 col-xxl-12">
							<label for="example-text-input" class="col-form-label">Scratch Your Offer<span class="text-danger">*</span></label>
							<select class="form-select" name="offer_id" id="offer_id" required>
								<option value="" >select</option>
								@foreach($offers as $row)
								
									@if($offer_id!="" and ($row->pk_int_scratch_offers_id==$offer_id))
										<option value="{{$row->pk_int_scratch_offers_id}}" @if($row->pk_int_scratch_offers_id==$offer_id) selected @endif >{{$row->vchr_scratch_offers_name}}</option>
									@else
										<option value="{{$row->pk_int_scratch_offers_id}}" >{{$row->vchr_scratch_offers_name}}</option>
									@endif
								
								@endforeach
							  </select>
					</div>
				</div>
				
				
				<div class="mb-2 mt-3 row">
					<div class="col-lg-8 col-xl-8 col-xxl-8">
						<label for="example-text-input" class="col-form-label">Bill Number Required?</label>
						<div style="display:flex;" class="align-item-center ps-2">					
						<input type="radio" value="0" class="bill_req" name="custom_field"  style="width:20px;height:20px;" checked><span style="margin-right:15px;">&nbsp;NO</span>
						<input type="radio" value="1" class="bill_req" name="custom_field" style="width:20px;height:20px;" ><span>&nbsp;YES</span>
						</div>
				</div>
				</div>

							
				<div class="mb-2 mt-3 row">
					<div class="col-lg-8 col-xl-8 col-xxl-8">
						<label for="example-text-input" class="col-form-label">Branch Required?</label>
						<div style="display:flex;" class="align-item-center ps-2">					
						<input type="radio" value="0" name="branch_required"  style="width:20px;height:20px;" checked><span style="margin-right:15px;">&nbsp;NO</span>
						<input type="radio" value="1" name="branch_required" style="width:20px;height:20px;" ><span>&nbsp;YES</span>
						</div>
				</div>
				</div>
					
				<div class="mb-2 mt-3 row">
					<div class="col-lg-8 col-xl-8 col-xxl-8">
						<label for="example-text-input" class="col-form-label">Email Required?</label>
						<div style="display:flex;" class="align-item-center ps-2">					
						<input type="radio" value="0" name="email_required" style="width:20px;height:20px;" checked><span style="margin-right:15px;">&nbsp;NO</span>
						<input type="radio" value="1" name="email_required" style="width:20px;height:20px;" ><span>&nbsp;YES</span>
						</div>
				</div>
				</div>
						
								
				<hr class="mt-3">
				<div class="mb-2 row">	
					<div class="col-lg-12 col-xl-12 col-xxl-12 " style="text-align:right;">
					<button type="button" class="btn btn-danger btn-reset"  id="btn-reset-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
					<button class="btn btn-primary" type="submit">Save Link</button>
					</div>
				</div>
			</form>
			
<script>

// adding record --------------------------------------------				
				
var validate=$('#formAddLink').validate({ 
	
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

	submitHandler: function(form) 
	{
		//$("#partner_submit").attr('disabled',true).html('Saving <i class="fa fa-spinner fa-spin"></i>')

		$.ajax({
		url: "{{route('users.save-link')}}",
		method: 'post',
		data: $('#formAddLink').serialize(),
		success: function(result){
			if(result.status == 1)
			{
				$('#datatable').DataTable().ajax.reload(null,false);
				toastr.success(result.msg);
				$('#formAddLink')[0].reset();
				$("#btn-reset-offcanvas").trigger('click');
			}
			else
			{
				toastr.error(result.msg);
				$('#formAddLink')[0].reset();
			}
		}
		});
	  }
	});
   
</script>