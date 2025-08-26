@extends('layouts.master')
@section('title','Gl-links')
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

.select2-selection--single
{
	height: 38px !important;
    border: 1px solid #dfdbdb !important;
    padding: 3px 3px !important;		
}
.select2-selection__arrow
{
	margin-top:4px;
}
</style>

		<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">Scratch Customers</div>
 
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

              <div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-users"></i> Customers List</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				@if((Auth::user()->int_role_id==1 and Auth::user()->admin_status==1) OR (Auth::user()->parent_user_id==""))
				     <a href="javascript:;" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" ><i class="lni lni-funnel"></i></a>
				@endif
				  </div>
				  </div>
                </div>
                <div class="card-body">
				
					<div class="accordion-item accordion-item-bm" >
                        <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" >
                         <div class="accordion-body">
						 <!-- <label style="font-weight:500;padding:5px 10px;" > Filter By: </label>-->

						  
						<form method="POST" id="exportCustomer"  action="{{url('users/export-web-customers-list')}}" enctype="multipart/form-data" >
						 @csrf 
						  
						<div class="row" style="padding:0px 10px 10px 10px;" >
							@if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)							
							<div class="col-12 col-lg-3 col-xl-3 col-xxl-3">
								<label class="mt-2" style="width:150px;font-weight:500;">Filter Branch</label>
								<select id="branch_user" name="branch_user" class="form-control" >
                                 <option value="">Select Branch</option>
								  @foreach($branches as $row)
									<option value="{{ $row->pk_int_user_id}}">{{ $row->vchr_user_name }}</option>
								  @endforeach
								</select>
							</div>
							@elseif(Auth::user()->parent_user_id=="")
								
							<div class="col-12 col-lg-3 col-xl-3 col-xxl-3">
								<label class="mt-2" style="width:150px;font-weight:500;">Filter Branch</label>
								<select id="branch_user" name="branch_user" class="form-control" >
                                 <option value="">Select Branch</option>
								  @foreach($branches as $row)
									<option value="{{ $row->id}}">{{ $row->branch_name }}</option>
								  @endforeach
								</select>
							</div>
							
							@endif
							<div class="col-12 col-lg-9 col-xl-9 col-xxl-9" style="padding:5px 10px 10px 10px;">
								
								
								<div class="row" style="padding:3px 10px 10px 10px;" >
									<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
										<label>Start Date</label>
										<input type="date" id="start_date" name="start_date" style="content:attr(placeholder)! important;" class="form-control" placeholder="strting date" >
									</div>
									
									<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
										<label>End Date</label>
										<input type="date" id="end_date" name="end_date" class="form-control" placeholder="End Date" >
									</div>

									<!--<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
										<label>Search Any Data</label>
										<input type="text" id="global_search" name="global_search" class="form-control" placeholder="Search data" >
									</div> -->

									<div class="col-3 col-lg-3 col-xl-3 col-xxl-3" style="padding-top:22px;">
									<button type="button" class="btn btn-primary btn-xs" id="btn-filter" > <i class="lni lni-funnel"></i> Filter</button>&nbsp;&nbsp;
									<button type="button" class="btn btn-secondary btn-xs me-2" id="btn-clear-filter" > Clear</button>&nbsp;&nbsp;
									<button type="button" class="btn btn-secondary btn-xs" id="btn_download" > <i class="lni lni-download"></i> Download</button>
									</div>

								</div>

							</div>
						</div>
					</form>
						   
						</div>
					  </div>
				</div>

				
				<!---  filer end ----------------------------------------->
				<div class="row mt-3">
					 <div class="col-12 col-lg-8 col-xl-8 col-xxl-8">
						<p style="color:blue;">To display last 3 months data only. You can display custom range of data, use filters.</p>
					 </div>
                     <div class="col-12 col-lg-4 col-xl-4 col-xxl-4 text-right">
					 <label>Total : <span style="font-weight:600;" id="web_count"></span></label>
					 
					 </div>
				</div>
	
                <div class="row">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
					  
						<ul class="nav nav-tabs nav-primary mt-2" role="tablist">
							<li class="nav-item" role="presentation">
								<a class="nav-link active" data-bs-toggle="tab" href="#primaryhome" role="tab" aria-selected="false" tabindex="-1">
									<div class="d-flex align-items-center">
										<div class="tab-icon"><i class="bx bx-user-pin font-18 me-1"></i>
										</div>
										<div class="tab-title">Customers List</div>
									</div>
								</a>
							</li>

						</ul>
					  <div class="tab-content py-3">
						<div class="tab-pane fade show active" id="primaryhome" role="tabpanel">

						  <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark" >
								<tr>
									<th>Sl No</th>
									<th>User_Id</th>
									<th>Created At</th>
									<th>Unique Id</th>
									<th>Name</th>
									<th>Mobile No</th>
									<th>Email</th>
									<th>Bill No</th>
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
						  
						  </div>
					  </div>

					  
                        <!--<div class="card-body">-->
                          

                       <!-- </div>-->
                      </div> 
                    </div>
                   </div><!--end row-->
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

$("#branch").select2();
$("#campaign").select2();


var table2 = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging     : true,
        pageLength :50,
		
		'pagingType':"simple_numbers",
        'lengthChange': true,
				
		ajax:
		{
			url:BASE_URL+"/users/get-scratch-web-customers",
			data: function (data) 
		    {
               data.branch_user = $('#branch_user').val();
			   data.start_date = $('#start_date').val();
			   data.end_date = $('#end_date').val();
			   data.global_search = $('#global_search').val();
		    },
        },
		columns: [
		   {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
		   {data: 'user_id', name: 'user_id'},
		   {data: 'created_at', name: 'created_at'},
		   {data: 'unique_id', name: 'unique_id'},
		   {data: 'name', name: 'name'},
		   {data: 'mobile', name: 'mobile'},
		   {data: 'email', name: 'email'},
		   {data: 'billno', name: 'bill_no'},
		   {data: 'branch', name: 'branch'},
		   {data: 'offer_text', name: 'offer_text'},
		   {data: 'status', name: 'status'},
		   {data: 'show', name: 'show', orderable: false, searchable: false}
	   ],
	   
	   initComplete: function (settings, json) {
        var total=table2.page.info().recordsTotal;
		$("#web_count").html(total);
		
    }

});

$("#branch_user").change(function()
{
	$('#datatable').DataTable().ajax.reload(function (json) {
		$("#app_count").html(json.recordsTotal);
	});
	
});

$("#btn-filter").click(function()
{
	$('#datatable').DataTable().ajax.reload(function (json) {
		$("#web_count").html(json.recordsTotal);
	});

});

$("#btn_download").click(function()
{
	var brna=$("#branch_user").val();
	var sdt=$("#start_date").val();
	var edt=$("#end_date").val();
	if(brna!="")
	{
		$("form#exportCustomer").submit();
	}	
	else if(sdt!="" && edt!="")
	{
		$("form#exportCustomer").submit();
	}
	else
	{
		alert("Please set branch or start & end dates.!")
	}

});

$('#datatable').on('click', '.scratch-web-redeem', function (event) {
           event.preventDefault();
           var customer_id = $(this).attr('customer-id');

           var url = BASE_URL + '/user/scratch-web-redeem/' + customer_id;
   
			Swal.fire({
				  //title: "Are you sure?",
				  text: "Are you sure, You want to redeem now?",
				  icon: "question",
				  showCancelButton: true,
				  confirmButtonColor: "#3085d6",
				  cancelButtonColor: "#d33",
				  confirmButtonText: "Yes, Redeem it!"
				}).then((result) => {
				  if (result.isConfirmed) {
					
					var tid=$(this).attr('id');
					
					  $.ajax({
					  url: BASE_URL + '/users/scratch-web-redeem/' + customer_id,
					  type: 'get',
					  dataType: 'json',
					  //data:{'track_id':tid},
					  success: function (res) 
					  {
						if(res.status==1)
						{
							 toastr.success(res.msg);
							 $('#datatable').DataTable().ajax.reload(null,false);
						}
						else
						{
							 toastr.error(res.msg);
						}
					  }
					});

				  }
				});
       });


$(document).on('click','#btn-clear-filter',function()
{
	
	location.reload();


});


/*

$("#export-to-excel").click(function()
{
	var sdt=new Date($("#start_date").val());
	var edt=new Date($("#end_date").val());
		
	if(sdt!="" && edt!="")
	{
		
		var sDate = [sdt.getFullYear(), sdt.getMonth() + 1,sdt.getDate()].join('-');
		var eDate = [edt.getFullYear(), edt.getMonth() + 1,edt.getDate()].join('-');
		
		var lnk="{{url('users/export-web-customers-list')}}"+"/"+sDate+"/"+eDate;
	    $("#export-to-excel").attr('href',lnk);	
	}
	else
	{
		var sDate="0";
		var eDate="0";
		var lnk="{{url('users/export-web-customers-list')}}"+"/"+sDate+"/"+eDate;
	    $("#export-to-excel").attr('href',lnk);
	}
});
*/


</script>
@endpush
@endsection
