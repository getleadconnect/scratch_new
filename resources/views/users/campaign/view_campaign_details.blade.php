@extends('layouts.master')
@section('title','Dashboard')
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

.crd-height
{
	height:146px;
}

</style>

		<div class="row mt-3 mb-3">	
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6">

		<div class="page-breadcrumb d-none d-lg-flex align-items-center mb-3">
			<div class="breadcrumb-title pe-3">Campaign Details</div>
		</div>
		</div>
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6 text-right">
				  @php
				  $cid=request()->segment(3);
				  @endphp
 
				  <a href="{{route('users.campaigns')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>&nbsp;&nbsp;
				  <a href="{{route('users.add-gifts',$cid)}}" class="btn btn-primary"><i class="fa fa-trophy"></i>&nbsp;Add Gifts</a>&nbsp;&nbsp;
				  <a href="javascript:;" id="{{$cid}}" class="btn btn-primary link-add" data-bs-toggle="offcanvas" data-bs-target="#add-link"><i class="fa fa-plus"></i>&nbsp;Add Scratch Link</a>&nbsp;&nbsp;

		</div>
        </div>

		
		<div class="row">
		<div class="col-lg-5 col-xl-5 col-xxl-5 col-5">
		
		    <div class="col">
                <div class="card radius-10">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="">
						<h4 class="mb-1 mt-2 text-primary">Campaign: {{$offer->vchr_scratch_offers_name}}</h4>
                        <p class="mb-1">Type: {{$offer->type}}</p>
						
						<p class="mb-1">Expiry Date : 
							@if($offer->end_date!="")
							{{date_create($offer->end_date)->format('d-m-Y')}}
							@else 
								--
							@endif
						</p>
						
						
                      </div>
                      <div class="ms-auto fs-2 text-primary">
                        <!--<i class="bi bi-bell"></i>-->
                      </div>
                    </div>
                    <hr class="my-2">
                    <span class="mb-0"><i class="bi bi-arrow-up"></i> <span class="text-blue " >Expiring in {{$diff_days}} days</span></span>
					
                  </div>
                </div>
               </div>

		</div>
		<div class="col-lg-7 col-xl-7 col-xxl-7 col-7">
		
				<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-3">

					<div class="col">
					<div class="card radius-10 crd-height">
					  <div class="card-body">
						<div class="d-flex align-items-center">
						  <div class="">
							<h5 class="mb-1 mt-3 p-2">Total Gift</h5>
							<h4 class="mb-0 text-pink p-2">{{$counts['total_count']}}</h4>
						  </div>
						  <div class="ms-auto fs-2 text-pink">
							<i class="bx bx-trophy"></i>
						  </div>
						</div>
					  </div>
					</div>
				   </div>
				  
				 <div class="col">
					<div class="card radius-10 crd-height">
					  <div class="card-body">
						<div class="d-flex align-items-center">
						  <div class="">
							<h6 class="mb-1 mt-3 p-2">Used Gifts</h6>
							<h4 class="mb-0 text-info p-2">{{$counts['used_count']}}</h4>
						  </div>
						  <div class="ms-auto fs-2 text-info">
							<i class="bx bx-trophy"></i>
						  </div>
						</div>
					  </div>
					</div>
				   </div>
				   
				   <div class="col">
						<div class="card radius-10 crd-height" >
						  <div class="card-body">
							<div class="d-flex align-items-center">
							  <div class="">
								<h6 class="mb-1 mt-3 p-2">Gift Balance</h6>
								<h4 class="mb-0 text-purple p-2">{{$counts['bal_count']}}</h4>
							  </div>
							  <div class="ms-auto fs-2 text-purple">
								<i class="bx bx-trophy"></i>
							  </div>
							</div>
						  </div>
						</div>
					   </div>

				</div><!--end row-->
			</div>
		</div>
		
		<div class="card">
                <div class="card-header p-y-3">
				<div class="row">
					<div class="col-lg-8 col-xl-8 col-xxl-8 col-8">
					  <h6 ><i class="fa fa-users"></i> Scratch Customers List</h6>
					</div>
					
					<div class="col-lg-4 col-xl-4 col-xxl-4 col-4 text-right">

					 <label>Web Total : <span style="font-weight:600;" id="web_count"></span></label>
					 &nbsp;|&nbsp;<label class="ms-1">App Total : <span style="font-weight:600;" id="app_count"></span></label>

					</div>
				  
				</div>
                </div>
                <div class="card-body">
				
				<input type="hidden" id="campaign_id" value="{{$cid}}">
									
								<ul class="nav nav-tabs nav-primary mt-3" role="tablist">
									<li class="nav-item" role="presentation">
										<a class="nav-link active" data-bs-toggle="tab" href="#primaryhome" role="tab" aria-selected="false" tabindex="-1">
											<div class="d-flex align-items-center">
												<div class="tab-icon"><i class="bx bx-user-pin font-18 me-1"></i>
												</div>
												<div class="tab-title">Web Customers</div>
											</div>
										</a>
									</li>
									<li class="nav-item" role="presentation">
										<a class="nav-link" data-bs-toggle="tab" href="#primaryprofile" role="tab" aria-selected="false" tabindex="-1">
											<div class="d-flex align-items-center">
												<div class="tab-icon"><i class="bx bx-user-pin font-18 me-1"></i>
												</div>
												<div class="tab-title">App Customers</div>
											</div>
										</a>
									</li>
									
								</ul>
								<div class="tab-content py-3">
									<div class="tab-pane fade show active" id="primaryhome" role="tabpanel">
										
									<div class="row mt-2">
										 <div class="col-12 col-lg-12 d-flex">
										  <div class="card  shadow-none w-100">
										  
												<div class="table-responsive">
													<table class="table" id="datatable" style="width:100% !important;">
														<thead class="thead-semi-dark">
														  <tr>
															<th>SlNo</th>
															<th>Name</th>
															<th>Mobile</th>
															<th>Email</th>
															<th>Created At</th>
															<th>Branch</th>
															<th>Offer</th>
															<th>Status</th>
															<th>Redeem</th>
															  </tr>
														</thead>
														<tbody>
																								
														</tbody>
													</table>
												</div>
									
										   <!-- </div>-->
										  </div> 
										</div>
									   </div><!--end row-->
									
									</div>
									
									<!-- tab page-2 ------------------------>
									<div class="tab-pane fade" id="primaryprofile" role="tabpanel">
										<div class="row mt-2">
										 <div class="col-12 col-lg-12 d-flex">
										  <div class="card  shadow-none w-100">
										  
												<div class="table-responsive">
													<table class="table" id="datatable_app" style="width:100% !important;">
														<thead class="thead-semi-dark">
														  <tr>
															<th>SlNo</th>
															<th>Name</th>
															<th>Mobile</th>
															<th>Email</th>
															<th>Created At</th>
															<th>Branch</th>
															<th>Offer</th>
															<th>Status</th>
															<th>Redeem</th>
															  </tr>
														</thead>
														<tbody>
																								
														</tbody>
													</table>
												</div>
									
										   <!-- </div>-->
										  </div> 
										</div>
									   </div><!--end row-->
									</div>
							
								</div>
                </div>
        </div>
			  
	<form id='foto' method="post" action="{{url('users/update-image')}}" method="POST" enctype="multipart/form-data" >
	@csrf
	    <div style="height:0px;overflow:hidden"> 
        <input type="file" id="picField" name="picField" onchange="this.form.submit()" class="d-none"/> 
        <input type="hidden" id="scrId" name="scrId" /> 
        </div>
      
        <!--<i class='fa fa-camera' onclick="fileInput.click();"></i>-->
    </form> 
			  
			 

<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="add-link" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
	  <div class="offcanvas-header border-bottom">
		<h5 class="offcanvas-title" id="offcanvasScrollingLabel">Add gl-link</h5>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
	  </div>
		<div class="offcanvas-body">



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

   $(document).on('click','#imgUpload',function(e){
        $('#picField').trigger('click');
        scrId = $(this).data('id')
        $('#scrId').val(scrId);
    })

BASE_URL ={!! json_encode(url('/')) !!}

 var table1 = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging : true,
        pageLength :50,

		'pagingType':"simple_numbers",
        'lengthChange': true,

		ajax:
		{
			url:BASE_URL+"/users/view-campaign-customers",
			data: function (data) 
		    {
               data.campaign_id = $('#campaign_id').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "name" },
			{"data": "mobile" },
			{"data": "email" },
			{"data": "created" },
			{"data": "branch" },
			{"data": "offer" },
			{"data": "status" },
			{"data": "redeem" },
        ],
		
		initComplete: function (settings, json) {
        var total=table1.page.info().recordsTotal;
		$("#web_count").html(total);
		},
    });
	
	
	
var table2 = $('#datatable_app').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging : true,
        pageLength :50,

		'pagingType':"simple_numbers",
        'lengthChange': true,

		ajax:
		{
			url:BASE_URL+"/users/view-campaign-app-customers",
			data: function (data) 
		    {
               data.campaign_id = $('#campaign_id').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "name" },
			{"data": "mobile" },
			{"data": "email" },
			{"data": "created" },
			{"data": "branch" },
			{"data": "offer" },
			{"data": "status" },
			{"data": "redeem" },
        ],
		
		initComplete: function (settings, json) {
        var total=table2.page.info().recordsTotal;
		$("#app_count").html(total);
		},
    });


$(document).on('click','.link-add',function()
{
	var Result=$("#add-link .offcanvas-body");

		var id=$(this).attr('id');
			jQuery.ajax({
			type: "GET",
			url: "{{url('users/add-link')}}",
			dataType: 'html',
			data: {offer_id:id},
			success: function(res)
			{
			   Result.html(res);
			}
		});
});




</script>
@endpush
@endsection
