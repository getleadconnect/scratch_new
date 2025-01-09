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
              <div class="breadcrumb-title pe-3">Bill</div>
 
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
				   <h6 class="mb-0 pt5 mt-2"> &nbsp;</h6>
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
					  
					  <div class="row">
					  <div class="col-lg-4 col-xl-4 col-xxl-4">
					  
							  <h6 class="mb-3"> Add Bill Number </h6>
							  
							<!--  <form method="POST" id="SaveAdsImage" action="{{url('users/save-ads-image')}}"enctype="multipart/form-data">--->
							
							<form id="formAddBill" enctype="multipart/form-data">
								@csrf
							  <div class="mb-2 row">
									
									<div class="col-lg-11 col-xl-11 col-xxl-11 mt-2">
										<label for="example-text-input" class="col-form-label">Select Offer</label>
										<select class="form-select" name="offer_id" required>
										<option value=''>--select--</option>
										<option value='0'>All</option>
										@foreach($offers as $row)
											<option value='{{$row->pk_int_scratch_offers_id}}'>{{$row->vchr_scratch_offers_name}}</option>
										@endforeach
										</select>
										</div>
																  
									<div class="col-lg-11 col-xl-11 col-xxl-11 mt-2">
										<label for="example-text-input" class="col-form-label">Bill Number</label>
										<input class="form-control" type="number" name="bill_number" multiple required>
										</div>
							
									<div class="col-lg-11 col-xl-11 col-xxl-11 mt-3">
									<button type="submit" id="btnBillSave" class="btn btn-primary">Save Bill </button>
									</div>
							  </div>
							  </form>
					  </div>
					  
					  <div class="col-lg-8 col-xl-8 col-xxl-8">

                        <!--<div class="card-body">-->
                          <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark">
								<tr>
									<th>Sl No</th>
									<th>Bill</th>
									<th>Offer</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
                               </thead>
                               <tbody>
                                  
                               </tbody>
                             </table>
                          </div>
						  
						  </div>
						  </div>
                       <!-- </div>-->
                      </div> 
                    </div>
                   </div><!--end row-->
                </div>
              </div>
			  
		<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="edit-scratch-bill" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Edit Bill</h5>
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

BASE_URL ={!! json_encode(url('/')) !!}


var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging     : true,
        pageLength :50,
		scrollX: true,
		
		'pagingType':"simple_numbers",
        'lengthChange': true,
				
		ajax:
		{
			url:BASE_URL+"/users/view-bills",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
		    },
        },
		columns: [
		   {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
		   {data: 'bill', name: 'bill'},
		   {data: 'offer_name', name: 'offer_name'},
		   {data: 'status', name: 'status'},
		   {data: 'action', name: 'action', orderable: false, searchable: false}
	   ],

});



 $(document).on('submit', '#formAddBill', function(event) {
           event.preventDefault();

			$.ajax({
                    url: BASE_URL + '/users/save-bill',
                    type: 'POST',
                    dataType: 'json',
                    data:  new FormData(this),
                    contentType: false,
                    processData:false,
                })
                .done(function(res) {
                  if(res.status == true){
                    toastr.success(res.msg);
					$('#datatable').DataTable().ajax.reload(null, false);
					$("#formAddBill")[0].reset();
                }else{
                   toastr.error(res.msg);
                }
                })
                .fail(function() {
                })
                 .always(function(com) {
				 
            });

       });
	   


$('#datatable tbody').on('click','.bill-delete',function()
{
	Swal.fire({
	  //title: "Are you sure?",
	  text: "Are you sure, You want to delete this bill?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, Delete it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		var tid=$(this).attr('id');
		
		  $.ajax({
          url: "{{url('users/delete-bill')}}"+'/'+tid,
          type: 'get',
		  dataType: 'json',
          //data:{'track_id':tid},
          success: function (res) 
		  {
			if(res.status==1)
			{
				 toastr.success(res.msg);
				 $("#datatable").DataTable().ajax.reload(null,false);
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


$('#datatable tbody').on('click','.bill-edit',function()
{

	var id=$(this).attr('id');
	var Result=$("#edit-scratch-bill .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/edit-bill')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});

});


 $(document).on('submit', '#formEditBill', function(event) {
           event.preventDefault();

			$.ajax({
                    url: BASE_URL + '/users/update-bill',
                    type: 'POST',
                    dataType: 'json',
                    data:  new FormData(this),
                    contentType: false,
                    processData:false,
                })
                .done(function(res) {
                  if(res.status == true){
                    toastr.success(res.msg);
					$('#datatable').DataTable().ajax.reload(null, false);
					$("#formEditBill")[0].reset();
                }else{
                   toastr.error(res.msg);
                }
                })
                .fail(function() {
                })
                 .always(function(com) {
				 
            });

       });


$("#datatable tbody").on('click','.btn-act-deact',function()
{
	var opt=$(this).data('option');
	var id=$(this).attr('id');
	
	var opt_text=(opt==1)?"activate":"deactivate";
	optText=opt_text.charAt(0).toUpperCase()+opt_text.slice(1);
	
	Swal.fire({
	  title: optText+"?",
	  text: "You want to "+opt_text+" this bill?",
	  icon: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, "+opt_text+" it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		
		  jQuery.ajax({
			type: "get",
			url: BASE_URL+"/users/act-deact-bill/"+opt+"/"+id,
			dataType: 'json',
			//data: {vid: vid},
			success: function(res)
			{
			   if(res.status==true)
			   {
				   toastr.success(res.msg);
				   $('#datatable').DataTable().ajax.reload(null, false);
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
