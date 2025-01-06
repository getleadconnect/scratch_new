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


<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">Campaigns Gifts List</div>
             </div>
            <!--end breadcrumb-->

              <div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				  <h5 >{{$offer->vchr_scratch_offers_name}}</h5>
				  <h6 class="card-title">Type: {{$offer->type}}</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				  @php
				  $cid=request()->segment(3);
				  @endphp
                  <a href="{{route('users.campaigns')}}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>&nbsp;&nbsp;
				  
				  <!--<button type="button" id="{{$cid}}" data-bs-toggle="offcanvas" data-bs-target="#add-campaign-gifts" class="btn btn-gl-primary btn-sm add-gifts"><i class="fa fa-trophy fs-7"></i>&nbsp;Add Gifts</button>-->
				  
				  <button type="button" id="{{$cid}}" class="btn btn-gl-primary btn-sm add-gifts"><i class="fa fa-trophy fs-7"></i>&nbsp;Add Gifts</button>
				  </div>
				  
				  </div>
                </div>
                <div class="card-body">

                   <div class="row mt-2">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
					  
							<div class="table-responsive">
								<table class="table" id="datatable" style="width:100% !important;">
									<thead class="thead-light">
									  <tr>
										<th>SlNo</th>
										<th>Image</th>
										<th>Count</th>
										<th>Stage</th>
										<th>Description</th>
										<th>Balance</th>
										<th>Status</th>
										<th>Action</th>
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
			  
	<form id='foto' method="post" action="{{url('users/update-image')}}" method="POST" enctype="multipart/form-data" >
	@csrf
	    <div style="height:0px;overflow:hidden"> 
        <input type="file" id="picField" name="picField" onchange="this.form.submit()" class="d-none"/> 
        <input type="hidden" id="scrId" name="scrId" /> 
        </div>
      
        <!--<i class='fa fa-camera' onclick="fileInput.click();"></i>-->
    </form> 
			  
			
	<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="edit-campaign" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Edit Campaign</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
			<div class="offcanvas-body">
  
            </div>
    </div>


<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="add-campaign-gifts" style="width:50% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Add Gifts</h5>
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

<script>

   $(document).on('click','#imgUpload',function(e){
        $('#picField').trigger('click');
        scrId = $(this).data('id')
        $('#scrId').val(scrId);
    })

BASE_URL ={!! json_encode(url('/')) !!}

 var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging : true,
        pageLength :50,
		scrollX: true,
		/*lengthChange: true,
		lengthMenu: [
                    [10, 25, 50, 100,200,300,500,1000,2000],
                    [10, 25, 50, 100,200,300,500,1000,2000],
                ],
		*/
		
		'pagingType':"simple_numbers",
        'lengthChange': true,

		ajax:
		{
			url:BASE_URL+"/users/view-campaign-gifts-listings",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "image" },
			{"data": "int_scratch_offers_count" },
			{"data": "type_name" },
			{"data": "txt_description" },
			{"data": "int_scratch_offers_balance" },
			{"data": "status" },
			{"data": "action" ,name: 'Action',orderable: false, searchable: false },
        ],
			
    });


 $('#datatable tbody').on('click','.delete-gift',function()
{
	Swal.fire({
	  //title: "Are you sure?",
	  text: "Are you sure, You want to delete this gift details?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, Delete it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		var tid=$(this).attr('id');
		
		  $.ajax({
          url: "{{url('users/delete-gift')}}"+'/'+tid,
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
 
$('#datatable1 tbody').on('click','.offer-edit',function()
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

/*$(document).on('click','.add-gifts',function()
{

	var id=$(this).attr('id');
	var Result=$("#add-campaign-gifts .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/add-gifts')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});

});*/

$(document).on('click','#btn-add-gift-close',function()
{
	$("#campaign-gift").removeClass('show');
	$("#campaign-gift").addClass('hide');
	$("#campaign-gift .prd-body").html('');
});

$(document).on('click','.add-gifts',function()
{

	var id=$(this).attr('id');
	
	$("#campaign-gift").removeClass('hide');
	$("#campaign-gift").addClass('show');
	
	var Result=$("#campaign-gift .gift-modal-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/add-gifts')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});

});


</script>
@endpush
@endsection
