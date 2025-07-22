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
 
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-4">

<div class="col">
	<div class="card radius-10">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <div class="">
			<h6 class="mb-1">Total Scratches</h6>
			<h4 class="mb-0 text-pink">0</h4>
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
			<h6 class="mb-1">Scratch Gifts</h6>
			<h4 class="mb-0 text-info">0</h4>
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
				<h6 class="mb-1">&nbsp;</h6>
				<h4 class="mb-0 text-purple"></h4>
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
				<h6 class="mb-1">&nbsp;</h6>
				<h4 class="mb-0 text-purple"></h4>
			  </div>
			  <div class="ms-auto fs-2 text-purple">
				<i class="bx bx-receipt"></i>
			  </div>
			</div>
		  </div>
		</div>
	</div>	   
   
   
  </div><!--end row-->

<div class="row">
  <div class="col-12 col-lg-12 d-flex">
	<div class="card radius-10 w-100">
	  <div class="card-body">
		<div class="d-flex align-items-center">
		  <h6 class="mb-0">Scratched Customers</h6>
		</div>
		<hr/>

	  </div>
	</div>
  </div>

  </div>
</div><!--end row-->

			
@push('scripts')
<script>

</script>
@endpush
@endsection
