@extends('layouts.master')
@section('title','Scratch Bills')
@section('contents')
<style>
.card-body{
	padding-top:2px !important;
}
.td-count
{
	width:10%;
}
.td-desc
{
	width:30%;
}						
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">General Settings</div>
 
             <!-- <div class="ms-auto">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary">Settings</button>
                  <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">	<a class="dropdown-item" href="javascript:;">Action</a>
                    <a class="dropdown-item" href="javascript:;">Another action</a>
                    <a class="dropdown-item" href="javascript:;">Something else here</a>
                    <div class="dropdown-divider"></div>	<a class="dropdown-item" href="javascript:;">Separated link</a>
                  </div>
                </div>
              </div>  -->
            </div>
            <!--end breadcrumb-->
			
			
			<div class="row">
			<div class="col-6 col-lg-6 col-xl-6 col-xxl-6">
		
			<div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;Set Customer OTP</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				     <!--<a href="javascript:;" class="btn btn-gl-primary" ><i class="lni lni-upload"></i>&nbsp;Export</a>-->
				  </div>
				  </div>
                </div>
                <div class="card-body">

                 <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100 mt-2">
						
						<div class="row mt-3">
						<div class="col-12 col-lg-12">
						<p style="font-weight:500;">➧ Scratch & Win</b></p>
						<div class="d-flex" style="padding-left:20px;">
						<p class="pt-2">●&nbsp; Scratch Customers OTP verification is <b><span id="enabled">{{$data['otp_bypass_value']}}</span></b></p>&nbsp;&nbsp; 
						<div class="form-check form-switch">
							<input class="form-check-input" style="width:70px;height:30px;" id="otp_bypass" type="checkbox" id="flexSwitchCheckChecked"
							 value="{{$data['otp_bypass_value']}}" @if($data['otp_bypass_value']=='Enabled') checked @endif >
						</div>	
						
						</div>
						</div>
						</div>
									
                    </div>
                   </div><!--end row-->
                </div>
			</div>
			</div>
			
		
			<div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;Connect CRM API</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				     <!--<a href="javascript:;" class="btn btn-gl-primary" ><i class="lni lni-upload"></i>&nbsp;Export</a>-->
				  </div>
				  </div>
                </div>
                <div class="card-body">

				
				<div class="row">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
						
						<form id="formAddToken">
						@csrf
						<div class="row mt-3">
							<div class="col-12 col-lg-12">
							<p style="font-weight:500;">➧ To insert customer details to CRM</p>
							</div>
							<div class="col-8 col-lg-8 col-xl-8 col-xxl-8">
							<div id="div_token" style="padding-left:20px;">
								<p class="pt-2">●&nbsp;Enter Your CRM Account API token here and Enabled this option</p> 
								<input type ="text" class="form-control" name="crm_api_token" id="crm_api_token" value="{{$data['crm_api_token']}}" required>
							</div>
							</div>
							
							<div class="col-1 col-lg-1">
								<p class="pt-2 mt-0 mb-0">&nbsp;&nbsp;</p> 
								<button type ="submit" class="btn btn-primary mt-3" > Submit </button>
							</div>
							</div>
						</form>
						<form id="formEnabledCrmApi">
						@csrf
							
							<div class="row mt-3">
								<div class="col-12 col-lg-12" style="padding-left:30px;">
								<div class="d-flex">
								<p class="pt-2">●&nbsp; This service is  <b><span id="api_enabled">@if($data['crm_api_status']==1)Enabled @else Disabled @endif</span></b></p>&nbsp;&nbsp; 
								<div class="form-check form-switch">
									<input class="form-check-input" style="width:70px;height:30px;" id="crm_api_enabled_disabled" name="api_status" type="checkbox" 
									 value="{{$data['crm_api_status']}}"  @if($data['crm_api_status']==1) checked  @endif>
								</div></div>	
								</div>
							</div>
						</form>
									
                    </div>
                   </div><!--end row-->
                </div>

              </div>

		</div>
		</div>
			<div class="col-6 col-lg-6 col-xl-6 col-xxl-6">
			
			</div>
		</div>	  
		
		  
		
@push('scripts')


@if(Session::get('success'))
	<script>
		toastr.success("{{Session::get('success')}}");
	</script>
@endif

@if (Session::get('fail'))
	<script>
		toastr.error("{{Session::get('fail')}}");
	</script>
@endif

<script>

BASE_URL ={!! json_encode(url('/')) !!}

$(document).on('click','#otp_bypass',function()
{
	var bypass_value=$(this).val();

			jQuery.ajax({
			type: "POST",
			url: "{{url('users/set-scratch-otp-enabled')}}",
			dataType: 'json',
			data: {_token:"{{csrf_token()}}",otp_bypass_value: bypass_value},
			success: function(res)
			{
			   if(res.status==true)
			   {
				   toastr.success(res.msg);
				   $("#enabled").html(res.bypass_value);
				   $("#otp_bypass").val(res.bypass_value);
			   }
			   else
			   {
				   toastr.error(res.msg);
			   }
			}
		});
});


$(document).on('change','#crm_api_enabled_disabled',function()
{
	var status_value=$(this).val();
	alert(status_value);
			jQuery.ajax({
			type: "POST",
			url: "{{url('users/set-crm-api-status')}}",
			dataType: 'json',
			data: $("#formEnabledCrmApi").serialize(),
			success: function(res)
			{
			   if(res.status==true)
			   {
				   toastr.success(res.msg);
				   $("#api_enabled").html(res.status_value);
				   $("#crm_api_enabled_disabled").val(res.api_status);
			   }
			   else
			   {
				   toastr.error(res.msg);
				   $("#formEnabledCrmApi")[0].reset();
			   }
			}
		});
});





var validate=$('#formAddToken').validate({ 
	
		rules: {
               crm_api_token: {
                   required: true,
               },
           },

	submitHandler: function(form) 
	{

		$.ajax({
		url: "{{route('users.save-crmapi-token')}}",
		method: 'post',
		data: $('#formAddToken').serialize(),
		success: function(result){
			if(result.status == 1)
			{
				toastr.success(result.msg);
			}
			else
			{
				toastr.error(result.msg);
			}
		}
		});
	  }
	});


$(document).on('click','#set_crm_token',function()
{
	var bypass_value=$(this).val();
	if($(this).is(':checked'))
	{
		$("#div_token").removeClass('hide').addClass('show');
	}		
	else
	{
		$("#div_token").removeClass('show').addClass('hide');
	}
});


$(document).on('click','#set_crm_token',function()
{
	var bypass_value=$(this).val();
	if($(this).is(':checked'))
	{
		$("#div_token").removeClass('hide').addClass('show');
	}		
	else
	{
		$("#div_token").removeClass('show').addClass('hide');
	}
	
	
	
/*
		jQuery.ajax({
			type: "POST",
			url: "{{url('users/set-scratch-otp-enabled')}}",
			dataType: 'json',
			data: {_token:"{{csrf_token()}}",otp_bypass_value: bypass_value},
			success: function(res)
			{
			   if(res.status==true)
			   {
				   toastr.success(res.msg);
				   $("#enabled").html(res.bypass_value);
				   $("#otp_bypass").val(res.bypass_value);
			   }
			   else
			   {
				   toastr.error(res.msg);
			   }
			}
		});*/
		
});

</script>
@endpush
@endsection
