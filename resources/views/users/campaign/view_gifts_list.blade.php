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
                  <h6 class="mb-0 pt5"><i class="fa fa-file-alt"></i> Gifts List</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				  <button class="btn btn-secondary btn-xs btn-sm btn-p-filter" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" style="padding-left:3px;" title="Filter"  aria-expanded="true" aria-controls="flush-collapseOne">
                      &nbsp;<i class="lni lni-funnel"></i>
                  </button>&nbsp;
				  </div>

				  </div>
                </div>
                <div class="card-body">
				
				<div class="accordion-item accordion-item-bm mb-2" >
                        <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" >
                          <div class="accordion-body">
						  <label style="font-weight:500;padding:5px 10px;" > Filter By: </label>
						   <div class="row" style="padding:3px 10px 5px 10px;" >

							<div class="col-3 col-lg-3">
								<label>Campaign</label>
								<select id="offer_id" name="offer_id" class="form-control" >
                                 <option value="">--Select--</option>
                                @foreach($offers as $row)
                                 <option value="{{ $row->pk_int_scratch_offers_id }}">{{ $row->vchr_scratch_offer_name }}</option>
                                 @endforeach
                              </select>
							</div>
							
							<div class="col-3 col-lg-3">
								<label style="width:100%;">&nbsp;</label>
								<a href="javascript:;" class="btn btn-secondary btn-xs" id="btn-filter" > <i class="lni lni-funnel"></i> Filter</a>
							</div>
						   </div>
						   
						</div>
					  </div>
					</div>

                   <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
					  
							<div class="table-responsive">
                                    <table class="table" id="datatable" style="width:100% !important;">
                                        <thead class="thead-light">
                                          <tr>
											<th>SlNo</th>
											<th>User</th>
											<th>Campaign</th>
                                            <th>Image</th>
                                            <th>Stage</th>
                                            <th>Description</th>
											<th>Count</th>
											<th>Win/Loss</th>
                                            <th>Status</th>
											<th style="width:50px;">Action</th>
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
			  
	<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="edit-gift" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Edit gift</h5>
            <button type="button" class="btn-close text-reset btn-reset" data-bs-dismiss="offcanvas"></button>
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
		paging : true,
        pageLength :50,
		scrollX: true,

		'pagingType':"simple_numbers",
        'lengthChange': true,

		ajax:
		{
			url:BASE_URL+"/users/view-gifts-list",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
			   data.user_id = $('#user_id').val();
			   data.offer_id = $('offer_id').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "username" },
			{"data":"campaign"},
			{"data": "image" },
			{"data": "type_name" },
			{"data": "txt_description" },
			{"data": "gift_count" },
			{"data": "win_status" },
			{"data": "status" },
			{"data": "action" },
        ],
			
    });
	
	
$("#btn-filter").click(function()
{
	table.draw();
});


$('#datatable tbody').on('click','.edit-gift',function()
{

	var id=$(this).attr('id');
	var Result=$("#edit-gift .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/edit-gift')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});

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
				 $('#datatable').DataTable().ajax.reload(null,false);
				 
				 var sbal=parseInt($("#scratch_balance").val());
				 var cnt=sbal+res.offer_count;
				 $("#scratch_balance").val(cnt);
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
 
 $("#datatable tbody").on('click','.act-deact-gift',function()
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
			url: BASE_URL+"/users/gift-activate-deactivate/"+opt+"/"+id,
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
