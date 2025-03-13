{{--<form id="formAddLink" method="POST" action="{{route('users.generate-multiple-links')}}" enctype="multipart/form-data">--}}
  
  <!-- pre-loader-------------------------->
  	   <div class="loading-outer hide" >
			<div class="loading-inner" style="position:relative;">
			  <span class="spinner-loading">
			  <label style="text-align:center;width:90%;"> <i class="text-red fa fa-spinner fa-spin fa-4x"></i> </label>
			  <h6 style="color:red;"> Please Wait.....</h6>
			  </span>
			</div>
	   </div>
  <!---------------------------------------->   
  
		<div class="mb-2 row">
			<div class="col-lg-12 col-xl-12 col-xxl-12">
			<ul style="color:blue;">
			<li>To create one time scratch links. Once scratch the link is automaticaly disabled</li>
			<li>And repeated scratching not allowed</li>
			</ul>
			</div>
		</div>
  
    
		<form id="formMultipleLinks" enctype="multipart/form-data">
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
							
				<!--<div class="mb-2 row">
					<div class="col-lg-11 col-xl-11 col-xxl-11">
					<label for="example-text-input" class="col-form-label">Link identified Group Code (<small class="text-blue">3 to 5 character</small>)<span class="text-danger">*</span></label>
					<input class="form-control " type="text" name="link_group_code" id="link_group_code" value="{{old('link__group_code')}}" placeholder="Multiple link group code" minlength=3 maxlength=5 required >
						  @if ($errors->has('code'))
						  <span class="invalid-feedback" role="alert">
						  <strong>{{ $errors->first('code') }}</strong>
						  </span>
						  @endif
					</div>
				</div>-->
								
				<div class=" mb-2 row">				
					<div class="col-lg-12 col-xl-12 col-xxl-12 ">
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
				
				<div class="mb-3 mt-3 row">
					<div class="col-lg-8 col-xl-8 col-xxl-8">
						<label for="example-text-input" class="col-form-label">Shop Selection in scratch link?</label>
						<div style="display:flex;" class="align-item-center ps-2">					
						<input type="radio" value="0" name="branch_required"  style="width:20px;height:20px;" checked><span style="margin-right:15px;">&nbsp;NO</span>
						<input type="radio" value="1" name="branch_required" style="width:20px;height:20px;" ><span>&nbsp;YES</span>
						</div>
				</div>
				</div>
				
				<div class="mb-2 row">
					<div class="col-lg-8 col-xl-8 col-xxl-8">
					<label for="example-text-input" class="col-form-label">Lniks Count (<small class="text-blue">Max : 1000 links only</small>)<span class="text-danger">*</span></label>
					<input class="form-control " type="number" name="link_count" id="link_count" value="{{old('link_count')}}" maxlength=1000 placeholder="link count" required >
						  @if ($errors->has('link_count'))
						  <span class="invalid-feedback" role="alert">
						  <strong>{{ $errors->first('link_count') }}</strong>
						  </span>
						  @endif
					</div>
					<div class="col-lg-12 col-xl-12 col-xxl-12">
						<div>&nbsp;<label style="float:left;" id="link_count-error"class="error" for="link_count"></label></div>
					</div>
				</div>
		
				<hr class="mt-3">
				<div class="mb-2 row">	
					<div class="col-lg-12 col-xl-12 col-xxl-12 " style="text-align:right;">
					<button type="button" class="btn btn-danger btn-reset btn-close"  id="btn-reset-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
					<button class="btn btn-primary" type="submit">Generate Links</button>
					</div>
				</div>
			</form>
			
<script>

// adding record --------------------------------------------



	
				
var validate=$('#formMultipleLinks').validate({ 
	
		rules: {
               offer_id: {
                   required: true,
               },
			link_count: {
                   required: true,
				   maxlength:1000
               },
           },

		submitHandler: function(form) 
		{
			//$("#partner_submit").attr('disabled',true).html('Saving <i class="fa fa-spinner fa-spin"></i>')

			if(parseInt($("#link_count").val())>1000)
			{
				$("#link_count-error").css('display','block');
				$("#link_count-error").html('Count exceeded, Maximum 1000 links!');
				
			}
			else
			{
				$(".loading-outer").removeClass('hide').addClass('show');
				
				$.ajax({
				url: "{{route('users.generate-multiple-links')}}",
				method: 'post',
				data: $('#formMultipleLinks').serialize(),
				success: function(result){
					if(result.status == 1)
					{
						$('#datatable').DataTable().ajax.reload(null,false);
						toastr.success(result.msg);
						$('#formMultipleLinks')[0].reset();
						$(".loading-outer").removeClass('show').addClass('hide');
						$("#btn-reset-offcanvas").trigger('click');
					}
					else
					{
						toastr.error(result.msg);
						//$('#formMultipleLinks')[0].reset();
					}
				}
				});
			}
		}
	});
   
</script>