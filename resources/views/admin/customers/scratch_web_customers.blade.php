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
</style>

		<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">Scratch Web Customers</div>
 
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
				     <a href="javascript:;" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" ><i class="lni lni-funnel"></i></a>
				  </div>
				  </div>
                </div>
                <div class="card-body">
				
					<div class="accordion-item accordion-item-bm" >
                        <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" >
                         <div class="accordion-body">
						  <label style="font-weight:500;padding:5px 10px;" > Filter By: </label>
						  
						  
						  <form method="POST" id="export_redeem_history"  action="{{url('admin/export-customers-list')}}" enctype="multipart/form-data" >
							@csrf 
						  
						   <div class="row" style="padding:3px 10px 10px 10px;" >
							<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
								<label>Start Date</label>
								<input type="date" id="start_date" name="start_date" style="content:attr(placeholder)! important;" class="form-control" placeholder="strting date" required>
							</div>
							
							<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
								<label>End Date</label>
								<input type="date" id="end_date" name="end_date" class="form-control" placeholder="End Date" required>
							</div>
							
							<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
								<label>Users</label>
								<select id="user_id" name="user_id" class="form-control" >
                                 <option value="">Select User</option>
                                 @foreach($users as $usr)
                                 <option value="{{ $usr->id }}">{{ $usr->vchr_user_name }}</option>
                                 @endforeach
								</select>
							</div>
							
							<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
								<label>Branch</label>
								<select id="branch" name="branch" class="form-control" >
                                 <option value="">Select Branch</option>
                                 @foreach($branches as $branche)
                                 <option value="{{ $branche->id }}">{{ $branche->branch_name }}</option>
                                 @endforeach
								</select>
							</div>
							
							<div class="col-3 col-lg-3 col-xl-3 col-xxl-3">
								<label>Campaign</label>
								<select id="campaign" name="campaign" class="form-control" >
                                 <option value="">Select Campaign</option>
                                 @foreach($offers as $campaign)
                                 <option value="{{ $campaign->pk_int_scratch_offers_id }}">{{ $campaign->vchr_scratch_offers_name }}</option>
                                 @endforeach
                              </select>
							</div>
							</div>
							<div class="row text-right">
							<div class="col-12 col-lg-12 col-xl-12 col-xxl-12" style="padding:5px 50px 15px 10px;">
							<button type="button" class="btn btn-primary btn-xs" id="btn-filter" > <i class="lni lni-funnel"></i> Filter</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-secondary btn-xs me-2" id="btn-clear-filter" > Clear</button>&nbsp;&nbsp;
							<button type="submit" class="btn btn-secondary btn-xs"  > <i class="lni lni-download"></i> Download</button>
							</div>

						   </div>

						   </form>
						   
						</div>
					  </div>
				</div>

				
				<!---  filer end ----------------------------------------->
				<div class="row mt-3">
                     <div class="col-12 col-lg-12  text-right">
					 <label>Web Total : <span style="font-weight:600;" id="web_count"></span></label>
					 &nbsp;|&nbsp;<label class="ms-1">App Total : <span style="font-weight:600;" id="app_count"></span></label>
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

						  <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark">
								<tr>
									<th>Sl No</th>
									<th>Created At</th>
									<th>Unique Id</th>
									<th>Name</th>
									<th>Mobile No</th>
									<th>Email</th>
									<th>Bill No</th>
									<th>Branch</th>
									<th>Redeemed By</th>
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
					  <!-- TAB Pane 2 --------------->
						  <div class="tab-pane fade" id="primaryprofile" role="tabpanel">
						  
						  <div class="table-responsive">
	
                             <table id="datatable_app" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark" >
								<tr>
									<th>Sl No</th>
									<th>Created At</th>
									<th>Unique Id</th>
									<th>Name</th>
									<th>Mobile No</th>
									<th>Email</th>
									<th>Bill No</th>
									<th>Branch</th>
									<th>Redeemed By</th>
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


var table1 = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging     : true,
        pageLength :50,
				
		'pagingType':"simple_numbers",
        'lengthChange': true,
				
		ajax:
		{
			url:BASE_URL+"/admin/get-scratch-web-customers",
			data: function (data) 
		    {
               data.start_date = $('#start_date').val();
               data.end_date = $('#end_date').val();
			   data.user_id = $('#user_id').val();
               data.branch = $('#branch').val();
               data.campaign = $('#campaign').val();
		    },
        },
		columns: [
		   {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
		   {data: 'created_at', name: 'created_at'},
		   {data: 'unique_id', name: 'unique_id'},
		   {data: 'name', name: 'name'},
		   {data: 'mobile', name: 'mobile'},
		   {data: 'email', name: 'email'},
		   {data: 'billno', name: 'bill_no'},
		   {data: 'branch', name: 'branch'},
		   {data: 'agent', name: 'agent'},
		   {data: 'offer_text', name: 'offer_text'},
		   {data: 'status', name: 'status'},
		   {data: 'show', name: 'show', orderable: false, searchable: false}
	   ],
	   
	   initComplete: function (settings, json) {
        var total=table1.page.info().recordsTotal;
		$("#web_count").html(total);
		
    }

});


var table2 = $('#datatable_app').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging     : true,
        pageLength :50,
		
		'pagingType':"simple_numbers",
        'lengthChange': true,
				
		ajax:
		{
			url:BASE_URL+"/admin/get-scratch-app-customers",
			data: function (data) 
		    {
               data.start_date = $('#start_date').val();
               data.end_date = $('#end_date').val();
               data.branch = $('#branch').val();
			   data.user_id = $('#user_id').val();
               data.campaign = $('#campaign').val();
		    },
        },
		columns: [
		   {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
		   {data: 'created_at', name: 'created_at'},
		   {data: 'unique_id', name: 'unique_id'},
		   {data: 'name', name: 'name'},
		   {data: 'mobile', name: 'mobile'},
		   {data: 'email', name: 'email'},
		   {data: 'billno', name: 'bill_no'},
		   {data: 'branch', name: 'branch'},
		   {data: 'agent', name: 'agent'},
		   {data: 'offer_text', name: 'offer_text'},
		   {data: 'status', name: 'status'},
		   {data: 'show', name: 'show', orderable: false, searchable: false}
	   ],
	   
	   initComplete: function (settings, json) {
        var total=table2.page.info().recordsTotal;
		$("#app_count").html(total);
		
    }

});



$("#btn-filter").click(function()
{
	$('#datatable').DataTable().ajax.reload(function (json) {
		$("#web_count").html(json.recordsTotal);
	});
	
	$('#datatable_app').DataTable().ajax.reload(function (json) {
		$("#app_count").html(json.recordsTotal);
	});

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
