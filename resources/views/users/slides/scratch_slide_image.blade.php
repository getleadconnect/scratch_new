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
              <div class="breadcrumb-title pe-3">Scratch Slide Images</div>
 
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
					  <div class="col-lg-5 col-xl-5 col-xxl-5">
					  
							  <h6 class="mb-3"> Add Slide Image</h6>
							  
							<!--  <form method="POST" id="SaveAdsImage" action="{{url('users/save-ads-image')}}"enctype="multipart/form-data">--->
							
							<form id="SaveSlideImage" enctype="multipart/form-data">
								@csrf
							  <div class="mb-2 row">
																  
									<div class="col-lg-11 col-xl-11 col-xxl-11 mt-2">
										<label for="example-text-input" class="col-form-label">Select Image ((Max:500mb, Size 385x555) </label>
										<input class="form-control" type="file" name="image_file" id="image_file" required>
										</div>
									<div class="col-lg-11 col-xl-11 col-xxl-11">
									<img class="mb-2 mt-2" id="img_output" src="" style="width:150px;">
									</div>
									
							
									<div class="col-lg-11 col-xl-11 col-xxl-11 mt-3">
									<button type="submit" id="btnSave" class="btn btn-primary">Save Image </button>
									</div>
							  </div>
							  </form>
					  </div>
					  
					  <div class="col-lg-7 col-xl-7 col-xxl-7">

                        <!--<div class="card-body">-->
                          <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark">
								<tr>
									<th>Sl No</th>
									<th>Image</th>
									<th>Created_By</th>
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
			url:BASE_URL+"/users/get-slide-images",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
		    },
        },
		columns: [
		   {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
		   {data: 'image', name: 'image'},
		   {data: 'created_by', name: 'created_by'},
		   {data: 'action', name: 'action', orderable: false, searchable: false}
	   ],

});


image_file.onchange = evt => {
  const [file] = image_file.files

	var size=file.size;
	if(size>524288)
	{
		alert("Image size too large. Maximum 500Kb only");
		$(this).val('');
	}
	else
	{

        var allowedExtensions="";
	    allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
	    var filePath = file.name;
		console.log(file);
	
		if (!allowedExtensions.exec(filePath)) { 
			alert('Invalid file type, Try again.'); 
			$("#ads_image").val('');
			$("#img_output").prop('src','');
		}
		else
		{
			if (file) {
				img_output.src = URL.createObjectURL(file)
			  }
		}  
	}
}

 $(document).on('submit', '#SaveSlideImage', function(event) {
           event.preventDefault();
		   
		   alert("ok");
			$.ajax({
                    url: BASE_URL + '/users/save-slide-image',
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
					 ("#SaveSlideImage")[0].reset();
					
                }else{
                   toastr.error(res.msg);
                }
                })
                .fail(function() {
                })
                 .always(function(com) {
            });
       });


$('#datatable tbody').on('click','.slide-delete',function()
{
	Swal.fire({
	  //title: "Are you sure?",
	  text: "Are you sure, You want to delete this ads?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, Delete it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		var tid=$(this).attr('id');
		
		  $.ajax({
          url: "{{url('users/delete-slide-image')}}"+'/'+tid,
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


</script>
@endpush
@endsection
