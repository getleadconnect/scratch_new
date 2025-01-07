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

.crd-height
{
	height:122px;
}

</style>

		<div class="row mt-3 mb-3">	
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6">

		<div class="page-breadcrumb d-none d-lg-flex align-items-center mb-3">
			<div class="breadcrumb-title pe-3">Campaign Details</div>
		</div>
		</div>
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6 text-right">
				  @php
				  $cid=request()->segment(3);
				  @endphp
                  <a href="{{route('users.campaigns')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>&nbsp;&nbsp;
				  <a href="{{route('users.add-gifts',$cid)}}" class="btn btn-gl-primary"><i class="fa fa-trophy"></i>&nbsp;Add Gifts</a>&nbsp;&nbsp;
		</div>
        </div>

		
		<div class="row">
		<div class="col-lg-5 col-xl-5 col-xxl-5 col-5">
		
		    <div class="col">
                <div class="card radius-10">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="">
						<h4 class="mb-1 mt-2 text-primary">Campaign: {{$offer->vchr_scratch_offers_name}}</h4>
                        <p class="mb-1">Type: {{$offer->type}}</p>
                      </div>
                      <div class="ms-auto fs-2 text-primary">
                        <i class="bi bi-bell"></i>
                      </div>
                    </div>
                    <hr class="my-2">
                    <span class="mb-0"><i class="bi bi-arrow-up"></i> <span class="text-blue " style="font-size:16px;" >Expiring in {{$diff_days}} days</span></span>
                  </div>
                </div>
               </div>

		</div>
		<div class="col-lg-7 col-xl-7 col-xxl-7 col-7">
		
				<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-3">

					<div class="col">
					<div class="card radius-10 crd-height">
					  <div class="card-body">
						<div class="d-flex align-items-center">
						  <div class="">
							<h5 class="mb-1 mt-3 p-2">Total Gift</h5>
							<h4 class="mb-0 text-pink p-2">{{$counts['total_count']}}</h4>
						  </div>
						  <div class="ms-auto fs-2 text-pink">
							<i class="bx bx-trophy"></i>
						  </div>
						</div>
					  </div>
					</div>
				   </div>
				  
				 <div class="col">
					<div class="card radius-10 crd-height">
					  <div class="card-body">
						<div class="d-flex align-items-center">
						  <div class="">
							<h6 class="mb-1 mt-3 p-2">Used Gifts</h6>
							<h4 class="mb-0 text-info p-2">{{$counts['used_count']}}</h4>
						  </div>
						  <div class="ms-auto fs-2 text-info">
							<i class="bx bx-trophy"></i>
						  </div>
						</div>
					  </div>
					</div>
				   </div>
				   
				   <div class="col">
						<div class="card radius-10 crd-height" >
						  <div class="card-body">
							<div class="d-flex align-items-center">
							  <div class="">
								<h6 class="mb-1 mt-3 p-2">Gift Balance</h6>
								<h4 class="mb-0 text-purple p-2">{{$counts['bal_count']}}</h4>
							  </div>
							  <div class="ms-auto fs-2 text-purple">
								<i class="bx bx-trophy"></i>
							  </div>
							</div>
						  </div>
						</div>
					   </div>

				</div><!--end row-->
			</div>
		</div>
		
		<div class="card">
                <div class="card-header p-y-3">
				<div class="row">
					<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
					  <h5 ><i class="fa fa-users"></i> Customers List</h5>
					</div>
					
					<div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">

					</div>
				  
				</div>
                </div>
                <div class="card-body">
				
				<input type="hidden" id="campaign_id" value="{{$cid}}">
				
                   <div class="row mt-2">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
					  
							<div class="table-responsive">
								<table class="table" id="datatable" style="width:100% !important;">
									<thead class="thead-light">
									  <tr>
										<th>SlNo</th>
										<th>Name</th>
										<th>Mobile</th>
										<th>Email</th>
										<th>Created At</th>
										<th>Branch</th>
										<th>Offer</th>
										<th>Status</th>
										<th>Redeem</th>
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

		'pagingType':"simple_numbers",
        'lengthChange': true,

		ajax:
		{
			url:BASE_URL+"/users/view-campaign-customers",
			data: function (data) 
		    {
               data.campaign_id = $('#campaign_id').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "name" },
			{"data": "mobile" },
			{"data": "email" },
			{"data": "created" },
			{"data": "branch" },
			{"data": "offer" },
			{"data": "status" },
			{"data": "redeem" },
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



</script>
@endpush
@endsection
