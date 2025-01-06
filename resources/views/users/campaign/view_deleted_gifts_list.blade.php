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
              <div class="breadcrumb-title pe-3">Deleted Gifts List</div>
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
								<label>Campaigns</label>
								<select id="offer_id" name="offer_id" class="form-control" >
                                 <option value="">--Select--</option>
								 @foreach($offers as $row)
								 <option value="{{$row->pk_int_scratch_offers_id}}">{{$row->vchr_scratch_offers_name}}</option>
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
										<th>Campaign Name</th>
										<th>Image</th>
										<th>Count</th>
										<th>Stage</th>
										<th>Description</th>
										<th>Balance</th>
										<th>Status</th>
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
			url:BASE_URL+"/users/view-deleted-gifts-listings",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
			   data.offer_id = $('#offer_id').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "campaign" },
			{"data": "image" },
			{"data": "int_scratch_offers_count" },
			{"data": "type_name" },
			{"data": "txt_description" },
			{"data": "int_scratch_offers_balance" },
			{"data": "status" },
        ],
			
    });

$("#btn-filter").click(function()
{
	table.draw();
});


$(document).on('change','#user_id',function()
{

	var id=$(this).val();
	
		jQuery.ajax({
			type: "GET",
			url: "{{url('admin/get-user-offers')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   $("#offer_id").html(res);
			}
		});

});




</script>
@endpush
@endsection
