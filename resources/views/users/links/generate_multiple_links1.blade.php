{{--<form id="formAddLink" method="POST" action="{{route('users.generate-multiple-links')}}" enctype="multipart/form-data">--}}
  
<style>
body{background-color:#fff !important;}
.text-white{color:#fff;text-shadow:1px 1px 1px #000;}
h3.h3{text-align:center;padding:1.5em 0em 2em 0em;text-transform:capitalize;font-size:1.5em;}
.container-fluid{padding:2em 0em 4em 0em;background: linear-gradient(to right,#7474bf,#348ac7);}
.container{padding:2em 0em 5em 0em;}
.bg-1{background: linear-gradient(to right,#7b4379,#dc2430);}
.bg-2{background:#ABAD5D;}
.bg-3{background:#1ABC9C;}

/********************  Preloader Demo-15 *******************/
.loader15{text-align:center;margin:30px 0}
.loader15 span{width:20px;height:20px;border-radius:50%;background:#f7bd3a;display:inline-block}
.loader15 span:first-child{animation:loading-152 .5s linear infinite;opacity:0;transform:translate(-20px)}
.loader15 span:nth-child(2),.loader15 span:nth-child(3){animation:loading-153 .5s linear infinite}
.loader15 span:last-child{animation:loading-15 .5s linear infinite}
@-webkit-keyframes loading-15{
	100%{transform:translate(40px);opacity:0}
}
@keyframes loading-15{
	100%{transform:translate(40px);opacity:0}
}
@-webkit-keyframes loading-152{
	100%{transform:translate(20px);opacity:1}
}
@keyframes loading-152{
	100%{transform:translate(20px);opacity:1}
}
@-webkit-keyframes loading-153{
	100%{transform:translate(20px)}
}
@keyframes loading-153{
	100%{transform:translate(20px)}
}
.pre-loader
{
	width:250px;
	height:70px;
	background:#e4e4e4;
}
</style>


<div class="pre-loader">
        <div class="row">
            <div class="col-md-12">
                <div class="loader15">
                    <span></span><span></span><span></span><span></span>
                </div>
            </div>
        </div>
	</div>


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
					<button type="button" class="btn btn-danger btn-reset"  id="btn-reset-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
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