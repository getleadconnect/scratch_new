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
</style>

<!-- for message -------------->
		<input type="hidden" id="view_message" value="{{ Session::get('message') }}">
<!-- for message end-------------->	

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">App Customers Redeem History</div>
 
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
                  <h6 class="mb-0 pt5"><i class="fa fa-users"></i> Customers List</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				  <button class="btn btn-gl-primary btn-xs btn-sm btn-p-filter" type="button " data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" style="padding-left:3px;" title="Filter"  aria-expanded="true" aria-controls="flush-collapseOne">
                      &nbsp;<i class="lni lni-funnel"></i> Filter
                  </button>&nbsp;
				    <!-- <a href="{{route('users.add-campaign')}}" class="btn btn-gl-primary btn-xs" ><i class="fa fa-plus"></i>&nbsp;Add Campaign</a>-->
				  </div>

				  </div>
                </div>
                <div class="card-body">
					<div class="accordion-item accordion-item-bm mb-2" >
                        <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" >
                          <div class="accordion-body">
						  <label style="font-weight:500;padding:5px 10px;" > Filter By: </label>
						  
						  
						  <form method="POST" id="export_redeem_history"  action="{{url('users/export-customers-list')}}" enctype="multipart/form-data" >
							@csrf 
						  
						   <div class="row" style="padding:3px 10px 5px 10px;" >
							<div class="col-3 col-lg-3">
								<label>Start Date</label>
								<input type="date" id="start_date" name="start_date" style="content:attr(placeholder)! important;" class="form-control" placeholder="strting date" required>
							</div>
							
							<div class="col-3 col-lg-3">
								<label>End Date</label>
								<input type="date" id="end_date" name="end_date" class="form-control" placeholder="End Date" required>
							</div>
							<div class="col-3 col-lg-3">
								<label>Branch</label>
								<select id="branch" name="branch" class="form-control" >
                                 <option value="">Select Branch</option>
                                 @foreach($branches as $branche)
                                 <option value="{{ $branche->id }}">{{ $branche->branch }}</option>
                                 @endforeach
								</select>
							</div>
							
							<div class="col-3 col-lg-3">
								<label>Campaign</label>
								<select id="campaign" name="campaign" class="form-control" >
                                 <option value="">Select Campaign</option>
                                 @foreach($offers as $campaign)
                                 <option value="{{ $campaign->pk_int_scratch_offers_id }}">{{ $campaign->vchr_scratch_offers_name }}</option>
                                 @endforeach
                              </select>
							</div>
							
						   </div>
						   <div class="row" style="padding:3px 10px 15px 10px;">
						   <div class="col-lg-12 col-xl-12 col-xxl-12 text-right">
							<button type="button" class="btn btn-secondary btn-xs me-2" id="btn-clear-filter" > Clear</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-secondary btn-xs" id="btn-filter" > <i class="lni lni-funnel"></i> Filter</button>&nbsp;&nbsp;
							<button type="submit" class="btn btn-secondary btn-xs" id="btn-filter" > <i class="lni lni-download"></i> Download</button>
							</div>
						   </div>
						   </form>
						   
						</div>
					  </div>
					</div>
				
                   <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
                        <!--<div class="card-body">-->
                          <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="table-light">
                                 <tr>
									<th>SlNo</th>
									<th>Name</th>
									<th>Mobile</th>
									<th>Date</th>
									<th>Bill No</th>
									<th>Redeem ID</th>
									<th>Campaign</th>
									<th>Branch</th>
									<th>Gift</th>
									<th>Details</th>
									<th>Type</th>
									<th class="no-content" style="width:50px;">Redeem</th>
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

var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging    : true,
        pageLength :50,
		scrollX: true,
		
		'pagingType':"simple_numbers",
        'lengthChange': true,
			
		ajax:
		{
			
			url:"view-redeem-history",
			data: function (d) 
		    {
               d.start_date = $('#start_date').val();
               d.end_date = $('#end_date').val();
               d.branch = $('#branch').val();
               d.campaign = $('#campaign').val();
			},
        },

   columns: [
           { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
           { data: 'name', name: 'name'},
           { data: 'mobileno', name: 'mobileno'},
           { data: 'created_at',name: 'created_at'},
           { data: 'billno', name: 'billno'},
           { data: 'unique_id',name: 'unique_id'},
           { data: 'campaign', name: 'campaign'},
           { data: 'branch', name: 'branch'},
           { data: 'offer_text', name: 'offer_text'},
           { data: 'details', name: 'details'},
           { data: 'type_name', name: 'type_name'},
           { data: 'action', name: 'action', orderable: false, searchable: false }
           ],
		
});

$('#btn-filter').on('click',function(){
    $('#datatable').DataTable().ajax.reload(null, false);
 });

$('#btn-clear-filter').on('click',function(){
		 $('#start_date').val('');
         $('#end_date').val('');
         $('#branch').val('');
         $('#campaign').val('');
         $('#datatable').DataTable().ajax.reload(null, false);
       }); 	   

//testimonial-act
$('#datatable').on('click', '.offer-redeem', function(event) {
           event.preventDefault();
           if($(this).hasClass('btn-success')) {
               var url = BASE_URL + '/users/scratch-offer-deactivate/';
               action = 'Deactivate';
           } else {
               var url = BASE_URL + '/users/scratch-offer-redeem/';
               action = 'Redeem';
           }
		   
           var custid = $(this).attr('data-customerid');
           url = url + custid;
		   Swal.fire({
			  //title: "Are you sure?",
			  text: 'Are you sure, You want to ' + action + '?',
			  icon: 'question',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Yes, '+action +' it!'
			}).then((result) => {
			  if (result.isConfirmed) {

					$.ajax({
					  url: url,
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

	

</script>
@endpush
@endsection
