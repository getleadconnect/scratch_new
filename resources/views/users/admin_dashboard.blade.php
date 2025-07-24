@extends('layouts.master')
@section('title','Dashboard')
@section('contents')
<style>
.theme-icons {
    background-color: #cdeaf3 !important;
    color: #434547;
}

.w_chart {
    font-size: 1em !important;
}

.w_chart canvas
 {
    position: absolute;
    top: 9px !important;
    left: 8px !important;
    width: 60px !important;
    height: 60px !important;
}

</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Admin Dashboard</div>
 </div>
  
@if($sub['subscription']=="Active" and $sub_diff_days<=5)
<div class="alert border-0 bg-light-danger alert-dismissible fade show py-2">
	<div class="d-flex align-items-center">
	  <div class="fs-3 text-danger"><i class="bi bi-x-circle-fill"></i>
	  </div>
	  <div class="ms-3">
	  	<div class="text-danger">Your subscription expiring in <b>@if($sub_diff_days==0) Today! @elseif($sub_diff_days==1) Tomorrow! @else {{$sub_diff_days}} days! @endif</b></div>
	  </div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif($sub['subscription']=="Expired")
<div class="alert border-0 bg-light-danger alert-dismissible fade show py-2">
	<div class="d-flex align-items-center">
	  <div class="fs-3 text-danger"><i class="bi bi-x-circle-fill"></i>
	  </div>
	  <div class="ms-3">
	   <div class="text-danger">Your subscription has been <b>expired</b>. Please contact administrator. Thank You!</div>
	  </div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif($sub['subscription']=="No")
<div class="alert border-0 bg-light-danger alert-dismissible fade show py-2">
	<div class="d-flex align-items-center">
	  <div class="fs-3 text-danger"><i class="bi bi-x-circle-fill"></i>
	  </div>
	  <div class="ms-3">
	  <div class="text-danger">You have <b>no subscription</b>, Please contact administrator and subscribe now!</div>
	  </div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-6 row-cols-xl-6 row-cols-xxl-6">

  <div class="col">
	<div class="card radius-10">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <div class="">
			<h6 class="mb-1">Total Users</h6>
			<h4 class="mb-0 text-pink">5</h4>
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
			<h6 class="mb-1">Total Credit</h6>
			<h4 class="mb-0 text-pink">{{$data['total_credit']}}</h4>
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
			<h6 class="mb-1">Credit Used</h6>
			<h4 class="mb-0 text-pink">{{$data['used_credit']}}</h4>
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
			<h6 class="mb-1">Credit Balance</h6>
			<h4 class="mb-0 text-info">{{$data['bal_credit']}}</h4>
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
				<h6 class="mb-1">Subscription : <span class="@if($sub['subscription']!='Active') text-red @else text-green @endif " >{{strtoupper($sub['subscription'])}}</span></h6>
				@if($sub['subscription']!="Expired" and $sub['start_date']!="" and $sub['end_date']!="")
				<p class="mb-1" style="font-size:14px;color:#5959eb">{{$sub['start_date']}}&nbsp;&nbsp;=>&nbsp;&nbsp;{{$sub['end_date']}}</p>
				@endif
			  </div>
			  <div class="ms-auto fs-2 text-primary">
				<i class="fadeIn bx bx-dollar-circle"></i>
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
			<h6 class="mb-1">Options</h6>
			<h4 class="mb-0 text-info">0</h4>
		  </div>
		  <div class="ms-auto fs-2 text-info">
			<i class="bx bx-receipt"></i>
		  </div>
		</div>
	  </div>
	</div>
   </div>
   
  </div><!--end row-->
  <div class="row mt-3">
 <div class="col-12 col-lg-7 col-xl-7 col-xxl-7 d-flex">
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
  <div class="col-12 col-lg-5 col-xl-5 col-xxl-5 d-flex">
	<div class="card radius-10 w-100">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <h6 class="mb-0">Options</h6>
		</div>
		<hr/>

				
	  </div>
	</div>
  </div>
  
  </div>


  
  
  {{--<!--<div class="col-12 col-lg-3 col-xl-3 col-xxl-3">
  
  <div class="card radius-10 w-100">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <h6 class="mb-0">Analytics</h6>
		</div>
		<hr/>
  

		<div class="col">
			  <div class="card radius-10">
				<div class="card-body">
				  <div class="d-flex align-items-center">
					<div class="">
					  <p class="mb-1">Total Scratch Customers</p>
					  <h4 class="mb-0 text-primary">{{$pie['tot_count']}}</h4>
					</div>
					<div class="ms-auto">
					  <div class="w_chart" id="chart18" data-percent="100">
						<span class="w_percent" id="tot_percentage">100</span>
					  <canvas height="110" width="110"></canvas></div>
					</div>
				  </div>
				</div>
			  </div>
			</div>

				
				<div class="col-12">
				  <div class="card radius-10">
					<div class="card-body">
					  <div class="d-flex align-items-center">
						<div class="">
						  <p class="mb-1">Scratch Customers (<b>Win</b>)</p>
						  <h4 class="mb-0 text-success">{{$pie['win_count']}}</h4>
						</div>
						<div class="ms-auto">
						  <div class="w_chart" id="chart19" data-percent="{{$pie['win_per']}}">
							<span class="w_percent" id="win_percentage">{{$pie['win_per']}}</span>
						  <canvas height="110" width="110"></canvas></div>
						</div>
					  </div>
					</div>
				  </div>
				</div>
				
				<div class="col-12">
				  <div class="card radius-10">
					<div class="card-body">
					  <div class="d-flex align-items-center">
						<div class="">
						  <p class="mb-1">Scratch Customers (<b>Loss</b>)</p>
						  <h4 class="mb-0 text-pink">{{$pie['los_count']}}</h4>
						</div>
						<div class="ms-auto">
						  <div class="w_chart" id="chart17" data-percent="{{$pie['los_per']}}">
							<span class="w_percent" id="los_percentage" >{{$pie['los_per']}}</span>
						  <canvas height="110" width="110"></canvas></div>
						</div>
					  </div>
					</div>
				  </div>
				</div>
	</div>
  </div>
  </div>-->--}}
  
  <!--<div class="col-12 col-lg-4 d-flex">
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
	 <!--</div>
	</div> -->
	
  </div>
</div><!--end row-->

@push('scripts')

<script src="{{url('assets/plugins/easyPieChart/jquery.easypiechart.js')}}"></script>
<!--<script src="{{url('assets/plugins/apexcharts-bundle/js/apexcharts.min.js')}}"></script>-->
<script src="{{url('assets/js/data-widgets.js')}}"></script>

<script>

</script>
@endpush
@endsection
