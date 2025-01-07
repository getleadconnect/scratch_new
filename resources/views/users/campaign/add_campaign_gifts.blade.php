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

		<div class="row mt-3 mb-3">	
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6">

		<div class="page-breadcrumb d-none d-lg-flex align-items-center mb-3">
			<div class="breadcrumb-title pe-3">Add Gifts</div>
		</div>
		</div>
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6 text-right">
				  @php
				  $cid=request()->segment(3);
				  @endphp
                  <a href="{{url()->previous()}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>&nbsp;&nbsp;
				  <a href="{{route('users.campaigns')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>&nbsp;View Campaigns</a>&nbsp;&nbsp;
		</div>
        </div>

			<!--<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">Campaigns Gifts List</div>
             </div> -->
            <!--end breadcrumb-->

              <div class="card">
                <div class="card-header p-y-3">
				<div class="row" >
				<div class="col-lg-6 col-xl-6 col-xxl-6">
					<h6 class="mb-2" style="color:#623737;">Campaign:&nbsp;<span id="add_gift_campaign_name">{{$sof->vchr_scratch_offers_name}}&nbsp;</span></h6>
					<h6 class="mb-2" style="color:#623737;">Type:&nbsp;<span id="add_gift_type_name">{{$sof->type}}</span></h6>
					</div>
					<div class="col-lg-6 col-xl-6 col-xxl-6 text-end">
					<h6 >Scratch Balance: <label class="text-blue">
					<input type="text" name="scratch_balance" id="scratch_balance" style="width:80px;border:none;text-align:right;" readonly value="{{$sbal_count}}">
					/<span>{{$sbal_count}}</span></label> </h6>
				</div>
				</div>
                </div>
                <div class="card-body">
				
				
			<div class="row mt-3">
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
				<h6 class="mb-2">Add Gift :</h6>
									
				<input type="hidden" id="initial_tr_value" name="initial_tr_value" value="False">
				<form id="formAddGifts"  enctype="multipart/form-data">
				@csrf
				
				
				<input type="hidden" name="campaign_id" id="campaign_id" value="{{$sof->pk_int_scratch_offers_id}}">
				<input type="hidden" name="offer_type_id" id="offer_type_id" value="{{$sof->type_id}}">
			
				<div class="table-responsive">
						<table id="giftTable" class="table mb-0">
							<thead class="thead-light">
							<tr>
								<th>Gift_Count</th>
								<th>Description</th>
								<th>Image</th>
								<th></th>
								<th>Status</th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody>
						
							<tr>
								<td class="td-count"><input type="text" pattern="[0-9]*" class="form-control offer_count" name="offers_count" required  onkeypress="return /[0-9]/i.test(event.key)"/></td>
								<td class="td-desc"><textarea class="form-control" name="description" required></textarea></td>
								<td><input class="form-control gift-image-file" type="file" name="image_list" required>	</td>
								<td><img class="gift-image" src="{{url('assets/images/no-image.png')}}" style="width:70px;height:50px;"></td>
								<td>
									<select name="winning_status" id="winning_status" class="form-control wstatus" required>
									<option value="">--select--</option>
									<option value="1">Win</option>
									<option value="0">Loss</option>
								</td>
								<td>
								
								<button class="btn btn-primary btn-xs " id="btn_save_gift"  type="submit" >Add Gift</button>
								&nbsp;</td>
							</tr>
																
							</tbody>
						</table><!--end /table-->
					</div>
					
					</form>

				</div>
				</div>

                   <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
					  
					  <h6 class="mb-2 mt-2">Gifts List :</h6>
					  
							<div class="table-responsive mt-2">
								<table class="table" id="datatable" style="width:100% !important;">
									<thead class="table-semi-dark">
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
		
		'pagingType':"simple_numbers",
        'lengthChange': true,

		ajax:
		{
			url:BASE_URL+"/users/view-campaign-gifts",
			data: function (data) 
		    {
               data.campaign_id= $('#campaign_id').val();
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


var addValidator=$('#formAddGifts').validate({ 
	
	rules: {
		offer_count: {required: true},
		description: {required: true},
		image_list:{requirded:true},
		winning_status: {required: true}
	},
});


$('#formAddGifts').submit(function(e) 
	{
		e.preventDefault();

		var formData = new FormData(this);

		$.ajax({
		url: "{{ url('users/save-gift')}}",
		method: 'post',
		data: formData,
		contentType: false,
		processData: false,
		success: function(result){
			if(result.status == 1)
			{
				//$('#datatable').DataTable().ajax.reload(null,false);
				toastr.success(result.msg);
			}
			else
			{
				toastr.error(result.msg);
			}
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
 

$("#giftTable tbody").on('change','.gift-image-file',function()
{
	var img=$(this).closest('tr').find('img.gift-image');
	
	var file=this.files[0];
		var allowedExtensions="";
	    allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
	    var filePath = file.name;
		console.log(file);
	
		if (!allowedExtensions.exec(filePath)) { 
			alert('Invalid file type, Try again.'); 
			$(this).val('');
			img.attr('src','');
		}
		else
		{
			if (file) {
				var reader = new FileReader();
					reader.onload = function (e) {
						img.attr('src',e.target.result);
					}
					reader.readAsDataURL(file);
			  }
		}  
});
</script>
@endpush
@endsection
