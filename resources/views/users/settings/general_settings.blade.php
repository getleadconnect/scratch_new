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
			<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
		
			<div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-users"></i> Settings</h6>
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
						<p>Scratch & Win</p>
						<div class="d-flex">
						<p class="ps-3 pt-2">●&nbsp; Scratch Customers OTP verification is On/Off</p>&nbsp;&nbsp; 
						<div class="form-check form-switch">
							<input class="form-check-input" data-userid="{{data['user_id']}}" style="width:70px;height:30px;" id="otp_bypass" type="checkbox" id="flexSwitchCheckChecked"
							@if($data['otp_user_id']!="") value="1" checked @else value="0" @endif >
						</div>	
						</div>
						</div>
						</div>
						
			
                    </div>
                   </div><!--end row-->
                </div>
              </div>
		
		
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




$(document).on('click','.otp_bypass',function()
{

	var id=$(this).value('id');
	var userid=$(this).data('userid');
	
			jQuery.ajax({
			type: "GET",
			url: "{{url('users/edit-staff-user')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});
});


</script>
@endpush
@endsection
