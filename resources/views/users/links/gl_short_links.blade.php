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
.qrcode-icon:hover
{
	border:1px solid blue;
}
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">GL-Links</div>
 
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
				<div class="col-lg-6 col-xl-6 col-xxl-6 col-6">
                  <h6 class="mb-0 pt5"><i class="fa fa-link"></i> Web links</h6>
				  </div>
				  <div class="col-lg-6 col-xl-6 col-xxl-6 col-6 text-right">

				  <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->pk_int_user_id}}">
				  
				  <button class="btn btn-gl-primary btn-xs" id="delele_multiple" ><i class="fa fa-trash"></i>&nbsp;Delete</button>&nbsp;&nbsp;

					 <button class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#gen-pdf-modal" ><i class="fa fa-qrcode"></i>&nbsp;Qr-Code PDF</button>
					 &nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs link-multiple" data-bs-toggle="offcanvas" data-bs-target="#add-multiple-links"><i class="fa fa-plus"></i>&nbsp;Add Multiple Links</button>
				     &nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs link-add" data-bs-toggle="offcanvas" data-bs-target="#add-link"><i class="fa fa-plus"></i>&nbsp;Add Link</button>
				  				
				  </div>

				  </div>
                </div>
                <div class="card-body">
			
                   <div class="row mt-2">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
                        <!--<div class="card-body">-->
                          <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark">
                                <tr>
								<th style="width:30px;"><input type="checkbox" id="chkbox-all" class="dt-checkboxes chkbox-all" name="chkbox_all"></th>
                                <th>Sl No</th>
                                <th>Offer Name</th>
                                <th>Link</th>
								<th>QrCode</th>
                                <th>Code</th>
								<th><span>Email</span> <p style="color:#2727e9;font-size:11px;margin:0px;">(Required)</p></th>
								<th>BillNo <p style="color:#2727e9;font-size:11px;margin:0px;">(Required)</p></th>
								<th>Shop <p style="color:#2727e9;font-size:11px;margin:0px;">(Required)</p></th>
                                <th>Click Count</th>
								 <th>Status</th>
                                <th class="no-content" style="width:50px;">Action</th>
                            </tr>
									
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
					
	
	
	
	<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="edit-link" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Edit gl-link</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
			<div class="offcanvas-body">
  
            </div>
    </div>
	
	
<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="add-link" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Add gl-link</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
			<div class="offcanvas-body">
  
  

            </div>
</div>
	
	
<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="add-multiple-links" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Generate multiple links</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
			<div class="offcanvas-body">
  
  
            </div>
    </div>
	
		
	<div class="modal fade" id="gen-pdf-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Generate Qr-Code PDF</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				
				<div class="modal-body">
				<form method="Post" action="{{route('users.generate-qrcode-pdf')}}" enctype="multipart/form-data">
				@csrf
				
				<input type="hidden" name="user_id" id="user_id" value="{{$user_id}}">
				
				<div class="mb-2 row">
					<div class="col-lg-12 col-xl-12 col-xxl-12">
					<label for="example-text-input" class="col-form-label">Select Campaign</label>
					<select class="form-select" name="offer_id" id="offer_id" required>
						<option value="" >select</option>
						@foreach($offers as $row)
							<option value="{{$row->pk_int_scratch_offers_id}}" >{{$row->vchr_scratch_offers_name}}</option>
						@endforeach
					  </select>
					
					</div>
				</div>
				
				<div class="modal-footer mt-5">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"> Close </button>
					<button type="submit" class="btn btn-primary" name="btn-submit"> Download PDF </button>
				</div>
				</form>
				
				</div>
			</div>
		</div>
	</div>

<div class="modal fade" id="add-gifts-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xxl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add Gifts</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
			
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

$(document).on('click','.link-add-err',function()
{

	Swal.fire({
		  title: "{{Session::get('msg_title')}}",
		  text: "{{Session::get('msg_swal')}}",
		  icon: "info"
		});
	
});


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
			url:BASE_URL+"/users/view-short-links",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
		    },
        },
				
        columns: [
			{	
				targets: 0,
                data: null,
				className: 'text-center',
				searchable: false,
				orderable: false,
				render: function (data, type, full, meta) {
					return '<input type="checkbox" id="' + data.chkbox + '" class="dt-checkboxes" name="checkboxes" value="' + data.chkbox + '">';
				 },
			},
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "offer" },
			{"data": "link" },
			{"data": "qrcode" },
			{"data": "code" },
			{"data": "email" },
			{"data": "billno" },
			{"data": "branch" },
			{"data": "click_count" },
			{"data": "status" },
			{"data": "action" ,name: 'Action',orderable: false, searchable: false },
        ],

});


$(document).on('click','.link-add',function()
{
	var Result=$("#add-link .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/add-link')}}",
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});
});

$(document).on('click','.link-multiple',function()
{
	var Result=$("#add-multiple-links .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/generate-links')}}",
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});
});



$(document).on('click','.gen-qrcode',function()
{
	var link_id=$(this).attr('id');
	
		jQuery.ajax({
		type: "GET",
		url: "{{url('users/generate-qrcode')}}",
		dataType: 'json',
		data: {link_id: link_id},
		success: function(res)
		{
		   if(res.status==true)
		   {
			   toastr.success(res.msg);
			   $('#datatable').DataTable().ajax.reload(null,false);
		   }
		   else
		   {
			   toastr.success(res.msg);
		   }
		}
	});
});



$('#datatable tbody').on('click','.link-edit',function()
{

	var id=$(this).attr('id');

	var Result=$("#edit-link .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/edit-link')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
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
	  text: "You want to "+opt_text+" this link?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, "+opt_text+" it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		
		  jQuery.ajax({
			type: "get",
			url: BASE_URL+"/users/link-activate-deactivate/"+opt+"/"+id,
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


$("#datatable tbody").on('click','.link-del',function()
{
	var id=$(this).attr('id');
	
	  Swal.fire({
	  title: "Are you sure?",
	  text: "You want to delete this link?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, Delete it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		
		  jQuery.ajax({
			type: "get",
			url: BASE_URL+"/users/delete-link"+"/"+id,
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

function fileValidation()
{
	var fileInput = document.getElementById('class_icon'); 
	 var allowedExtensions="";
	 
		allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
		var filePath = fileInput.value; 
			
		if (!allowedExtensions.exec(filePath)) { 
			alert('Invalid file type, Try again.'); 
			fileInput.value = ''; 
			return false; 
		}
		else
		{
			return true;
		}
}

$("#chkbox-all").click(function()
{
	if($(this).is(':checked'))
	{
		$(table.$('input[name="checkboxes"]').each(function () {
			$(this).prop('checked',true);
		}));
	}
	else
	{
		$(table.$('input[name="checkboxes"]:checked').each(function () {
			$(this).prop('checked',false);
		}));
	}
	
});

$(document).on('click','#delele_multiple',function()
{
	var selectedValues=[];
	$(table.$('input[name="checkboxes"]:checked').each(function () {
        //console.log($(this).val());
		selectedValues.push($(this).val());
    }));
	if(selectedValues.length>0)
	{
		var linkids=selectedValues.toString();

			Swal.fire({
				  title: "Are you sure?",
				  text: "You want to delete selected links?",
				  icon: "question",
				  showCancelButton: true,
				  confirmButtonColor: "#3085d6",
				  cancelButtonColor: "#d33",
				  confirmButtonText: "Yes, Delete it!"
				}).then((result) => {
				  if (result.isConfirmed) {

					  jQuery.ajax({
						type: "POST",
						url: BASE_URL+"/users/delete-multiple-links",
						dataType: 'json',
						data: {_token:"{{csrf_token()}}",link_ids:linkids},
						success: function(res)
						{
						   if(res.status==true)
						   {
							   toastr.success(res.msg);
							   $('#datatable').DataTable().ajax.reload(null, false);
							   $("#chkbox-all").prop('checked',false);
						   }
						   else
						   {
							 toastr.error(res.msg); 
						   }
						}
					  });
				  }
				});

	}
	else
	{
		toastr.error("Please select data from the list!");
	}
		
});

	

</script>
@endpush
@endsection
