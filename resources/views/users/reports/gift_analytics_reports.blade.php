@extends('layouts.master')
@section('title','Reports')
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
              <div class="breadcrumb-title pe-3">Analytics Report</div>
 
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
				   <h6 class="mb-0 pt5 mt-2">Gift Analytics &nbsp;</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				     <a href="{{url('users/export-analytics-report')}}" class="btn btn-gl-primary"><i class="lni lni-upload"></i>&nbsp;Export to Excel</a>
				  </div>
				  </div>
                </div>
                <div class="card-body">

                   <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100 mt-2">
					  
	                    <!--<div class="card-body">-->
                          <div class="table-responsive">
	
							<table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark">
								<tr>
									<th>Sl No</th>
									<th>User Id</th>
									<th>Delear Code</th>
									<th>Mobile</th>
									<th>Total Gift</th>
									<th>Total Used</th>
									<th>Balance</th>
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
			url:BASE_URL+"/users/view-gift-analytics",
			data: function (data) 
		    {
               //data.start_date = $('#start_date').val();
 		    },
        },
		columns: [
		   {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
		   {data: 'user_id', name: 'user_id'},
		   {data: 'name', name: 'name'},
		   {data: 'mobile', name: 'mobile'},
		   {data: 'total_gift_count', name: 'total_gift_count'},
		   {data: 'used_gift', name: 'used_gift'},
		   {data: 'total_gift_balance', name: 'total_gift_balance'},
	   ],
	   
	  /* initComplete: function (settings, json) {
        var total=table1.page.info().recordsTotal;
		$("#web_count").html(total);
	  }
		*/
});


</script>
@endpush
@endsection
