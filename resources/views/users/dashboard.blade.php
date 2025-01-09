@extends('layouts.master')
@section('title','Dashboard')
@section('contents')
<style>
.theme-icons {
    background-color: #cdeaf3 !important;
    color: #434547;
}
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Dashboard</div>
 </div>
  

<div class="alert border-0 bg-light-danger alert-dismissible fade show py-2">
	<div class="d-flex align-items-center">
	  <div class="fs-3 text-danger"><i class="bi bi-x-circle-fill"></i>
	  </div>
	  <div class="ms-3">
	  
	  @if($sub['subscription']=="Active" and $sub_diff_days<=5)
	  	<div class="text-danger">Your subscription expiring in <b>@if($sub_diff_days==0) Today! @elseif($sub_diff_days==1) Tomorrow! @else {{$sub_diff_days}} days! @endif</b></div>
	  @elseif($sub['subscription']=="Expired")
	  <div class="text-danger">Your subscription has been <b>expired</b>. Please contact administrator. Thank You!</div>
	  @else
	  <div class="text-danger">You have no subscription, Please contact administrator and subscribe now!</div>
	  @endif
	  </div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>



<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-4">

<div class="col">
	<div class="card radius-10">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <div class="">
			<h6 class="mb-1">Total Scratches</h6>
			<h4 class="mb-0 text-pink">{{$tot_count??0}}</h4>
		  </div>
		  <div class="ms-auto fs-2 text-pink">
			<i class="bx bx-receipt"></i>
		  </div>
		</div>
	  </div>
	</div>
   </div>
  
 <div class="col">
	<div class="card radius-10">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <div class="">
			<h6 class="mb-1">Used Scratches</h6>
			<h4 class="mb-0 text-info">{{$used_count??0}}</h4>
		  </div>
		  <div class="ms-auto fs-2 text-info">
			<i class="bx bx-receipt"></i>
		  </div>
		</div>
	  </div>
	</div>
   </div>
   
   <div class="col">
		<div class="card radius-10">
		  <div class="card-body">
			<div class="d-flex align-items-center">
			  <div class="">
				<h6 class="mb-1">Balance Scratches</h6>
				<h4 class="mb-0 text-purple">{{$bal_count??0}}</h4>
			  </div>
			  <div class="ms-auto fs-2 text-purple">
				<i class="bx bx-receipt"></i>
			  </div>
			</div>
		  </div>
		</div>
	   </div>
		   
   <div class="col">
		<div class="card radius-10">
		  <div class="card-body">
			<div class="d-flex align-items-center">
			  <div class="">
				<h6 class="mb-1">Subscription : <span class="@if($sub['subscription']!='Active') text-red @else text-green @endif " >{{$sub['subscription']}}</span></h6>
				@if($sub['subscription']!="Expired")
				<h6 class="mb-1" style="color:#5959eb">{{$sub['start_date']}}&nbsp;&nbsp;=>&nbsp;&nbsp;{{$sub['end_date']}}</h6>
				@endif
			  </div>
			  <div class="ms-auto fs-2 text-primary">
				<i class="fadeIn bx bx-dollar-circle"></i>
			  </div>
			</div>
		  </div>
		</div>
	   </div>
   
  </div><!--end row-->

<div class="row">
  <div class="col-12 col-lg-8 d-flex">
	<div class="card radius-10 w-100">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <h6 class="mb-0">Scratched Customers</h6>
		</div>
		<hr/>

				<input type="hidden" id="series_1" value="{{$chart['win_count']}}">
				<input type="hidden" id="series_2" value="{{$chart['los_count']}}">
				<input type="hidden" id="legend_x" value="{{$chart['campaigns']}}">
								
				<div class="row">
					<div class="col-xl-12 mx-auto">
						<div class="chart-container1">
							<canvas id="chart2"></canvas>
						</div>
					</div>
				</div>
	  </div>
	</div>
  </div>
  
  <div class="col-12 col-lg-4 d-flex">
	<div class="card radius-10 w-100">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		   <h6 class="mb-0">---</h6>
		   </div>
			<hr/>
			
			<input type="hidden" id="ur_year" value="{{$chart['user_year']}}">
			<input type="hidden" id="ur_count" value="{{$chart['user_count']}}">
				
				<div class="row">
					<div class="col-xl-12 mx-auto">
						<div class="chart-container1">
				  		   <canvas id="chart61"></canvas>
						</div>
					</div>
				</div>
		   
		   
		</div>
		<!-- content here -->
	  </div>
	</div>
  </div>
</div><!--end row-->

			
@push('scripts')
<script>

</script>
@endpush
@endsection
