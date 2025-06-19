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
					 <button type="button" class="btn btn-primary btn-xs link-add" data-bs-toggle="offcanvas" data-bs-target="#add-link"><i class="fa fa-plus"></i>&nbsp;Add Link</button>
					 &nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs link-multiple" data-bs-toggle="offcanvas" data-bs-target="#add-multiple-links"><i class="fa fa-plus"></i>&nbsp;Add Multiple Links</button>
				     &nbsp;&nbsp;<button class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#gen-pdf-modal" ><i class="fa fa-qrcode"></i>&nbsp;Qr-Code PDF</button>
				  </div>

				  </div>
                </div>
                <div class="card-body">
								
				<div class="accordion-item accordion-item-bm" >
                        <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" >
                         <div class="accordion-body">
						  <label style="font-weight:500;padding:5px 10px;" > Filter By: </label>
						  
						  
						  <form method="POST" action="{{url('users/gl-links')}}" enctype="multipart/form-data" >
						  @csrf 
						  
						   <div class="row" style="padding:3px 10px 10px 10px;" >

							<div class="col-3 col-lg-3 col-xl-3 col-xxl-3">
								<label>Campaign</label>
								<select id="flt_offer_id" name="flt_offer_id" class="form-control" >
                                 <option value="">Select Campaign</option>
                                 @foreach($offers as $rw)
                                 <option value="{{ $rw->pk_int_scratch_offers_id }}">{{ $rw->vchr_scratch_offers_name }}</option>
                                 @endforeach
                              </select>
							</div>
							
							<div class="col-2 col-lg-2 col-xl-2 col-xxl-2">
								<label>Link Section</label>
								<select id="flt_link_section_id" name="flt_link_section_id" class="form-control" >
                                 <option value="">--select--</option>
								</select>
							</div>
							
							<div class="col-3 col-lg-3 col-xl-3 col-xxl-3" style="padding-top:18px;">
							<button type="submit" class="btn btn-primary btn-xs" style="margin-top:5px;"> <i class="lni lni-funnel"></i> Filter</button>&nbsp;&nbsp;
							</div>
							
							<div class="col-4 col-lg-4 col-xl-4 col-xxl-4 text-right" style="padding-top:18px;">
							<button type="submit" class="btn btn-secondary btn-xs" style="margin-top:5px;"> Clear </button>&nbsp;&nbsp;
							</div>

						   </div>
						   
						   </form>
						   
						</div>
					  </div>
				</div>
			
					<form method="POST" action="{{url('users/gl-links')}}" enctype="multipart/form-data" >
					@csrf 
					   <div class="row mt-3">
						<div class="col-12 col-lg-12 col-xl-12 col-xxl-12 d-flex">
						 
							<div style="margin-left:auto;width:250px !important;">
							<div class="input-group mb-3">
							  <input type="text" class="form-control"  style="width:200px !important;" placeholder="Search" name="search" >
							  <div class="input-group-append">
								<button type="submit" class="btn btn-outline-secondary" style="height:36px;border-color:#e4e4e4;" type="button"><i class="fa fa-search"></i></button>
							  </div>
							</div>
							</div>

					   </div>
					   </div>
				   </form>
				  
				  
				   <div class="row ">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
                        <!--<div class="card-body">-->
                          <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark">
                                <tr>
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
							   
							   @foreach($links as $key=>$row)
							   <tr>
									<td>{{++$key}}</td>
									<td>{{$row->offer_id}}</td>
									<td>{{$row->link}}</td>
									<td>
									
									@if($row->link_type!="Multiple")
										<a href="{{\App\Facades\FileUpload::viewFile($row->qrcode_file,'local')}}" target="_blank" title="View"><i class="fa fa-qrcode qrcode-icon" style="font-size:28px;color:#6c757d;padding:2px;"></i></a>
										&nbsp;<a href="{{\App\Facades\FileUpload::viewFile($row->qrcode_file,'local')}}" download title="Download" ><i class="fa fa-download" style="font-size:18px;" ></i></a>	
									@else
										<a href="{{\App\Facades\FileUpload::viewFile($row->qrcode_file,'local')}}" target="_blank"><i class="fa fa-qrcode qrcode-icon" style="font-size:28px;color:#6c757d;padding:2px;"></i></a>	
									@endif

									</td>
									
									<td>{{$row->code}}</td>
									<td>
									
									@if($row->email_required==1) Yes @else No @endif
									</td>
									<td>
									@if($row->custom_field==1) Yes @else No @endif
									<td>
									@if($row->branch_required==1) Yes @else No @endif
									</td>
									<td>{{$row->click_count}}</td>
									 <td>
									    @if($row->status==1) 
											<span class="badge rounded-pill bg-success">Active</span>
										@else
											<span class="badge rounded-pill bg-danger">Inactive</span>
										@endif				 
									 </td>
									<td class="no-content" style="width:50px;">
										<div class="fs-5 ms-auto dropdown">
											<div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="fadeIn animated bx bx-dots-vertical"></i></div>
											<ul class="dropdown-menu">
											<li><a class="dropdown-item link-edit" href="javascript:;" id="{{$row->id}}" data-bs-toggle="offcanvas" data-bs-target="#edit-link" aria-controls="offcanvasScrolling" ><i class="lni lni-pencil-alt"></i> Edit</a></li>
											<li><a class="dropdown-item link-del" href="javascript:;" id="{{$row->id}}" ><i class="lni lni-trash"></i> Delete</a></li>
											<li><a class="dropdown-item link-view" href="{{route('users.web-click-link-history',$row->id)}}"><i class="lni lni-eye"></i> View</a></li>
											<li><a class="dropdown-item gen-qrcode" href="javascript:;" id="{{$row->id}}"><i class="fa fa-qrcode"></i> Generate QrCode</a></li>
							
											@if ($row->status == \App\Models\ShortLink::ACTIVE) 
											<li><a class="dropdown-item btn-act-deact" href="javascript:;" id="{{$row->id}}" data-option="2" ><i class="lni lni-close"></i> Deactivate</a></li>
											@else
											<li><a class="dropdown-item btn-act-deact" href="javascript:;" id="{{$row->id}}" data-option="1"><i class="lni lni-checkmark"></i> Activate</a></li>
											@endif
										
											<ul>
										</div>
									</td>
								</tr>
							  @endforeach
                               </tbody>
                             </table>
                          </div>

                       <!-- </div>-->
					   
					   <div class="row mt-3">
					   <div class="col-lg-3 col-xl-3 col-xxl-3"></div>
					   <div class="col-lg-6 col-xl-6 col-xxl-6">
							{{$links->links('pagination::bootstrap-4')}}
					   </div>
					   <div class="col-lg-3 col-xl-3 col-xxl-3"></div>
					   </div>
					   
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
				
				<ul style="font-size: 13px !important;color: #2d2de9;">
				<li> This is used for generate qr-code pdf file for <span style='font-weight:500;'>Multiple links</span></li>
				<li> Create multiple links one by one. Click '<span style='font-weight:500;'>Add Multiple Links</span>' button</li>
				<li> Then use this Qr-code generator option.</li>
				</ul>
				
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
				
				
				<div class="mb-2 row">
					<div class="col-lg-12 col-xl-12 col-xxl-12">
					<label for="example-text-input" class="col-form-label">Select link section to pdf</label>
					<select class="form-select" name="link_section_id" id="link_section_id" required>
						<option value="" >--select--</option>
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

$(document).on('change','#offer_id',function()
{
	var offer_id=$(this).val();

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/get-link-count-section')}}"+"/"+offer_id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(data)
			{
			   $("#link_section_id").html(data);
			}
		});
});

$(document).on('change','#flt_offer_id',function()
{
	var offer_id=$(this).val();
			jQuery.ajax({
			type: "GET",
			url: "{{url('users/get-link-count-section')}}"+"/"+offer_id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(data)
			{
			   $("#flt_link_section_id").html(data);
			}
		});
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
			   window.location.reload();
			   //$('#datatable').DataTable().ajax.reload(null,false);
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
				   window.location.reload();
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
				    window.location.reload();
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
	

</script>
@endpush
@endsection
