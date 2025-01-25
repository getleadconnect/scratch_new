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
              <div class="breadcrumb-title pe-3">Branches</div>
 
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
					  
							  <h6 class="mb-3"> Add Branch </h6>
							  
							<!--  <form method="POST" id="SaveAdsImage" action="{{url('users/save-ads-image')}}"enctype="multipart/form-data">--->
							
							<form id="formAddBranch" enctype="multipart/form-data">
								@csrf
							  <div class="mb-2 row">
									
									
									<div class="col-lg-11 col-xl-11 col-xxl-11 mt-2">
										<label for="example-text-input" class="col-form-label">Branch </label>
										<input class="form-control" type="text" name="branch" required>
										</div>
							
									<div class="col-lg-11 col-xl-11 col-xxl-11 mt-3">
									<button type="submit" id="btnBillSave" class="btn btn-primary">Save Branch </button>
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
									<th>Branch Name</th>
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
	  
	  
	<div class="modal fade" id="edit-branch" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Edit</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<form id="formEditBranch" enctype="multipart/form-data">
				@csrf
			
			<div class="modal-body">
				<input class="form-control" type="text" name="branch_id" id="branch_id" required>
				
			  <div class="mb-2 row">
					<div class="col-lg-11 col-xl-11 col-xxl-11 mt-2">
						<label for="example-text-input" class="col-form-label">Branch </label>
						<input class="form-control" type="text" name="branch_edit" id="branch_edit" required>
						</div>
			  </div>
			 
						
			<div class="modal-footer">
				<div class="col-lg-12 col-xl-12 col-xxl-12 text-end">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
					<button class="btn btn-primary" type="submit">Update</button>
				</div>
			</div>
			
			</form>
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
			url:BASE_URL+"/users/view-branches",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
		    },
        },
		columns: [
		   {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
		   {data: 'name', name: 'name'},
		   {data: 'status', name: 'status'},
		   {data: 'action', name: 'action', orderable: false, searchable: false}
	   ],

});



 $(document).on('submit', '#formAddBranch', function(event) {
           event.preventDefault();

			$.ajax({
                    url: BASE_URL + '/users/save-branch',
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
					$("#formAddBranch")[0].reset();
                }else{
                   toastr.error(res.msg);
                }
                })
                .fail(function() {
                })
                 .always(function(com) {
				 
            });

       });
	   


$('#datatable tbody').on('click','.delete-branch',function()
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
          url: "{{url('users/delete-branch')}}"+'/'+tid,
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


$('#datatable tbody').on('click','.edit-branch',function()
{

	var id=$(this).attr('id');
	var branch=$(this).data('branch');
	
	$("#branch_id").val(id);
	$("#branch_edit").val(branch);
	
});


 $(document).on('submit', '#formEditBranch', function(event) {
           event.preventDefault();

			$.ajax({
                    url: BASE_URL + '/users/update-branch',
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
					$("#formEditBranch")[0].reset();
					$("#edit-branch").modal('hide');
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
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, "+opt_text+" it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		
		  jQuery.ajax({
			type: "get",
			url: BASE_URL+"/users/act-deact-branch/"+opt+"/"+id,
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
