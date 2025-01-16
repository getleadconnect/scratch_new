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
              <div class="breadcrumb-title pe-3">Campaigns</div>
 
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
                  <h6 class="mb-0 pt5">&nbsp;</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				  @if($subscription)
				  {{--<a href="{{url('users/add-campaign')}}"  class="btn btn-primary btn-xs" ><i class="fa fa-plus"></i>&nbsp;Add Campaign</a>--}}
				  <a href="javascript:;" id="btn-add-campaign" class="btn btn-primary btn-xs" data-bs-toggle="offcanvas" data-bs-target="#add-campaign" ><i class="fa fa-plus"></i>&nbsp;Add Campaign</a>
				  @else
					 <a href="javascript:;" id="btn-add-camp" class="btn btn-primary btn-xs" ><i class="fa fa-plus"></i>&nbsp;Add Campaign</a>
				  @endif
				  </div>

				  </div>
                </div>
				
                <div class="card-body">
					<div class="accordion-item accordion-item-bm" >
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" >
                          <div class="accordion-body">
						   <div class="row" style="padding:3px 10px 0px 10px;" >
							<div class="col-3 col-lg-3">
								<label>Center</label>
								<select class="form-control mb-3" id="flt_center" placeholder="center" required>
								<option value="">select</option>

								</select>
							</div>
							
							<div class="col-3 col-lg-3">
								<label>District</label>
								<select class="form-control mb-3" id="flt_district" placeholder="district" required>
								<option value="">select</option>

								</select>
							</div>
						   </div>
						</div>
					  </div>
					  
					</div>
				
                   <div class="row mt-2">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
                        <!--<div class="card-body">-->
                          <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="table-semi-dark">
                                 <tr>
									<th>SlNo</th>
									<th>Created At</th>
									<th>Campaign Name</th>
									<th>Type</th>
									<th>Banner</th>
									<th>Expiry</th>
									<th>Status</th>
									<th style="width:50px;">Gifts</th>
									<th class="no-content" style="width:50px;">Action</th>
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
			  
			  
	<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="add-campaign" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Add Campaign</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
			<div class="offcanvas-body">
  
  
  
  
            </div>
    </div>
			
	<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="edit-campaign" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Edit Campaign</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
			<div class="offcanvas-body">
  
  
  
  
            </div>
    </div>
	
	
	
	
	<div class="gift-modal shadow hide" id="campaign-gift">
		<div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				  <h5 >Add Gifts</h5>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
					<button type="button" id="btn-add-gift-close" class="btn btn-danger btn-sm" title="close"><i class="fa fa-times fs-6" ></i></button>
				  </div>
				  
				  </div>
                </div>
				<div class="gift-modal-body">
				
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

@if (Session::get('msg_swal'))
	<script>
		toastr.error("{{Session::get('msg_swal')}}");
	</script>
@endif

<script>

$(document).on('click','#btn-add-camp',function()
{
	Swal.fire({
		  title: "{{Session::get('msg_title')}}",
		  text: "{{Session::get('msg_swal')}}",
		  icon: "info"
		});
});


BASE_URL ={!! json_encode(url('/')) !!}

/*
var mes=$('#view_message').val().split('#');

if(mes[0]=="success")
{	
	toastr.success(mes[1]);
}
else if(mes[0]=="danger")
{
	toastr.error(mes[1]);
}
*/
//---------------------------------------------------------------------------


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
			url:BASE_URL+"/users/view-offers",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "created" },
			{"data": "name" },
			{"data": "type" },
			{"data": "offer_image" },
			{"data": "enddate" },
			{"data": "status" },
			{"data": "add-gift" },
			{"data": "action" ,name: 'Action',orderable: false, searchable: false },
        ],

});

$('#datatable tbody').on('click','.offer-delete',function()
{
	Swal.fire({
	  //title: "Are you sure?",
	  text: "Are you sure, You want to delete this offer?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, Delete it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		var tid=$(this).attr('id');
		
		  $.ajax({
          url: "{{url('users/delete-campaign')}}"+'/'+tid,
          type: 'get',
		  dataType: 'json',
          //data:{'track_id':tid},
          success: function (res) 
		  {
			if(res.status==1)
			{
				 toastr.success(res.msg);
				 table.draw();
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
 

$('#datatable tbody').on('click','.offer-edit',function()
{

	var id=$(this).attr('id');
	var Result=$("#edit-campaign .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/edit-campaign')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});

});


$(document).on('click','#btn-add-campaign',function()
{

	var id=$(this).attr('id');
	var Result=$("#add-campaign .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/add-campaign')}}",
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
	  text: "You want to "+opt_text+" this campaign?",
	  icon: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, "+opt_text+" it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		
		  jQuery.ajax({
			type: "get",
			url: BASE_URL+"/users/offer-activate-deactivate/"+opt+"/"+id,
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


// add gifts --------------------------------------------------------------------------------------


</script>
@endpush
@endsection
